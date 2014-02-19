<?php

    class ProxyController extends Zend_Controller_Action
    {
        public function indexAction()
        {
            $this->getHelper('viewRenderer')->setNoRender();

            // On forme la chaine de param�tres
            $params = '?';
            foreach ($this->_request->getParams() as $key => $value) {

                if(!in_array($key, array('url', 'controller', 'action', 'module')))
                    $params .= $key . '=' . str_replace( ' ', '+', $value)  . '&';
            }

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
