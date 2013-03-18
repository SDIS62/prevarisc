<?php

    class ProxyController extends Zend_Controller_Action
    {
        public function indexAction()
        {
            $this->getHelper('viewRenderer')->setNoRender();

            // echo file_get_contents($this->_request->url);
            // Zend_Debug::Dump();

            // On forme la chaine de paramètres
            $params = '?';
            foreach ($this->_request->getParams() as $key => $value) {

                if(!in_array($key, array('url', 'controller', 'action', 'module')))
                    $params .= $key . '=' . str_replace( ' ', '+', $value)  . '&';
            }

            // echo $this->_request->url . $params; return false;

            // Website url to open
            $daurl = $this->_request->url . $params;

            // Get that website's content
            $handle = fopen($daurl, "r");

            // If there is something, read and return
            if ($handle) {
                while (!feof($handle)) {
                    $buffer = fgets($handle, 4096);
                    echo $buffer;
                }
                fclose($handle);
            }

        }
    }
