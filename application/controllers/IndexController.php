<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout->setLayout('index');

        $this->view->inlineScript()->appendFile("http://packery.metafizzy.co/packery.pkgd.min.js");
        
        $service_feed = new Service_Feed;
        $service_user = new Service_User;

        $this->view->user = $service_user->find(Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR']);
        $this->view->flux = $service_feed->get(Zend_Auth::getInstance()->getIdentity()['group']['ID_GROUPE']);

   
        /*$Commission = new Model_DbTable_Commission;
        
        $dbCommission = $Commission->getAllCommissions();
       
        $this->view->commissionsListe = $dbCommission;
        
        $Datecommission = new Model_DbTable_DateCommission;
        $Firstcommission = $Datecommission->getNextCommission(time(), time() + 3600 * 24 * 15);
        $this->view->firstcommission = $Firstcommission;*/
       
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
        
        $erpsanspreventionniste = Zend_Paginator::factory($data['erpsanspreventionniste']);
        $erpsanspreventionniste->setItemCountPerPage(10)->setCurrentPageNumber(array_key_exists('page', $_GET) ? (int) $_GET['page'] : 1)->setDefaultScrollingStyle('Elastic');
        $this->view->etablissementsanspreventionniste =  $erpsanspreventionniste;
        
        $etablissementavisdefavorable = Zend_Paginator::factory($data['etablissementavisdefavorable']);
        $etablissementavisdefavorable->setItemCountPerPage(10)->setCurrentPageNumber(array_key_exists('page', $_GET) ? (int) $_GET['page'] : 1)->setDefaultScrollingStyle('Elastic');
        $this->view->etablissementavisdefavorable =  $etablissementavisdefavorable;

        $listdossier = Zend_Paginator::factory($data['dossiercommissionechu']);
        $listdossier->setItemCountPerPage(10)->setCurrentPageNumber(array_key_exists('page', $_GET) ? (int) $_GET['page'] : 1)->setDefaultScrollingStyle('Elastic');
        $this->view->dossiercommissionechu =  $listdossier;
        
        $listcourrier= Zend_Paginator::factory($data['courrier']);
        $listcourrier->setItemCountPerPage(10)->setCurrentPageNumber(array_key_exists('page', $_GET) ? (int) $_GET['page'] : 1)->setDefaultScrollingStyle('Elastic');
        $this->view->courrier =  $listcourrier;
        
        $dbCommission= Zend_Paginator::factory($data['commissionsListe']);
        $dbCommission->setItemCountPerPage(10)->setCurrentPageNumber(array_key_exists('page', $_GET) ? (int) $_GET['page'] : 1)->setDefaultScrollingStyle('Elastic');
        $this->view->commissionsListe =  $dbCommission;
        
        $Firstcommission= Zend_Paginator::factory($data['firstcommission']);
        $Firstcommission->setItemCountPerPage(10)->setCurrentPageNumber(array_key_exists('page', $_GET) ? (int) $_GET['page'] : 1)->setDefaultScrollingStyle('Elastic');
        $this->view->firstcommission =  $Firstcommission;
    }
}
