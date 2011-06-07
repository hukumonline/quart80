<?php

/**
 * @package kutump
 * @copyright 2008-2009 hukumonline.com/en.hukumonline.com
 * @author Nihki Prihadi <nihki@hukumonline.com>
 *
 * $Id: User.php 2009-01-10 19:45: $
 */

class Pandamp_Modules_Identity_User_Model_Row_User extends Zend_Db_Table_Row_Abstract
{
	protected function _insert()
	{
		if (empty($this->guid))
		{
			$generateGuid = new Pandamp_Core_Guid();
			$this->guid = $generateGuid->generateGuid();
		}
		
//		if (empty($this->kopel))
//		{
//			$num = new Pandamp_Core_Number();
//			$this->kopel = $num->generateNumber();
//		}
		
		$today = date('Y-m-d h:i:s');
		
		if (empty($this->createdDate)) 	
			$this->createdDate = $today;	
		
		if (empty($this->updatedDate))
			$this->updatedDate = $today;
		
		$userName = '';
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$userName = $auth->getIdentity()->username;
		}
		
		if (empty($this->createdBy))
			$this->createdBy = $userName;
		
		if (empty($this->updatedBy))		
			$this->updatedBy = $userName;
		
		if (empty($this->educationId))
			$this->educationId = 0;
		
		if (empty($this->expenseId))
			$this->expenseId = 0;
		
		if (empty($this->activationDate))
			$this->activationDate = '0000-00-00 00:00:00';
		
		if (empty($this->paymentId))
			$this->paymentId = 0;
		
		if (empty($this->periodeId))		
			$this->periodeId = 1;
		
		if (empty($this->isEmailSent))		
			$this->isEmailSent = 'N';
	}
	protected function _update()
	{
    	$this->updatedDate = date("Y-m-d h:i:s");
    	
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$userName = $auth->getIdentity()->username;
			$this->updatedBy = $userName;
		}
	}
	protected function _postDelete()
	{
		$tblUserDetail = new Pandamp_Modules_Identity_User_Model_UserDetail();
		$rowsetUserDetail = $tblUserDetail->fetchAll("uid='$this->guid'");
		foreach ($rowsetUserDetail as $row)
		{
			// delete
			$row->delete();
		}
		
		//delete from table KutuUserInvoice
		$tblInvoice = new Pandamp_Modules_Payment_Invoice_Model_Invoice();
		$tblInvoice->delete("uid='$this->kopel'");
		//delete from table KutuUserAccessLog
		$tblUserLog = new Pandamp_Modules_Identity_Log_Model_Log();
		$tblUserLog->delete("user_id='$this->guid'");
			
		//delete from ACL
		$aclMan = Pandamp_Acl::manager();
		$aclMan->deleteUser($this->username);
		
		// delete physical user folder define by guid 
		$sDir = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$this->guid;
		try {
			$this->removeRessource($sDir);
		}
		catch (Exception $e)
		{
			throw new Exception($e);
		}
		
		$sDir = ROOT_DIR.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'photo';
		try {
		   	if (file_exists($sDir."/".$this->guid.".gif")) 		{ unlink($sDir."/".$this->guid.".gif"); 	}
		   	if (file_exists($sDir."/".$this->guid.".jpg")) 		{ unlink($sDir."/".$this->guid.".jpg"); 	}
		   	if (file_exists($sDir."/".$this->guid.".jpeg")) 	{ unlink($sDir."/".$this->guid.".jpeg"); 	}
		   	if (file_exists($sDir."/".$this->guid.".png")) 		{ unlink($sDir."/".$this->guid.".png"); 	}
		}
		catch (Exception $e)
		{
			
		}
		
	}
	public function findParentRowUserDetail()
	{
		return $this->findParentRow('Pandamp_Modules_Identity_User_Model_UserDetail');
	}
	public function findDependentRowsetUserDetail()
	{
		return $this->findDependentRowset('Pandamp_Modules_Identity_User_Model_UserDetail');
	}
	public function removeRessource( $_target ) {
	   
	    //file?
	    if( is_file($_target) ) {
	        if( is_writable($_target) ) {
	            if( @unlink($_target) ) {
	                return true;
	            }
	        }
	       
	        return false;
	    }
	       
	    //dir?
	    if( is_dir($_target) ) {
	        if( is_writeable($_target) ) {
	            foreach( new DirectoryIterator($_target) as $_res ) {
	                if( $_res->isDot() ) {
	                    unset($_res);
	                    continue;
	                }
	                   
	                if( $_res->isFile() ) {
	                    removeRessource( $_res->getPathName() );
	                } elseif( $_res->isDir() ) {
	                    removeRessource( $_res->getRealPath() );
	                }
	               
	                unset($_res);
	            }
	                   
	            if( @rmdir($_target) ) {
	                return true;
	            }
	        }
	       
	        return false;
	    }
	} 	
}

?>