<?php

/**
 * @package holemp
 * @author Nihki Prihadi <nihki@hukumonline.com>
 *
 * $error: ErrorController 2009-07-11 11:09
 */

class ErrorController extends Zend_Controller_Action {

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

}