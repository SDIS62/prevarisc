<?php

class Plugin_XmlHttpRequest extends Zend_Controller_Plugin_Abstract
{   
      // Ajax : désactive le layout quand une requete ajax est envoyée
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        //Header pour autoriser la connexion avec l'application mobile
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        if( $request->isXmlHttpRequest() )
        {
            Zend_Layout::getMvcInstance()->disableLayout();
        }
    }

}