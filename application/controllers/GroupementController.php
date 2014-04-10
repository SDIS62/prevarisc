<?php

class GroupementController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('add', 'json')
                    ->addActionContext('display', 'html')
                    ->addActionContext('add-type', 'json')
                    ->initContext();
    }

    public function indexAction()
    {
        // Titre
        $this->view->title = "Groupements de communes";

        $this->_helper->layout->setLayout('menu_admin');

        // Liste des models
        $model_groupementstypes = new Model_DbTable_GroupementType;

        // Liste des types de groupement
        $array_groupementstypes = $model_groupementstypes->fetchAll()->toArray();

        // Envoi dans la vue les groupements et leur types
        $this->view->array_groupementstypes = $array_groupementstypes;

        $commune = new Model_DbTable_AdresseCommune;
        $this->view->villes_tests = $commune->fetchAll()->toArray();
    }

    public function displayAction()
    {
        // Liste des villes pour le select
        $commune = new Model_DbTable_AdresseCommune;
        $this->view->villes = $commune->fetchAll()->toArray();

        // On check les prev du groupement
        $groupements = new Model_DbTable_Groupement();

        // Liste des types de groupement
        $groupement_type = new Model_DbTable_GroupementType();
        $types = $groupement_type->fetchAll();

        $this->view->types = $types;

        // Coordonnées du groupement
        $DB_informations = new Model_DbTable_UtilisateurInformations;

        if ( isset($_GET["id"]) && $_GET["id"] != 0 ) {
            $groupement = $groupements->find($_GET["id"])->current();
            $this->view->groupement = $groupement->toArray();
            $this->view->libelle = $groupement["LIBELLE_GROUPEMENT"];
            $this->view->type = $groupement["ID_GROUPEMENTTYPE"];
            $this->view->preventionnistes = $groupements->getPreventionnistes($_GET["id"]);
            $this->view->ville_du_groupement = $groupement->findModel_DbTable_AdresseCommuneViaModel_DbTable_GroupementCommune()->toArray();
            $this->view->user_info = $DB_informations->find( $groupement->ID_UTILISATEURINFORMATIONS )->current();
        } else
            $this->view->preventionnistes = array();
    }

    public function viewAction()
    {
        $model_groupement = new Model_DbTable_Groupement();
        $this->view->row = $model_groupement->get($this->_request->id);
        $this->view->prev = $model_groupement->getPreventionnistes($this->_request->id);
    }

    public function addAction()
    {
        try {
            // Modele groupement et groupement communes et groupement prev.
            $groupements = new Model_DbTable_Groupement;
            $groupementscommune = new Model_DbTable_GroupementCommune;
            $groupementsprev = new Model_DbTable_GroupementPreventionniste;
            $DB_informations = new Model_DbTable_UtilisateurInformations;

            // Si c'est pour un nouveau groupement
            if ($_POST["id_gpt"] == 0) {
                $new_groupement = $groupements->createRow();
                $id_coord = $DB_informations->insert(array_intersect_key($_POST, $DB_informations->info('metadata')));
                $new_groupement->ID_UTILISATEURINFORMATIONS = $id_coord;
            } else {
                $new_groupement_item = $groupements->find( $_POST["id_gpt"] );
                $new_groupement = $new_groupement_item->current();

                $groupementscommune->delete( $groupementscommune->getAdapter()->quoteInto('ID_GROUPEMENT = ?', $new_groupement->ID_GROUPEMENT ) );
                $groupementsprev->delete( $groupementsprev->getAdapter()->quoteInto('ID_GROUPEMENT = ?', $new_groupement->ID_GROUPEMENT ) );

                $info = $DB_informations->find( $new_groupement->ID_UTILISATEURINFORMATIONS )->current();

                if ($info == null) {
                    if($_POST["ID_UTILISATEURCIVILITE"] == "null")
                        unset($_POST["ID_UTILISATEURCIVILITE"]);
                    unset($_POST["ID_FONCTION"]);
                    $id = $DB_informations->insert(array_intersect_key($_POST, $DB_informations->info('metadata')));
                    $new_groupement->ID_UTILISATEURINFORMATIONS = $id;
                } else {
                    if($_POST["ID_UTILISATEURCIVILITE"] == "null")
                        unset($_POST["ID_UTILISATEURCIVILITE"]);
                    unset($_POST["ID_FONCTION"]);
                    $info->setFromArray(array_intersect_key($_POST, $DB_informations->info('metadata')))->save();
                }
            }

            $new_groupement->LIBELLE_GROUPEMENT = $_POST["nom_groupement"];
            $new_groupement->ID_GROUPEMENTTYPE = $_POST["type_groupement"];

            $new_groupement->save();

            // On associe les communes
            if ( isset($_POST["villes"]) ) {
                foreach ($_POST["villes"] as $value) {
                    $new = $groupementscommune->createRow();

                    $new->ID_GROUPEMENT = $new_groupement->ID_GROUPEMENT;
                    $new->NUMINSEE_COMMUNE = $value;

                    $new->save();
                }
            }

            // On associe les preventionnistes
            if ( isset($_POST["prev"]) ) {
                foreach ($_POST["prev"] as $value) {
                    $new = $groupementsprev->createRow();

                    $new->ID_GROUPEMENT = $new_groupement->ID_GROUPEMENT;
                    $new->ID_UTILISATEUR = $value;
                    $new->DATEDEBUT_GROUPEMENTPREVENTIONNISTE = date("Y-m-d H:i:s");

                    $new->save();
                }
            }

            $this->view->id = $new_groupement->ID_GROUPEMENT;
            $this->view->libelle = $new_groupement->LIBELLE_GROUPEMENT;
            $this->view->type = $new_groupement->ID_GROUPEMENTTYPE;

            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'Ajout réussi !',
                'message' => 'Le groupement '.$new_groupement->LIBELLE_GROUPEMENT.' a été ajouté.'
            ));

        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Aie',
                'message' => $e->getMessage()
            ));
        }
    }

    public function deleteAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender(); // On desactive la vue

            // On supprime le groupement
            $groupements = new Model_DbTable_Groupement();
            $communes = new Model_DbTable_GroupementCommune();
            $prev = new Model_DbTable_GroupementPreventionniste();
            $contacts = new Model_DbTable_GroupementContact();

            $contacts->delete("ID_GROUPEMENT = " . $_GET["id"]);
            $communes->delete("ID_GROUPEMENT = " . $_GET["id"]);
            $prev->delete("ID_GROUPEMENT = " . $_GET["id"]);
            $groupements->delete("ID_GROUPEMENT = " . $_GET["id"]);

            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'Suppression réussie !',
                'message' => 'Le groupement a été supprimé.'
            ));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Aie',
                'message' => $e->getMessage()
            ));
        }

        // Redirection
        $this->_helper->redirector('index');
    }

    public function addTypeAction()
    {
        try {
            // Modèle
            $model_groupementtype = new Model_DbTable_GroupementType();

            // Ajout du type
            $new = $model_groupementtype->createRow();
            $new->LIBELLE_GROUPEMENTTYPE = $this->_request->LIBELLE_GROUPEMENTTYPE;
            $new->save();

            $this->view->id = $new->ID_GROUPEMENTTYPE;

            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'Ajout réussi !',
                'message' => 'Le traitement est ok.'
            ));
        } catch (Exception $ex) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Aie',
                'message' => $ex->getMessage()
            ));
        }
        // Redirection
        $this->_helper->redirector('index');
    }

    public function deleteTypeAction()
    {
        // On desactive la vue
        $this->_helper->viewRenderer->setNoRender();
    }
}
