<?php

class ProxyController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        $this->_helper->layout()->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(true);
        

        // On forme la chaine de paramètres
        $params = "";
        foreach ($this->_request->getParams() as $key => $value) {

            if(!in_array($key, array('url', 'controller', 'action', 'module')))
                $params .= $key . '=' . str_replace( ' ', '+', $value)  . '&';
        }
        
        if ($params) {
            $params = '?'.$params;
        }

        // Website url to open
        $daurl = $this->_request->url . $params;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $daurl);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_REFERER']);
        
        // are we under a proxy?
        if (1 == getenv('PREVARISC_PROXY_ENABLED')) {
            
            curl_setopt($ch, CURLOPT_PROXYTYPE, getenv('PREVARISC_PROXY_PROTOCOL'));
            curl_setopt($ch, CURLOPT_PROXYPORT, getenv('PREVARISC_PROXY_PORT'));
            curl_setopt($ch, CURLOPT_PROXY, getenv('PREVARISC_PROXY_HOST'));
            if (getenv('PREVARISC_PROXY_USERNAME')) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, getenv('PREVARISC_PROXY_USERNAME').':'.getenv('PREVARISC_PROXY_PASSWORD'));
            }
        }
        
        $data = curl_exec($ch);
        
        if ($data === false) {
            $body = curl_error($ch);
        } else {
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($data, 0, $header_size);
            $headers = explode("\r\n", $header);
            $body = substr($data, $header_size);

            curl_close($ch);

            foreach($headers as $header) {
                if ($header) {
                    if (preg_match('/^Content-Type/i', $header) !== 0) {
                        $this->_response->setRawHeader($header);
                    }
                }
            }
        }
        
        
        
        $this->_response->setBody($body);
    }
}
