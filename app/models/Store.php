<?php
class App_Model_Store
{
	function isUserOwnOrder($userId, $orderId)
	{
		$tblOrder = new Pandamp_Modules_Payment_Order_Model_Order();
		$rowOrder = $tblOrder->find($orderId)->current();
		if(!empty($rowOrder))
		{
			if($userId == $rowOrder->userId)
				return true;
			else
				return false;
		}
		else
			return false;
	}
	function isOrderPaid($orderId)
	{
		$tblOrder = new Pandamp_Modules_Payment_Order_Model_Order();
		$rowOrder = $tblOrder->find($orderId)->current();
		if(!empty($rowOrder))
		{
			if($rowOrder->orderStatus==3)
				return true;
			else
				return false;
		}
		else
			return false;
	}
}