<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $service_feed = new Service_Feed;
        $service_user = new Service_User;

        $this->view->user = $service_user->find(Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR']);
        $this->view->flux = $service_feed->get(Zend_Auth::getInstance()->getIdentity()['group']['ID_GROUPE']);

        $paginator = Zend_Paginator::factory($service_user->getEtablissements(Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR']));
        $paginator->setItemCountPerPage(10)->setCurrentPageNumber(array_key_exists('page', $_GET) ? (int) $_GET['page'] : 1)->setDefaultScrollingStyle('Elastic');
        $this->view->etablissements = $paginator;
    }
}
