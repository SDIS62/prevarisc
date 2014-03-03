<?php

class Api_ApiController extends Zend_Controller_Action
{
    public function indexAction()
    {
        header('Content-type: application/json');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        // On configure le serveur du Web Service
        $server = new SDIS62_Rest_Server;
        $server->setClass("Api_Service_Api");
        
        // On gère la demande
        $server->handle($this->_request->getParams());
    }
}