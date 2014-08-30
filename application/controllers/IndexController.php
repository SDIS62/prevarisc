<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout->setLayout('index');

        $this->view->inlineScript()->appendFile("/js/jquery.packery.pkgd.min.js");
        
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
        
        $erpSansPreventionniste = Zend_Paginator::factory($data['erpSansPreventionniste']);
        $erpSansPreventionniste->setItemCountPerPage(10)->setCurrentPageNumber(array_key_exists('page', $_GET) ? (int) $_GET['page'] : 1)->setDefaultScrollingStyle('Elastic');
        $this->view->erpSansPreventionniste =  $erpSansPreventionniste;
        
        $etablissementAvisDefavorable = Zend_Paginator::factory($data['etablissementAvisDefavorable']);
        $etablissementAvisDefavorable->setItemCountPerPage(10)->setCurrentPageNumber(array_key_exists('page', $_GET) ? (int) $_GET['page'] : 1)->setDefaultScrollingStyle('Elastic');
        $this->view->etablissementAvisDefavorable =  $etablissementAvisDefavorable;

        $listDossierCommissionEchu = Zend_Paginator::factory($data['dossierCommissionEchu']);
        $listDossierCommissionEchu->setItemCountPerPage(10)->setCurrentPageNumber(array_key_exists('page', $_GET) ? (int) $_GET['page'] : 1)->setDefaultScrollingStyle('Elastic');
        $this->view->dossierCommissionEchu =  $listDossierCommissionEchu;
        
        $listeDesCourrierSansReponse= Zend_Paginator::factory($data['CourrierSansReponse']);
        $listeDesCourrierSansReponse->setItemCountPerPage(10)->setCurrentPageNumber(array_key_exists('page', $_GET) ? (int) $_GET['page'] : 1)->setDefaultScrollingStyle('Elastic');
        $this->view->CourrierSansReponse =  $listeDesCourrierSansReponse;
        
        $prochainesCommission= Zend_Paginator::factory($data['prochainesCommission']);
        $prochainesCommission->setItemCountPerPage(10)->setCurrentPageNumber(array_key_exists('page', $_GET) ? (int) $_GET['page'] : 1)->setDefaultScrollingStyle('Elastic');
        $this->view->prochainesCommission =  $prochainesCommission;
        
        $NbrDossiersAffect= Zend_Paginator::factory($data['NbrDossiersAffect']);
        $NbrDossiersAffect->setItemCountPerPage(10)->setCurrentPageNumber(array_key_exists('page', $_GET) ? (int) $_GET['page'] : 1)->setDefaultScrollingStyle('Elastic');
        $this->view->NbrDossiersAffect =  $NbrDossiersAffect;
        
        $ErpSansProchaineVisitePeriodeOuvert= Zend_Paginator::factory($data['ErpSansProchaineVisitePeriodeOuvert']);
        $ErpSansProchaineVisitePeriodeOuvert->setItemCountPerPage(10)->setCurrentPageNumber(array_key_exists('page', $_GET) ? (int) $_GET['page'] : 1)->setDefaultScrollingStyle('Elastic');
        $this->view->ErpSansProchaineVisitePeriodeOuvert =  $ErpSansProchaineVisitePeriodeOuvert;
        
    }
}
