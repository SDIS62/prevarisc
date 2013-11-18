<?php

    class MatriceDesDroitsController extends Zend_Controller_Action
    {
        public function indexAction()
        {
            // Modèles
            $model_groupes = new Model_DbTable_Groupe;
            $model_genres = new Model_DbTable_Genre;
            $model_types = new Model_DbTable_Type;
            $model_natures = new Model_DbTable_DossierNatureliste;

            // On envoit les données sur la vue
            $this->view->rowset_groupes = $model_groupes->myFetchAll();
            $this->view->rowset_genres = $model_genres->fetchAll();
            $this->view->rowset_types = $model_types->fetchAll();
            $this->view->rowset_natures = $model_natures->fetchAll();
        }

        public function saveAction()
        {
            $this->getHelper('viewRenderer')->setNoRender();

            // Modèles
            $model_groupegenres = new Model_DbTable_GroupeGenre;
            $model_groupenatures = new Model_DbTable_GroupeNature;
            $model_groupetypes = new Model_DbTable_GroupeType;
            $model_groupes = new Model_DbTable_Groupe;

            // Correspondances des porteuses
            $correspondances = array(
                "ID_GENRE" => array("model" => $model_groupegenres),
                "ID_TYPE" => array("model" => $model_groupetypes),
                "ID_DOSSIERNATURE" => array("model" => $model_groupenatures)
            );

            foreach ($this->_request->droits as $id_groupe => $droits) {

                // On cherche le groupe correspondant
                $row_groupe = $model_groupes->find($id_groupe)->current();

                // On supprime les checkbox
                $row_groupe->DROITADMINSYS_GROUPE = 0;
                $row_groupe->DROITADMINPREV_GROUPE = 0;
                $row_groupe->DROITADMINCOMMISSION_GROUPE = 0;
                $row_groupe->DROITFILACTU_GROUPE = 0;
                $row_groupe->DROITDOSSCREATION_GROUPE= 0;
                $row_groupe->DROITETSCREATION_GROUPE = 0;

                // Données dans la table groupe
                $row_groupe->setFromArray(array_intersect_key($droits, $model_groupes->info('metadata')))->save();

                // On supprime ce qu'il se trouve dans les porteuses
                $model_groupegenres->delete("ID_GROUPE = " .  $id_groupe);
                $model_groupenatures->delete("ID_GROUPE = " .  $id_groupe);
                $model_groupetypes->delete("ID_GROUPE = " .  $id_groupe);

                // Tables dépendantes
                foreach ($correspondances as $key => $porteuse) {

                    if (isset($droits[$key])) {

                        foreach ($droits[$key] as $primary => $data) {

                            if ($data != null) {

                                $row = $porteuse["model"]->createRow();
                                $row->ID_GROUPE = $id_groupe;
                                $row->$key = $primary;
                                
                                if (is_array($data)) {
                                    // Zend_Debug::Dump(array_intersect_key($data, $porteuse["model"]->info('metadata')));
                                    $row->setFromArray(array_intersect_key($data, $porteuse["model"]->info('metadata')))->save();
                                }

                                
                                $row->save();
                            }
                        }
                    }
                }
            }
        }
    }
