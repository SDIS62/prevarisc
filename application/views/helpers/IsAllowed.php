<?php

class View_Helper_IsAllowed extends Zend_View_Helper_Abstract
{
    public function isAllowed($action, $controller = null, $module = null, $params = array(), $affix = '')
    {
        $urlOptions = array('action' => $action);
        if($controller) $urlOptions['controller'] = $controller;
        if($module) $urlOptions['module'] = $module;
        $urlOptions = array_merge($urlOptions, $params);

        $url = $this->view->serverUrl() . @$this->view->url($urlOptions) . $affix;

        $request = new Zend_Controller_Request_Http($url);

        $frontController = Zend_Controller_Front::getInstance();
        $router = $frontController->getRouter();
        $router->route($request);

        $acl = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('acl');
        return $acl->isAllowedRequest($request);
    }
}
