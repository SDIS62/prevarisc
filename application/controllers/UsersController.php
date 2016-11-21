<?php

class UsersController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        $service_user = new Service_User;
        $service_search = new Service_Search;

        $this->view->users = $service_search->users(null, null, $this->hasParam('gid') ? $this->_request->getParam('gid') : null, true, 1000)['results'];
        $this->view->inactives_users = $service_search->users(null, null, $this->hasParam('gid') ? $this->_request->getParam('gid') : null, false, 1000)['results'];

        $this->view->groupes = $service_user->getAllGroupes();
    }

    public function editAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        $service_user = new Service_User;
        $service_groupement = new Service_GroupementCommunes;
        $service_commission = new Service_Commission;
        $service_adresse = new Service_Adresse;

        $this->view->user = $service_user->find($this->_request->getParam('uid'));
        $this->view->commissions = $service_commission->getAll();
        $this->view->groupements = $service_groupement->findGroupementAndGroupementType();
        $this->view->fonctions = $service_user->getAllFonctions();
        $this->view->communes = $service_adresse->getAllCommunes();
        $this->view->groupes = $service_user->getAllGroupes();
        $this->view->params = array("LDAP_ACTIF" => getenv('PREVARISC_LDAP_ENABLED') 
                                                 || getenv('PREVARISC_NTLM_ENABLED') 
                                                 || getenv('PREVARISC_CAS_ENABLED'));

        $this->view->add = false;

        if($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $service_user->save($post, $_FILES['avatar'], $this->_request->uid);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'L\'utilisateur a bien été mis à jour.'));
                $this->_helper->redirector('index', null, null);
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => '','message' => 'L\'utilisateur n\'a pas été mis à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }
    }

    public function addAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        $service_user = new Service_User;
        $service_groupement = new Service_GroupementCommunes;
        $service_commission = new Service_Commission;
        $service_adresse = new Service_Adresse;

        $this->view->commissions = $service_commission->getAll();
        $this->view->groupements = $service_groupement->findGroupementAndGroupementType();
        $this->view->fonctions = $service_user->getAllFonctions();
        $this->view->communes = $service_adresse->getAllCommunes();
        $this->view->groupes = $service_user->getAllGroupes();
        $this->view->params = array("LDAP_ACTIF" => getenv('PREVARISC_LDAP_ENABLED') 
                                                 || getenv('PREVARISC_NTLM_ENABLED') 
                                                 || getenv('PREVARISC_CAS_ENABLED'));

        $this->view->add = true;

        if($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $service_user->save($post, $_FILES['avatar']);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'L\'utilisateur a bien été ajouté.'));
                $this->_helper->redirector('index', null, null);
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => '','message' => 'L\'utilisateur n\'a pas été ajouté. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }

        $this->render('edit');
    }

    public function matriceDesDroitsAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        // Modèles
        $model_groupes = new Model_DbTable_Groupe;
        $model_resource = new Model_DbTable_Resource;
        $model_groupes_privilege = new Model_DbTable_GroupePrivilege;

        // On envoit les données sur la vue
        $this->view->rowset_groupes = $model_groupes->fetchAll();
        $this->view->rowset_resources = $model_resource->fetchAll();
        $this->view->rowset_groupes_privilege = $model_groupes_privilege->fetchAll()->toArray();

        // Si des données sont envoyées, on procède à leur traitement
        if ($this->_request->isPost()) {
            try {
                foreach ($this->_request->getParam('groupe') as $id_groupe => $privileges) {
                    foreach ($privileges as $id_privilege => $value_privilege) {
                        $groupe_privilege_exists = $model_groupes_privilege->find($id_groupe, $id_privilege)->current() !== null;

                        if ($value_privilege == 1 && !$groupe_privilege_exists) {
                            $row_groupe_priv = $model_groupes_privilege->createRow();
                            $row_groupe_priv->ID_GROUPE = $id_groupe;
                            $row_groupe_priv->id_privilege = $id_privilege;
                            $row_groupe_priv->save();
                        }

                        if ($value_privilege == 0 && $groupe_privilege_exists) {
                            $model_groupes_privilege->delete('ID_GROUPE = ' . $id_groupe . ' AND id_privilege = ' . $id_privilege);
                        }
                    }
                }
                $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
                $cache->remove('acl');

                $this->_helper->flashMessenger(array(
                    'context' => 'success',
                    'title' => 'Mise à jour réussie !',
                    'message' => 'La matrice des droits a bien été mise à jour.'
                ));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array(
                    'context' => 'error',
                    'title' => 'Aie',
                    'message' => $e->getMessage()
                ));
            }
            
            // Redirection
            $this->_helper->redirector('matrice-des-droits');
        }
    }

    public function editGroupAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        $service_user = new Service_User;

        $this->view->group = $service_user->getGroup($this->_request->gid);

        $this->view->add = false;

        if($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $service_user->saveGroup($post, $this->_request->gid);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'Le groupe a bien été mis à jour.'));
                $this->_helper->redirector('index', null, null);
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => '','message' => 'Le groupe n\'a pas été mis à jour. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }
    }

    public function addGroupAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        $service_user = new Service_User;

        $this->view->add = true;

        if($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $service_user->saveGroup($post);
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Mise à jour réussie !', 'message' => 'Le groupe a bien été ajouté.'));
                $this->_helper->redirector('index', null, null);
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => '','message' => 'Le groupe n\'a pas été ajouté. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }

        $this->render('edit-group');
    }

    public function deleteGroupAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        $service_user = new Service_User;

        $this->view->add = true;

        try {
            $post = $this->_request->getPost();
            $service_user->deleteGroup($this->_request->gid);
            $this->_helper->flashMessenger(array('context' => 'success','title' => 'Suppression réussie !','message' => 'Le groupe a été supprimé.'));
        }
        catch(Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'error','title' => '','message' => 'Erreur dans la suppression du groupe. Veuillez rééssayez. (' . $e->getMessage() . ')'));
        }

        $this->_helper->redirector('index', null, null);
    }

    public function ressourcesSpecialiseesAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        $service_categorie = new Service_Categorie;
        $service_type = new Service_TypeActivite;
        $service_dossier = new Service_Dossier;
        $service_genre = new Service_Genre;
        $service_famille = new Service_Famille;
        $service_classe = new Service_Classe;

        $model_resource = new Model_DbTable_Resource;

        // On met le libellé du type dans le tableau des activités
        $types = $service_type->getAllWithTypes();
        $types_sort = array();

        foreach ($types as $_type) {
            $types_sort[$_type['ID_TYPE']] = $_type;
        }

        $type_sort = array();

        foreach ($types as $type) {
            if (!array_key_exists($types_sort[$type["ID_TYPE"]]['LIBELLE_TYPE'], $type_sort)) {
                $type_sort[$types_sort[$type["ID_TYPE"]]['LIBELLE_TYPE']] = array();
            }

            $type_sort[$types_sort[$type["ID_TYPE"]]['LIBELLE_TYPE']][] = $type;
        }

        $this->view->categories = $service_categorie->getAll();
        $this->view->types = $type_sort;
        $this->view->types_dossier = $service_dossier->getAllTypes();
        $this->view->natures_dossier = $service_dossier->getAllNatures();
        $this->view->genres = $service_genre->getAll();
        unset($this->view->genres[0]);
        $this->view->resources = $model_resource->fetchAll();
        $this->view->familles = $service_famille->getAll();
        $this->view->classements = $service_genre->getClassements();
        $this->view->classes = $service_classe->getAll();
    }

    public function addRessourceSpecialiseeAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $model_resource = new Model_DbTable_Resource;
        $model_privilege = new Model_DbTable_Privilege;

        if($this->_request->isPost()) {
            try {

                $name = $text = '';

                switch($this->_request->type_ressource) {
                    case 'etablissement':

                        switch($this->_request->genre) {
                            case '2':
                                $name = 'etablissement_erp_';
                                $name .= (is_array($this->_request->types) ? implode($this->_request->types, '-') : '0') . '_';
                                $name .= (is_array($this->_request->categories) ? implode($this->_request->categories, '-') : '0') . '_';
                                $name .= $this->_request->commissions . '_';
                                $name .= $this->_request->groupements . '_';
                                $name .= $this->_request->commune;

                                if(is_array($this->_request->types)) {
                                    $array = $this->_request->types;
                                    array_walk($array, function(&$val, $key) use(&$array){
                                        $service_type = new Service_TypeActivite;
                                        $tmp_types = $service_type->getAll();
                                        $types = array();
                                        foreach($tmp_types as $t) {
                                            $types[$t['ID_TYPEACTIVITE']] = $t['LIBELLE_ACTIVITE'];
                                        }
                                        $array[$key] = $types[$val];
                                    });
                                }

                                $text = 'Établissement (';
                                $text .= (is_array($this->_request->types) ? 'Types ' . implode($array, '-') : 'Tous les types') . ' - ';
                                $text .= (is_array($this->_request->categories) ? 'Catégories ' . implode($this->_request->categories, '-') : 'Toutes les catégories') . ' - ';
                                $text .= ($this->_request->commissions == 0 ? 'Ignorer les commissions' : 'Sur les commissions de l\'utilisateur') . ' - ';
                                $text .= ($this->_request->groupements == 0 ? 'Ignorer les groupements' : 'Sur les groupements de l\'utilisateur') . ' - ';
                                $text .= ($this->_request->commune == 0 ? 'Ignorer la commune' : 'Sur la commune de l\'utilisateur');
                                $text .= ')';
                                break;

                            case '3':
                                $name = 'etablissement_cell_';
                                $name .= (is_array($this->_request->types) ? implode($this->_request->types, '-') : '0') . '_';
                                $name .= (is_array($this->_request->categories) ? implode($this->_request->categories, '-') : '0');

                                if(is_array($this->_request->types)) {
                                    $array = $this->_request->types;
                                    array_walk($array, function(&$val, $key) use(&$array){
                                        $service_type = new Service_TypeActivite;
                                        $tmp_types = $service_type->getAll();
                                        $types = array();
                                        foreach($tmp_types as $t) {
                                            $types[$t['ID_TYPEACTIVITE']] = $t['LIBELLE_ACTIVITE'];
                                        }
                                        $array[$key] = $types[$val];
                                    });
                                }

                                $text = 'Cellule (';
                                $text .= (is_array($this->_request->types) ? 'Types ' . implode($array, '-') : 'Tous les types') . ' - ';
                                $text .= (is_array($this->_request->categories) ? 'Catégories ' . implode($this->_request->categories, '-') : 'Toutes les catégories');
                                $text .= ')';
                                break;

                            case '4':
                                $name = 'etablissement_hab_';
                                $name .= (is_array($this->_request->familles) ? implode($this->_request->familles, '-') : '0') . '_';
                                $name .= $this->_request->groupements . '_';
                                $name .= $this->_request->commune;

                                if(is_array($this->_request->familles)) {
                                    $array = $this->_request->familles;
                                    array_walk($array, function(&$val, $key) use(&$array){
                                        $service_famille = new Service_Famille;
                                        $tmp_familles = $service_famille->getAll();
                                        $familles = array();
                                        foreach($tmp_familles as $t) {
                                            $types[$t['ID_FAMILLE']] = $t['LIBELLE_FAMILLE'];
                                        }
                                        $array[$key] = $familles[$val];
                                    });
                                }

                                $text = 'Habitation (';
                                $text .= (is_array($this->_request->familles) ? 'Familles ' . implode($array, '-') : 'Toutes les familles') . ' - ';
                                $text .= ($this->_request->groupements == 0 ? 'Ignorer les groupements' : 'Sur les groupements de l\'utilisateur') . ' - ';
                                $text .= ($this->_request->commune == 0 ? 'Ignorer la commune' : 'Sur la commune de l\'utilisateur');
                                $text .= ')';
                                break;

                            case '5':
                                $name = 'etablissement_igh_';
                                $name .= (is_array($this->_request->classes) ? implode($this->_request->classes, '-') : '0') . '_';
                                $name .= $this->_request->commissions . '_';
                                $name .= $this->_request->groupements . '_';
                                $name .= $this->_request->commune;

                                if(is_array($this->_request->classes)) {
                                    $array = $this->_request->classes;
                                    array_walk($array, function(&$val, $key) use(&$array){
                                        $service_classe = new Service_Classe;
                                        $tmp_classes = $service_classe->getAll();
                                        $classes = array();
                                        foreach($tmp_classes as $t) {
                                            $classes[$t['ID_CLASSE']] = $t['LIBELLE_CLASSE'];
                                        }
                                        $array[$key] = $classes[$val];
                                    });
                                }

                                $text = 'IGH (';
                                $text .= (is_array($this->_request->classes) ? 'Classes ' . implode($array, '-') : 'Toutes les classes') . ' - ';
                                $text .= ($this->_request->commissions == 0 ? 'Ignorer les commissions' : 'Sur les commissions de l\'utilisateur') . ' - ';
                                $text .= ($this->_request->groupements == 0 ? 'Ignorer les groupements' : 'Sur les groupements de l\'utilisateur') . ' - ';
                                $text .= ($this->_request->commune == 0 ? 'Ignorer la commune' : 'Sur la commune de l\'utilisateur');
                                $text .= ')';
                                break;

                            case '6':
                                $name = 'etablissement_eic_';
                                $name .= $this->_request->groupements . '_';
                                $name .= $this->_request->commune;

                                $text = 'EIC (';
                                $text .= ($this->_request->groupements == 0 ? 'Ignorer les groupements' : 'Sur les groupements de l\'utilisateur') . ' - ';
                                $text .= ($this->_request->commune == 0 ? 'Ignorer la commune' : 'Sur la commune de l\'utilisateur');
                                $text .= ')';
                                break;
                            
                            case '7':
                                $name = 'etablissement_camp_';
                                $name .= $this->_request->groupements . '_';
                                $name .= $this->_request->commune;
                                
                                $text = 'Camping (';
                                $text .= ($this->_request->groupements == 0 ? 'Ignorer les groupements' : 'Sur les groupements de l\'utilisateur') . ' - ';
                                $text .= ($this->_request->commune == 0 ? 'Ignorer la commune' : 'Sur la commune de l\'utilisateur');
                                $text .= ')';
                                break;
                            
                            case '8':
                                $name = 'etablissement_temp_';
                                $name .= $this->_request->groupements . '_';
                                $name .= $this->_request->commune;
                                
                                $text = 'Manifestation temporaire (';
                                $text .= ($this->_request->groupements == 0 ? 'Ignorer les groupements' : 'Sur les groupements de l\'utilisateur') . ' - ';
                                $text .= ($this->_request->commune == 0 ? 'Ignorer la commune' : 'Sur la commune de l\'utilisateur');
                                $text .= ')';
                                break;
                            
                            case '9':
                                $name = 'etablissement_iop_';
                                $name .= $this->_request->groupements . '_';
                                $name .= $this->_request->commune;
                                
                                $text = 'IOP (';
                                $text .= ($this->_request->groupements == 0 ? 'Ignorer les groupements' : 'Sur les groupements de l\'utilisateur') . ' - ';
                                $text .= ($this->_request->commune == 0 ? 'Ignorer la commune' : 'Sur la commune de l\'utilisateur');
                                $text .= ')';
                                break;
                            
                            case '10':
                                $name = 'etablissement_zone_';
                                $name .= (is_array($this->_request->classements) ? implode($this->_request->classements, '-') : '0') . '_';
                                $name .= $this->_request->groupements . '_';
                                $name .= $this->_request->commune;
                                
                                if(is_array($this->_request->classements)) {
                                    $array = $this->_request->classements;
                                    array_walk($array, function(&$val, $key) use(&$array){
                                        $service_genre = new Service_Genre;
                                        $tmp_classement = $service_genre->getClassements();
                                        $classement = array();
                                        foreach($tmp_classement as $t) {
                                            $classement[$t['ID_CLASSEMENT']] = $t['LIBELLE_CLASSEMENT'];
                                        }
                                        $array[$key] = $classement[$val];
                                    });
                                }
                                
                                $text = 'Zone (';
                                $text .= (is_array($this->_request->classements) ? 'Classes ' . implode($array, '-') : 'Tous les classements') . ' - ';
                                $text .= ($this->_request->groupements == 0 ? 'Ignorer les groupements' : 'Sur les groupements de l\'utilisateur') . ' - ';
                                $text .= ($this->_request->commune == 0 ? 'Ignorer la commune' : 'Sur la commune de l\'utilisateur');
                                $text .= ')';
                                break;
                        }

                        $id_resource = $model_resource->createRow(array('name' => $name, 'text' => $this->_request->text == '' ? $text : $this->_request->text))->save();
                        $model_privilege->createRow(array('name' => 'view_ets', 'text' => 'Lecture', 'id_resource' => $id_resource))->save();
                        $model_privilege->createRow(array('name' => 'edit_ets', 'text' => 'Modifier', 'id_resource' => $id_resource))->save();

                        break;

                    case 'dossier':
                        $name = 'dossier_';
                        $name .= (is_array($this->_request->dossier_natures) ? implode($this->_request->dossier_natures, '-') : '0');

                        if(is_array($this->_request->dossier_natures)) {
                            $array = $this->_request->dossier_natures;
                            array_walk($array, function(&$val, $key) use(&$array){
                                $service_dossier = new Service_Dossier;
                                $tmp_natures = $service_dossier->getAllNatures();
                                $natures = array();
                                foreach($tmp_natures as $n) {
                                    $natures[$n['ID_DOSSIERNATURE']] = $n['LIBELLE_DOSSIERNATURE'];
                                }
                                $array[$key] = $natures[$val];
                            });
                        }

                        $text = 'Dossier (';
                        $text .= (is_array($this->_request->dossier_natures) ? 'Natures ' . implode($array, '-') : 'Toutes les natures');
                        $text .= ')';

                        $id_resource = $model_resource->createRow(array('name' => $name, 'text' => $this->_request->text == '' ? $text : $this->_request->text))->save();
                        $model_privilege->createRow(array('name' => 'view_doss', 'text' => 'Lecture', 'id_resource' => $id_resource))->save();
                        $model_privilege->createRow(array('name' => 'edit_doss', 'text' => 'Modifier', 'id_resource' => $id_resource))->save();
                        $model_privilege->createRow(array('name' => 'verrouillage_dossier', 'text' => 'Verrouillage d\'un dossier', 'id_resource' => $id_resource))->save();

                        break;
                }

                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Ajout réussi !', 'message' => 'La ressource a bien été ajoutée.'));
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error', 'title' => 'Ajout annulé', 'message' => 'La ressource n\'a été ajoutée. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector('ressources-specialisees');
        }
    }

    public function deleteRessourceSpecialiseeAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $model_resource = new Model_DbTable_Resource;

        if($this->_request->isGet()) {
            try {
                $model_resource->find($this->_request->id)->current()->delete();
                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Suppression réussie !', 'message' => 'La ressource a bien été supprimée.'));
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error', 'title' => 'Suppression annulée', 'message' => 'La ressource n\'a été supprimée. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }

            $this->_helper->redirector('ressources-specialisees');
        }
    }
}
