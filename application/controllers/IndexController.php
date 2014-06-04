<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout->setLayout('dashboard');

        $this->view->inlineScript()->appendFile("http://packery.metafizzy.co/packery.pkgd.min.js");
        //$this->view->headLink()->appendStylesheet('http://ksylvest.github.io/jquery-gridly/stylesheets/jquery.gridly.css', 'all');

        $service_feed = new Service_Feed;
        $service_user = new Service_User;

        $this->view->user = $service_user->find(Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR']);
        $this->view->flux = $service_feed->get(Zend_Auth::getInstance()->getIdentity()['group']['ID_GROUPE']);

        $data = $service_user->getDashboardData(Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR']);

        $etablissements = Zend_Paginator::factory($data['etablissements']);
        $etablissements->setItemCountPerPage(10)->setCurrentPageNumber(array_key_exists('page', $_GET) ? (int) $_GET['page'] : 1)->setDefaultScrollingStyle('Elastic');
        $this->view->etablissements = $etablissements;

        $dossiers = Zend_Paginator::factory($data['dossiers']);
        $dossiers->setItemCountPerPage(10)->setCurrentPageNumber(array_key_exists('page', $_GET) ? (int) $_GET['page'] : 1)->setDefaultScrollingStyle('Elastic');
        $this->view->dossiers = $dossiers;

        $commissions = Zend_Paginator::factory($data['commissions']);
        $commissions->setItemCountPerPage(10)->setCurrentPageNumber(array_key_exists('page', $_GET) ? (int) $_GET['page'] : 1)->setDefaultScrollingStyle('Elastic');
        $this->view->commissions = $commissions;
    }
}
