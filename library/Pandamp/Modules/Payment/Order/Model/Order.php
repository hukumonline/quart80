<?php
class Pandamp_Modules_Payment_Order_Model_Order extends Zend_Db_Table_Abstract
{
	protected $_name = 'KutuOrder';
    function countOrders($query, $userId)
    {
    	$db = $this->_db->query
    	("Select count(KO.orderId) AS count From KutuOrder as KO, KutuOrderDetail AS KOD
    	where KOD.orderID =KO.orderID AND KO.userId=$userId $query");
    	
    	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);
    	
    	return ($dataFetch[0]['count']);
    }	
    public function getOrderSummary($query, $where,$limit,$offset){
        $db = $this->_db->query("SELECT KO.*,KOS.ordersStatus,
                                COUNT(itemId) AS countTotal,KU.* 
                                FROM
                                ((KutuOrder AS KO 
                                LEFT JOIN KutuOrderDetail AS KOD 
                                    ON KOD.orderId=KO.orderId)
                                LEFT JOIN KutuUser AS KU 
                                    ON KU.guid = KO.userId)
                                LEFT JOIN KutuOrderStatus AS KOS 
                                    ON KOS.orderStatusId = KO.orderStatus
                                WHERE KO.userId = $where
								$query
                                GROUP BY(KO.orderId) DESC
                                LIMIT $offset, $limit");
								
    	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);
    	
        $data  = array(
            'table'    => $this,
            'data'     => $dataFetch,
            'rowClass' => $this->_rowClass,
            'stored'   => true
        );

        Zend_Loader::loadClass($this->_rowsetClass);
        return new $this->_rowsetClass($data);
	}
	public function getOrderDetail($orderId){
		$db = $this->_db->query("SELECT KO.*, KOD.*
										FROM KutuOrder AS KO
										JOIN KutuOrderDetail AS KOD
										ON KOD.orderId = KO.orderId
										WHERE KO.orderId = $orderId");
		
    	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);        
		    	
        $data  = array(
            'table'    => $this,
            'data'     => $dataFetch,
            'rowClass' => $this->_rowClass,
            'stored'   => true
        );
        
        Zend_Loader::loadClass($this->_rowsetClass);
        return new $this->_rowsetClass($data);
	}
	public function getDocumentSummary($userId, $where, $limit, $offset){
        $db = $this->_db->query("SELECT KOD.*, KO.datePurchased AS purchasingDate
                                FROM
                                KutuOrderDetail AS KOD,
								KutuOrder AS KO 
                                WHERE 
									KO.orderId = KOD.orderId
								AND
									userId = '$userId'
								AND
									(KO.orderStatus = 3 
									OR
									KO.orderStatus = 5)
								AND 
									documentName LIKE '%$where%'
                                LIMIT $offset, $limit");
        //$db = $this->_db->query();
    	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);
    	
        $data  = array(
            'table'    => $this,
            'data'     => $dataFetch,
            'rowClass' => $this->_rowClass,
            'stored'   => true
        );

        Zend_Loader::loadClass($this->_rowsetClass);
        return new $this->_rowsetClass($data);
	}    
	function countDocument($userId, $where)
    {
    	$db = $this->_db->query("SELECT count(itemId) as totalDoc
                                FROM
									KutuOrderDetail AS KOD,
									KutuOrder AS KO 
                                WHERE 
									KO.orderId = KOD.orderId
								AND
									userId = '$userId'
								AND
									(KO.orderStatus = 3 
									OR
									KO.orderStatus = 5)
								AND 
									documentName LIKE '%$where%'");
    	
    	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);
    	
    	return ($dataFetch[0]['totalDoc']);
    }
    public function getTransactionToConfirm($userId /*, $limit, $offset*/){
        $db = $this->_db->query("SELECT 
                                    KO.*,KOS.ordersStatus,
                                    COUNT(itemId) AS countTotal,KU.guid 
                                FROM
                                    ((KutuOrder AS KO 
                                LEFT JOIN KutuOrderDetail AS KOD 
                                    ON KOD.orderId = KO.orderId)
                                LEFT JOIN KutuUser AS KU 
                                    ON KU.guid = KO.userId)
                                LEFT JOIN KutuOrderStatus AS KOS 
                                    ON KOS.orderStatusId = KO.orderStatus
                                WHERE 
                                    KO.userId = '$userId' 
                                AND 
                                    (paymentMethod = 'bank' 
                                AND
                                    (
                                    orderStatus = 5 
									OR orderStatus = 1  
									OR orderStatus = 4
									OR orderStatus = 6
                                    ))
                                GROUP BY(KO.orderId) ASC");
                                /*LIMIT 
                                    $offset,$limit");*/
        //$db = $this->_db->query();
    	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);        
		    	
        $data  = array(
            'table'    => $this,
            'data'     => $dataFetch,
            'rowClass' => $this->_rowClass,
            'stored'   => true
        );

        Zend_Loader::loadClass($this->_rowsetClass);
        return new $this->_rowsetClass($data);
    }
    public function getTransactionToConfirmCount($userId){
        $db = $this->_db->query("SELECT 
                                    COUNT(orderId) AS countConfirm
                                FROM
                                    KutuOrder 
                                WHERE 
                                    userId = '$userId' 
                                AND 
                                    (
                                    paymentMethod = 'bank'
                                AND
                                    (
                                    orderStatus = 5 
									OR orderStatus = 1 
									OR orderStatus = 4 
									OR orderStatus = 6 
                                    ))");
    	
    	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);
    	
    	return ($dataFetch[0]['countConfirm']);
    }
    function outstandingUserAmout($userId)
    {
    	$db = $this->_db->query
    	("SELECT SUM(orderTotal) AS total FROM KutuOrder where userId = '$userId' AND  orderStatus=5");
    	
    	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);
    	
    	return ($dataFetch[0]['total']);
    }
	public function getOrderAndStatus($orderId){
        $db = $this->_db->query("SELECT 
                            KO.*, KOS.* 
                            FROM
                                KutuOrder AS KO,
                                KutuOrderStatus AS KOS
                            WHERE 
                                orderStatus =orderStatusId
                            AND 
								orderId = $orderId");
        //$db = $this->_db->query();
    	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);        
		    	
        $data  = array(
            'table'    => $this,
            'data'     => $dataFetch,
            'rowClass' => $this->_rowClass,
            'stored'   => true
        );

        Zend_Loader::loadClass($this->_rowsetClass);
        return new $this->_rowsetClass($data);
	}
    
}