<?php
class Pandamp_Modules_Identity_User_Model_User extends Zend_Db_Table_Abstract
	implements Pandamp_BeanContext_Decoratable
{
	protected $_name = 'KutuUser';
	protected $_rowClass = 'Pandamp_Modules_Identity_User_Model_Row_User';
	protected $_dependentTables = array('Pandamp_Modules_Identity_User_Model_UserDetail');
	protected $_referenceMap = array(
		'UserDetail' => array(
			'columns'		=> 'uid',
			'refTableClass'	=> 'Pandamp_Modules_Identity_User_Model_UserDetail',
			'refColumns'	=> 'id'
		)
	);
	protected $_schema = 'hid';
    protected function  _setupDatabaseAdapter()
    {
        $this->_db = Zend_Registry::get('db2');

        parent::_setupDatabaseAdapter();
    }
	
    /**
     * Adds an user Twitter account for the specified user with the specified data.
     *
     * @param integer $userId The userid this account will belong to
     * @param array $data an assoc array with keys as fields and values as data
     * to add
     *
     * @return integer 0 if the data wasn't added, otherwise the id of the newly added
     * row
     */
    public function addUserTwitter(Array $data)
    {
        $whiteList = array(
            'username',
            'name',
            'password',
        	'tw_uid',
        	'registered_on'
        );

        $addData = array();

        foreach ($data as $key => $value) {
            if (in_array($key, $whiteList)) {
                $addData[$key] = $value;
            }
        }

        if (empty($addData)) {
            return 0;
        }

        $id = $this->insert($addData);

        if ((int)$id == 0) {
            return $id;
        }

        return $id;
    }

	/**
     * Returns the number of users that share the same username,
     * in case the index on the username field is missing.
     *
     * @param string $userName
     *
     * @return integer
     */
    public function getUserNameCount($userName)
    {
        if ($userName == "") {
            return 0;
        }

        $select = $this->select()
                  ->from($this, array(
                    'COUNT(id) as count_id'
                  ))
                  ->where('username = ?', $userName);

        $row = $this->fetchRow($select);

        return ($row !== null) ? $row->count_id : 0;
    }
    
    /**
     * Returns the number of email users that share the same email,
     * in case the index on the email user field is missing.
     *
     * @param string $email
     *
     * @return integer
     */
    public function getEmailCount($email)
    {
        if ($email == "") {
            return 0;
        }

        $select = $this->select()
                  ->from($this, array(
                    'COUNT(id) as count_id'
                  ))
                  ->where('email = ?', $email);

        $row = $this->fetchRow($select);

        return ($row !== null) ? $row->count_id : 0;
    }

    /**
     * Returs the row that matches both the username and the password.
     *
     * @param string $userName
     * @param string $password
     *
     * @return Zend_Db_Table_Row
     */
    public function getUserForUserNameCredentials($userName, $password)
    {
        if ($userName == "" || $password == "") {
            return null;
        }

        /**
         * @todo make sure username and password is a unique index
         */
        $select = $this->select()
                       ->from($this)
                       ->where('username = ?', $userName)
                       ->where('password=?', $password);
                       
    	// $select = $select->__toString();
    	// print_r($select);exit();
                       
        $row = $this->fetchRow($select);

        return $row;
    }

    /**
     * Looks up the row with the facebook key $fb_uid, fetches the data and
     * creates a new instance of Pandamp_User out of it or returns the data
     * as fetched from the table.
     *
     * @param integer $fb_uid
     *
     *
     * @return Pandamp_User or null if no data with the facebook key $fb_uid exists.
     *
     * @throws Pandamp_BeanContext_Exception The method will throw an exception
     * of the type Pandamp_BeanContext_Exception if the BeanContext-object
     * Pandamp_User could not be created automatically.
     */
    public function getUserFromFacebook($fb_uid)
    {
        $fb_uid = (int)$fb_uid;

        if ($fb_uid <= 0) {
            return null;
        }

        $row = $this->fetchRow($this->select()->where('fb_uid=?', $fb_uid));

        return $row;
    }

    /**
     * Looks up the row with the twitter key $tw_uid, fetches the data and
     * creates a new instance of Pandamp_User out of it or returns the data
     * as fetched from the table.
     *
     * @param integer $tw_uid
     *
     *
     * @return Pandamp_User or null if no data with the twitter key $tw_uid exists.
     *
     * @throws Pandamp_BeanContext_Exception The method will throw an exception
     * of the type Pandamp_BeanContext_Exception if the BeanContext-object
     * Pandamp_User could not be created automatically.
     */
    public function getUserFromTwitter($tw_uid)
    {
        $tw_uid = (int)$tw_uid;

        if ($tw_uid <= 0) {
            return null;
        }

        $row = $this->fetchRow($this->select()->where('tw_uid=?', $tw_uid));

        return $row;
    }

	/**
	 * Gets a user given his id.
   	 *
   	 * The id can be an internal id, or the FriendConnect id.
   	 *
   	 * @param string $id The id of the user.
   	 * @param bool $isFCId True, if this id represents a FriendConnect id.
   	 * Default value is False.
   	 * @return User a User object or a NULL if the user cannot be found.
   	 */
    public function getUserFromGoogleFriend($gfcId)
    {
    	if ($gfcId == "") {
    		return null;
    	}
    	
    	$row = $this->fetchRow($this->select()->where("gfcId='$gfcId'"));
    	
    	return $this->convertRowToGoogleUser($row);
    }
    
	/**
	 * Converts a mysql_result row into a User object.
	 *
	 * @param resource $row The mysql result row fetched using mysql_fetch_assoc,
	 * representing a user.
	 * @return User A User object.
	 */
	private function convertRowToGoogleUser($row) {
		$user = new Pandamp_Core_Panda_GoogleAuth();
		$user->setId($row["id"]);
		$user->setName($row["name"]);
		$user->setPassword($row["password"]);
		$user->setFCId($row["gfcId"]);
		return $user;
	}
  
    /**
   	 * Adds a new user.
   	 *
   	 * @param User $user The user object to be added. Id field, if present, is
   	 * ignored.
   	 * @return User The user object, with the id field populated.
   	 */
    public function addUserGoogle($user)
    {
        $whiteList = array(
            'username' => 'Google_'.$user->getFCId(),
            'name' =>$user->getName(),
        	'gfcId' =>$user->getFCId(),
        	'registered_on' => new Zend_Db_Expr('NOW()')
        );

        $id = $this->insert($whiteList);

        $user->setId($id);

        return $user;
    }
    
    function fetchUser($order,$start,$end, $fields, $selectedRows, $where, $startdt, $enddt)
    {
    	$sql = $this->_db->select()
			->from(array('ku' => 'KutuUser'))
//			->joinLeft(array('kude' => 'KutuUserDetail'),
//			'ku.guid = kude.uid',array('idUser'=>'id','uidUser'=>'uid','packageId','promotionId','educationId','expenseId','paymentId','businessTypeId','periodeId','activationDate','isEmailSent','createdDate','isActive'))
			->joinLeft(array('gag' => 'gacl_aro_groups'),
			'ku.packageId = gag.id')
			->joinLeft(array('kui' => 'KutuUserInvoice'),
			'ku.kopel = kui.uid', array('kui.invoiceId', 'kui.expirationDate','kui.isPaid','DaysLeft' => 'DATEDIFF(NOW(),kui.expirationDate)'))
			->joinLeft(array('kus' => 'KutuUserStatus'),
			'ku.periodeId = kus.accountStatusId','kus.status')
			->where('gag.value=?',"$where");
			
		if ($startdt && $enddt)
		{
			$sql = $sql->where("kui.expirationDate BETWEEN '$startdt' AND '$enddt'");
		}
		
		if (isset($selectedRows))
		{ 
			if (isset($fields))
			{
				$i = 1;
				foreach ($selectedRows as $selrows)
				{				
					switch ($selrows) {
						case 'guid':
							$selrows = 'ku.'.$selrows;
							break;
						case 'kopel':
							$selrows = 'ku.'.$selrows;
							break;
						case 'fullName':
							$selrows = 'ku.'.$selrows;
							break;
						case 'username':
							$selrows = 'ku.'.$selrows;
							break;
						case 'company':
							$selrows = 'ku.'.$selrows;
							break;
						case 'createdDate':
							$selrows = 'ku.'.$selrows;
							break;
						case 'expirationDate':
							$selrows = 'kui.'.$selrows;
							break;
						case 'packageId':
							$selrows = 'gag.value';
							break;
						case 'periodeId':
							$selrows = 'kus.status';
							break;
						case 'paymentId':
							$selrows = 'ku.'.$selrows;
							break;
					}
					if ($i > 1)
					{
						if (preg_match('/%/',$fields)) 
							$sql = $sql->orWhere($selrows.' like ?', "$fields");	
						else 
							$sql = $sql->orWhere($selrows.'= ?', "$fields");
					}
					else 
					{
						if (preg_match('/%/',$fields))
							$sql = $sql->where($selrows.' like ?', "$fields");
						else 
							$sql = $sql->where($selrows.'= ?', "$fields");
					}			
					$i++;
				}
			}				
		}
		
		$sql = $sql->group('kopel');
    	$sql = $sql->order(array($order,'invoiceId DESC'))->limit($end, $start);
//    	$sql = $sql->group('kopel');
//    	$sql = $sql->order(array($order))->limitPage($start, $end);
    	
//    	$sql = $sql->__toString();
//    	print_r($sql);exit();

    	$db = $this->_db->query($sql);
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
    function fetchUserGroupFree($order,$start,$end, $fields, $selectedRows)
    {
    	$sql = $this->_db->select()
			->from(array('ku' => 'KutuUser'))
			->joinLeft(array('gag' => 'gacl_aro_groups'),
			'ku.packageId = gag.id')
			->joinLeft(array('kus' => 'KutuUserStatus'),
			'ku.periodeId = kus.accountStatusId','status')
			->where('gag.value=?','member_gratis');
		
		if (isset($selectedRows))
		{ 
			if (isset($fields))
			{
				$i = 1;
				foreach ($selectedRows as $selrows)
				{				
					switch ($selrows) {
						case 'guid':
							$selrows = 'ku.'.$selrows;
							break;
						case 'kopel':
							$selrows = 'ku.'.$selrows;
							break;
						case 'fullName':
							$selrows = 'ku.'.$selrows;
							break;
						case 'username':
							$selrows = 'ku.'.$selrows;
							break;
						case 'company':
							$selrows = 'ku.'.$selrows;
							break;
						case 'createdDate':
							$selrows = 'ku.'.$selrows;
							break;
						case 'periodeId':
							$selrows = 'kus.status';
							break;
					}
					if ($i > 1)
					{
						if (preg_match('%',$fields)) 
							$sql = $sql->orWhere($selrows.' like ?', "$fields");	
						else 
							$sql = $sql->orWhere($selrows.'= ?', "$fields");
					}
					else 
					{
						if (preg_match('%',$fields))
							$sql = $sql->where($selrows.' like ?', "$fields");
						else 
							$sql = $sql->where($selrows.'= ?', "$fields");
					}			
					$i++;
				}
			}				
		}
		
//		$sql = $sql->group('uidUser');
    	$sql = $sql->order(array($order))->limit($end, $start);
//    	$sql = $sql->order(array($order))->limitPage($start, $end);
    	
//    	$sql = $sql->__toString();
//    	print_r($sql);exit();

    	$db = $this->_db->query($sql);
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
    function fetchUserGroupOther($order,$start,$end, $fields, $selectedRows)
    {
    	$sql = $this->_db->select()
			->from(array('ku' => 'KutuUser'))
			->joinLeft(array('gag' => 'gacl_aro_groups'),
			'ku.packageId = gag.id')
			->joinLeft(array('kus' => 'KutuUserStatus'),
			'ku.periodeId = kus.accountStatusId','status')
			->where('gag.value NOT IN ("member_gratis","member_corporate","member_individual","member_bonus")');
		
		if (isset($selectedRows))
		{ 
			if (isset($fields))
			{
				$i = 1;
				foreach ($selectedRows as $selrows)
				{				
					switch ($selrows) {
						case 'guid':
							$selrows = 'ku.'.$selrows;
							break;
						case 'kopel':
							$selrows = 'ku.'.$selrows;
							break;
						case 'fullName':
							$selrows = 'ku.'.$selrows;
							break;
						case 'username':
							$selrows = 'ku.'.$selrows;
							break;
						case 'company':
							$selrows = 'ku.'.$selrows;
							break;
						case 'createdDate':
							$selrows = 'ku.'.$selrows;
							break;
						case 'periodeId':
							$selrows = 'kus.status';
							break;
					}
					if ($i > 1)
					{
						if (preg_match('%',$fields)) 
							$sql = $sql->orWhere($selrows.' like ?', "$fields");	
						else 
							$sql = $sql->orWhere($selrows.'= ?', "$fields");
					}
					else 
					{
						if (preg_match('%',$fields))
							$sql = $sql->where($selrows.' like ?', "$fields");
						else 
							$sql = $sql->where($selrows.'= ?', "$fields");
					}			
					$i++;
				}
			}				
		}
		
//		$sql = $sql->group('uidUser');
    	$sql = $sql->order(array($order))->limit($end, $start);
//    	$sql = $sql->order(array($order))->limitPage($start, $end);
    	
//    	$sql = $sql->__toString();
//    	print_r($sql);exit();

    	$db = $this->_db->query($sql);
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
 	function fetchUserHistory($guid, $order,$start,$end)
    {
    	$id = $this->fetchUserGroupHistory($guid);
    	$sql = $this->_db->select()
			->from(array('ku' => 'KutuUser'))
			->joinLeft(array('kude' => 'KutuUserDetail'),
			'ku.guid = kude.uid',array('idUser'=>'id','uidUser'=>'uid','packageId','promotionId','educationId','expenseId','paymentId','businessTypeId','periodeId','activationDate','isEmailSent','createdDate','isActive'))
			->joinLeft(array('gag' => 'gacl_aro_groups'),
			'kude.packageId = gag.id')
			->joinLeft(array('kui' => 'KutuUserInvoice'),
			'ku.kopel = kui.uid', array('expirationDate','DaysLeft' => 'DATEDIFF(NOW(),kui.expirationDate)'))
			->joinLeft(array('kus' => 'KutuUserStatus'),
			'kude.periodeId = kus.accountStatusId','status');
		
		$sql = $sql->where('kude.uid=?',"$guid");
//		$sql = $sql->where('kude.id NOT IN ("'.$id.'")');
//		$sql = $sql1->group('uidUser');
    	$sql = $sql->order(array($order,'idUser DESC'))->limit($end, $start);
//    	$sq = $sql->order(array($order))->limitPage($start, $end);
    	
//    	$sql = $sql->__toString();
//    	print_r($sql);exit();

    	$db = $this->_db->query($sql);
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
    function fetchUserGroupHistory($guid)
    {
    	$db = $this->_db->query("SELECT id from KutuUserDetail WHERE uid='$guid' GROUP BY uid ORDER BY id DESC");
    	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);
    	if ($dataFetch) return ($dataFetch[0]['id']);
    }
    function countUserGroup($where, $startdt, $enddt)
    {
    	$sql = $this->_db->select()
			->from(array('ku' => 'KutuUser'),array('count'=>'COUNT(*)'))
			->joinLeft(array('gag' => 'gacl_aro_groups'),'ku.packageId = gag.id')
			->joinLeft(array('kui' => 'KutuUserInvoice'),'ku.kopel = kui.uid', array('kui.expirationDate','kui.isPaid'))
			->where('gag.value=?',$where);
			
		if ($startdt && $enddt)
		{
			$sql = $sql->where("kui.expirationDate BETWEEN '$startdt' AND '$enddt'");
		}
		
//    	$db = $this->_db->query
//    	("Select count(*) count From KutuUser,gacl_aro_groups where KutuUser.packageId=gacl_aro_groups.id AND gacl_aro_groups.value='$where'");
    	
//    	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);
    	
		$db = $this->_db->query($sql);
    	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);
    	
    	return ($dataFetch[0]['count']);
    }
    function countUserGroupOther()
    {
    	$sql = $this->_db->select()
			->from(array('ku' => 'KutuUser'),'count(*) as count')
			->joinLeft(array('gag' => 'gacl_aro_groups'),'ku.packageId = gag.id')
			->joinLeft(array('kus' => 'KutuUserStatus'),'ku.periodeId = kus.accountStatusId','status')
			->where('gag.value NOT IN ("member_gratis","member_corporate","member_individual","member_bonus")');
			
		$db = $this->_db->query($sql);
    	$dataFetch = $db->fetchAll(Zend_Db::FETCH_ASSOC);
    	
    	return ($dataFetch[0]['count']);
    }
    public function getUserCount($guid)
    {
        if ($guid == "") {
            return 0;
        }

        $select = $this->select()
                  ->from($this, array(
                    'COUNT(guid) as count_id'
                  ))
                  ->where('guid = ?', $guid);

        $row = $this->fetchRow($select);

        return ($row !== null) ? $row->count_id : 0;
    }
    
    
    
// -------- interface Pandamp_BeanContext_Decoratable
    
    public function getRepresentedEntity()
    {
    	return 'Pandamp_Modules_Identity_User';
    }
    public function getDecoratableMethods()
    {
    	return array(
    		'getUserForUserNameCredentials',
    		'getUserFromFacebook',
    		'getUserFromTwitter'
    	);
    }
}
