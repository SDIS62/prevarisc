<?php

class SearchController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->redirector('etablissement');
    }

    public function etablissementAction()
    {
        $this->_helper->layout->setLayout('search');

        $service_search = new Service_Search;

        $service_genre = new Service_Genre;
        $service_statut = new Service_Statut;
        $service_avis = new Service_Avis;
        $service_categorie = new Service_Categorie;
        $service_typeactivite = new Service_TypeActivite;
        $service_famille = new Service_Famille;
        $service_classe = new Service_Classe;

        $this->view->DB_genre = $service_genre->getAll();
        $this->view->DB_statut = $service_statut->getAll();
        $this->view->DB_avis = $service_avis->getAll();
        $this->view->DB_classe = $service_classe->getAll();
        $this->view->DB_categorie = $service_categorie->getAll();
        $this->view->DB_typeactivite = $service_typeactivite->getAllWithTypes();
        $this->view->DB_famille = $service_famille->getAll();

        if($this->_request->isGet() && count($this->_request->getQuery()) > 0) {
            try {
                $parameters = $this->_request->getQuery();
                $page = array_key_exists('page', $parameters) ? $parameters['page'] : null;
                $label = array_key_exists('label', $parameters) && $parameters['label'] != '' && (string) $parameters['label'][0] != '#' ? $parameters['label'] : null;
                $identifiant = array_key_exists('label', $parameters) && $parameters['label'] != '' && (string) $parameters['label'][0] == '#'? substr($parameters['label'], 1) : null;
                $genres = array_key_exists('genres', $parameters) ? $parameters['genres'] : null;
                $categories = array_key_exists('categories', $parameters) ? $parameters['categories'] : null;
                $classes = array_key_exists('classes', $parameters) ? $parameters['classes'] : null;
                $familles = array_key_exists('familles', $parameters) ? $parameters['familles'] : null;
                $types_activites = array_key_exists('types_activites', $parameters) ? $parameters['types_activites'] : null;
                $avis_favorable = array_key_exists('avis', $parameters) && count($parameters['avis']) == 1 ? $parameters['avis'][0] == 'true' : null;
                $statuts = array_key_exists('statuts', $parameters) ? $parameters['statuts'] : null;
                $local_sommeil = array_key_exists('presences_local_sommeil', $parameters) && count($parameters['presences_local_sommeil']) == 1 ? $parameters['presences_local_sommeil'][0] == 'true' : null;
                $city = array_key_exists('city', $parameters) && $parameters['city'] != '' ? $parameters['city'] : null;
                $street = array_key_exists('street', $parameters) && $parameters['street'] != '' ? $parameters['street'] : null;

                $search = $service_search->etablissements($label, $identifiant, $genres, $categories, $classes, $familles, $types_activites, $avis_favorable, $statuts, $local_sommeil, null, null, null, $city, $street, 50, $page);

                $paginator = new Zend_Paginator(new SDIS62_Paginator_Adapter_Array($search['results'], $search['search_metadata']['count']));
                $paginator->setItemCountPerPage(50)->setCurrentPageNumber($page)->setDefaultScrollingStyle('Elastic');

                $this->view->results = $paginator;
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => 'Problème de recherche','message' => 'La recherche n\'a pas été effectué correctement. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }
    }

    public function dossierAction()
    {
        $this->_helper->layout->setLayout('search');

        $service_search = new Service_Search;
        $service_commissions = new Service_Commission;
        $service_adresse = new Service_Adresse;
        $service_dossier = new Service_Dossier;

        $this->view->DB_type = $service_dossier->getAllTypes();
        $this->view->array_commissions = $service_commissions->getCommissionsAndTypes();
        $this->view->array_communes = $service_adresse->getAllCommunes();
        $this->view->liste_prev = $service_search->listePrevActifs();
        $this->view->array_voies = $this->_request->isGet() && count($this->_request->getQuery()) > 0 && array_key_exists('commune', $this->_request->getQuery()) && $this->_request->getQuery()['commune'] != '' ? $service_adresse->getVoies($this->_request->getQuery()['commune']) : array();

        $checkDateFormat = function($date) {
            if (!$date) return false;
            $dateArgs = explode('/', $date);
            return checkdate($dateArgs[1], $dateArgs[0], $dateArgs[2]);
        };

        if($this->_request->isGet() && count($this->_request->getQuery()) > 0) {
            try {
                $parameters = $this->_request->getQuery();
                $page = array_key_exists('page', $parameters) ? $parameters['page'] : 1;
                $num_doc_urba = array_key_exists('permis', $parameters) && $parameters['permis'] != '' ? $parameters['permis'] : null;
                $objet = array_key_exists('objet', $parameters) && $parameters['objet'] != ''  && (string) $parameters['objet'][0] != '#'? $parameters['objet'] : null;
                $types = array_key_exists('types', $parameters) ? $parameters['types'] : null;
                $criteresRecherche = array();
                $criteresRecherche['commissions'] = array_key_exists('commissions', $parameters) ? $parameters['commissions'] : null;
                $criteresRecherche['avisCommission'] = array_key_exists('avisCommission', $parameters) ? $parameters['avisCommission'] : null;
                $criteresRecherche['avisRapporteur'] = array_key_exists('avisRapporteur', $parameters) ? $parameters['avisRapporteur'] : null;
                $criteresRecherche['commune'] = array_key_exists('commune', $parameters) && $parameters['commune'] != '' ? $parameters['commune'] : null;
                $criteresRecherche['voie'] = array_key_exists('voie', $parameters) && $parameters['voie'] != '' ? $parameters['voie'] : null;
                $criteresRecherche['preventionniste'] = array_key_exists('preventionniste', $parameters) && $parameters['preventionniste'] != '' ? $parameters['preventionniste'] : null;
                $criteresRecherche['dateCreationStart'] = array_key_exists('date-creation-start', $parameters) && $checkDateFormat($parameters['date-creation-start']) ? $parameters['date-creation-start'] : null;
                $criteresRecherche['dateCreationEnd'] = array_key_exists('date-creation-end', $parameters) && $checkDateFormat($parameters['date-creation-end']) ? $parameters['date-creation-end'] : null;
                $criteresRecherche['dateReceptionStart'] = array_key_exists('date-reception-start', $parameters) && $checkDateFormat($parameters['date-reception-start']) ? $parameters['date-reception-start'] : null;
                $criteresRecherche['dateReceptionEnd'] = array_key_exists('date-reception-end', $parameters) && $checkDateFormat($parameters['date-reception-end']) ? $parameters['date-reception-end'] : null;
                $criteresRecherche['dateReponseStart'] = array_key_exists('date-reponse-start', $parameters) && $checkDateFormat($parameters['date-reponse-start']) ? $parameters['date-reponse-start'] : null;
                $criteresRecherche['dateReponseEnd'] = array_key_exists('date-reponse-end', $parameters) && $checkDateFormat($parameters['date-reponse-end']) ? $parameters['date-reponse-end'] : null;

                $search = $service_search->dossiers($types, $objet, $num_doc_urba, null, null, 50, $page,$criteresRecherche);

                $paginator = new Zend_Paginator(new SDIS62_Paginator_Adapter_Array($search['results'], $search['search_metadata']['count']));
                $paginator->setItemCountPerPage(50)->setCurrentPageNumber($page)->setDefaultScrollingStyle('Elastic');

                $this->view->results = $paginator;
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => 'Problème de recherche','message' => 'La recherche n\'a pas été effectué correctement. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }
    }

    public function utilisateurAction()
    {
        $this->_helper->layout->setLayout('search');

        $service_search = new Service_Search;
        $service_user = new Service_User;

        $this->view->DB_fonction = $service_user->getAllFonctions();

        if($this->_request->isGet() && count($this->_request->getQuery()) > 0) {
            try {
                $parameters = $this->_request->getQuery();
                $page = array_key_exists('page', $parameters) ? $parameters['page'] : 1;
                $name = $parameters['name'];
                $fonctions = array_key_exists('fonctions', $parameters) ? $parameters['fonctions'] : null;

                $search = $service_search->users($fonctions, $name, null, true, 50, $page);

                $paginator = new Zend_Paginator(new SDIS62_Paginator_Adapter_Array($search['results'], $search['search_metadata']['count']));
                $paginator->setItemCountPerPage(50)->setCurrentPageNumber($page)->setDefaultScrollingStyle('Elastic');

                $this->view->results = $paginator;
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => 'Problème de recherche','message' => 'La recherche n\'a pas été effectué correctement. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }
    }
    
    public function displayAjaxSearchEtablissementAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $service_search = new Service_Search;

        $data = $service_search->etablissements(null, null, null, null, null, null, null, null, null, null, null, null, $this->_request->parent, null, null, 1000);

        $data = $data['results'];

        $html = "<ul class='recherche_liste'>";
        $html .= Zend_Layout::getMvcInstance()->getView()->partialLoop('search/results/etablissement.phtml', (array) $data );
        $html .= "</ul>";

        echo $html;
    }
    
    public function displayAjaxSearchDossierAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $service_search = new Service_Search;

        $data = $service_search->dossiers(null, null, null, $this->_request->parent, null, 100);

        $data = $data['results'];

        $html = "<ul class='recherche_liste'>";
        $html .= Zend_Layout::getMvcInstance()->getView()->partialLoop('search/results/dossier.phtml', (array) $data );
        $html .= "</ul>";

        echo $html;
    }
}
