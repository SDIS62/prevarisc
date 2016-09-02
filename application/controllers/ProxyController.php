<?php

class ProxyController extends Zend_Controller_Action
{
    public function indexAction()
    {
        // Récupération de la configuration
        $options = Zend_Registry::get('options');

        // On annule le rendu des vues
        $this->_helper->viewRenderer->setNoRender(true);

        // Paramètres de configuration du proxy
        $config = array(
            'adapter'    => $options['proxy']['enabled'] ? 'Zend_Http_Client_Adapter_Proxy' : 'Zend_Http_Client_Adapter_Socket',
            'proxy_host' => $options['proxy']['host'],
            'proxy_port' => $options['proxy']['port'],
            'proxy_user' => $options['proxy']['username'],
            'proxy_pass' => $options['proxy']['password']
        );

        // Formattage de l'URL
        $params = "";
        foreach($this->_request->getParams() as $key => $value) {
            if(!in_array($key, array('url', 'controller', 'action', 'module'))) {
                $params .= (empty($params) ? "?" : "&").$key.'='.str_replace(' ', '+', $value);
            }
        }
        $url = $this->_request->url . $params;

        // Crée l'objet HTTP
        $client = new Zend_Http_Client($url, $config);

        // On récupère la réponse
        $this->_response->setBody($client->request());
    }
}
