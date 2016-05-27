<?php

class Api_Plugin_RefererCheck extends Zend_Controller_Plugin_Abstract
{
    /**
     * On se place dans le preDispatch pour autoriser ou non l'accès aux api
     * 
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // Récupération de la white-list
        $whitelist = new Zend_Config_Ini(APPLICATION_PATH . DS . 'modules' . DS . 'api' . DS . 'configs' . DS . 'whitelist.ini');

        // Contrôle de l'IP de celui qui fait la requête à notre whitelist
        if(in_array($_SERVER['REMOTE_ADDR'], $whitelist->toArray())) {
            return;
        } 
        else {
            // Repoint the request to the default error handler
            $request->setModuleName('default')->setControllerName('error')->setActionName('error');

            // Set up the error handler
            $error = new Zend_Controller_Plugin_ErrorHandler;
            $error->type = Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER;
            $error->request = clone($request);
            $error->exception = new Exception('Unauthorized', 401);
            $request->setParam('error_handler', $error);
        }
    }
}