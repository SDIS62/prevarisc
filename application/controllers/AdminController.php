<?php

class AdminController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        $this->view->key_ign = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('ign')['key'];
        $this->view->ldap_enabled = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('ldap')['enabled'];
        $this->view->dbname = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('resources')['db']['params']['dbname'];
        $this->view->db_url = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('resources')['db']['params']['host'];
    }
}
