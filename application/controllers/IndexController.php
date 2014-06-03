<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $service_feed = new Service_Feed;
        $service_user = new Service_User;

        $this->view->user = $service_user->find(Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR']);
        $this->view->flux = $service_feed->get(Zend_Auth::getInstance()->getIdentity()['group']['ID_GROUPE']);
        
        
        /************************************************/
        
        $Commission = new Model_DbTable_Commission;
        
        $dbCommission = $Commission->getAllCommissions();
       
        $this->view->commissionsListe = $dbCommission;
        
        $Datecommission = new Model_DbTable_DateCommission;
        $Firstcommission = $Datecommission->getNextCommission(time(), time() + 3600 * 24 * 15);
        $this->view->firstcommission = $Firstcommission;
        
    
        /**************************************************/
 
        $paginator = Zend_Paginator::factory($service_user->getEtablissements(Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR']));
        $paginator->setItemCountPerPage(10)->setCurrentPageNumber(array_key_exists('page', $_GET) ? (int) $_GET['page'] : 1)->setDefaultScrollingStyle('Elastic');
        $this->view->etablissements = $paginator;
    }
}
