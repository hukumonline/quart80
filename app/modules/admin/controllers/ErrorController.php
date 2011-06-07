<?php
class Admin_ErrorController extends Zend_Controller_Action {

    public function init()
    {
        $contextSwitch = $this->_helper->contextSwitch();

        $contextSwitch->addActionContext('error', 'json')
                      ->initContext();
    }

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        $error = array();

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:
                $error = Pandamp_Error::fromException($errors->exception);
            break;
        }

        $this->getResponse()->clearBody();
        $this->view->success = false;
        $this->view->error   = $error->getDto();
    }
	function restrictedAction()
	{
		$req = $this->getRequest();
		$type = ($req->getParam('type'))? $req->getParam('type') : '';
		$num =  ($req->getParam('num'))? $req->getParam('num') : '';
		switch ($type)
		{
			case "search":
				switch ($num) 
				{
					case 101:
						$error_msg = "Data tidak ditemukan";
						break;
				}
			break;
			case "user":
				switch ($num) 
				{
					case 101:
						$error_msg = "Don't have enough permission. Please contact Administrator.";
						break;
				}
			break;
			default:
				$error_msg = "Page restricted!!";
		}
		$this->view->error_msg = $error_msg;
	}
	public function temporaryAction() {}
}