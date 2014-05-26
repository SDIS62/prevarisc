<?php

class Plugin_ACL extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // Si l'utilisateur n'est pas connecté, alors on le redirige vers la page de login (si il ne s'y trouve pas encore)
        if ( !Zend_Auth::getInstance()->hasIdentity() && $request->getActionName() != "login" && $request->getActionName() != "error" )  {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->gotoSimple('login', 'session', 'default');
        }
        elseif(Zend_Auth::getInstance()->hasIdentity()) {

            $service_user = new Service_User;

            // On update la dernière action effectuée par l'utilisateur
            $service_user->updateLastActionDate(Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR']);

            // Chargement du cache
            $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');

            // Chargement des ACL
            if(($acl = unserialize($cache->load('acl'))) === false) {

                // Liste des ressources
                $resources_dbtable = new Model_DbTable_Resource;
                $privileges_dbtable = new Model_DbTable_Privilege;
                $groupes_dbtable = new Model_DbTable_Groupe;

                // Création de l'ACL (sans les établissements et les dossiers)
                $acl = new Zend_Acl();

                // On assigne les roles, ressources et privilèges à l'ACL
                foreach($groupes_dbtable->fetchAll() as $role) {

                    if(!$acl->hasRole($role->LIBELLE_GROUPE)) {
                        $acl->addRole($role->LIBELLE_GROUPE);
                    }

                    foreach($resources_dbtable->fetchAll()->toArray() as $resource) {

                        if(explode('_', $resource['name'])[0] == 'etablissement') {
                            continue;
                        }

                        if(!$acl->has($resource['name'])) {
                            $acl->add(new Zend_Acl_Resource($resource['name']));
                        }

                        $privileges = $privileges_dbtable->fetchAll('id_resource = ' . $resource['id_resource'] )->toArray();
                        $privileges_role = $role->findModel_DbTable_PrivilegeViaModel_DbTable_GroupePrivilege()->toArray();
                        array_walk($privileges_role, function(&$val, $key) use(&$privileges_role){ $val = $privileges_role[$key]['id_privilege']; });

                        foreach($privileges as $privilege) {
                            if(in_array($privilege['id_privilege'], $privileges_role)) {
                                $acl->allow($role->LIBELLE_GROUPE, $resource['name'], $privilege['name']);
                            }
                            else {
                                $acl->deny($role->LIBELLE_GROUPE, $resource['name'], $privilege['name']);
                            }
                        }
                    }

                }

                // Sauvegarde en cache
                $cache->save(serialize($acl));
            }

            // On adapte les ressources en fonction de l'utilisateur pour la page établissement
            if($request->getControllerName() == 'etablissement' || $request->getControllerName() == 'dossier') {

                // Liste des ressources
                $resources_dbtable = new Model_DbTable_Resource;
                $privileges_dbtable = new Model_DbTable_Privilege;
                $groupes_dbtable = new Model_DbTable_Groupe;

                $groupements = (array) Zend_Auth::getInstance()->getIdentity()['groupements'];
                array_walk($groupements, function(&$val, $key) use(&$groupements){ $val = $groupements[$key]['ID_GROUPEMENT']; });
                $groupements = implode('-', $groupements);

                $commissions = (array) Zend_Auth::getInstance()->getIdentity()['commissions'];
                array_walk($commissions, function(&$val, $key) use(&$commissions){ $val = $commissions[$key]['ID_COMMISSION']; });
                $commissions = implode('-', $commissions);

                // On ajoute les ressources spécialisées
                foreach($resources_dbtable->fetchAll()->toArray() as $resource) {
                    if(explode('_', $resource['name'])[0] == 'etablissement') {

                        $resource_exploded = explode('_', $resource['name']);

                        switch($resource_exploded[1]) {
                            case 'erp':
                                if($resource_exploded[4] == '1') $resource_exploded[4] = $commissions;
                                if($resource_exploded[5] == '1') $resource_exploded[5] = $groupements;
                                if($resource_exploded[6] == '1') $resource_exploded[6] = Zend_Auth::getInstance()->getIdentity()['NUMINSEE_COMMUNE'];
                                break;
                            case 'hab':
                                if($resource_exploded[3] == '1') $resource_exploded[3] = $groupements;
                                if($resource_exploded[4] == '1') $resource_exploded[4] = Zend_Auth::getInstance()->getIdentity()['NUMINSEE_COMMUNE'];
                                break;
                            case 'igh':
                                if($resource_exploded[3] == '1') $resource_exploded[3] = $commissions;
                                if($resource_exploded[4] == '1') $resource_exploded[4] = $groupements;
                                if($resource_exploded[5] == '1') $resource_exploded[5] = Zend_Auth::getInstance()->getIdentity()['NUMINSEE_COMMUNE'];
                                break;
                            case 'eic':
                                if($resource_exploded[2] == '1') $resource_exploded[2] = $groupements;
                                if($resource_exploded[3] == '1') $resource_exploded[3] = Zend_Auth::getInstance()->getIdentity()['NUMINSEE_COMMUNE'];
                                break;
                        }

                        $resource_imploded = implode($resource_exploded, '_');
                        $list_resources_finale = array($resource_imploded);

                        foreach($this->develop_resources($list_resources_finale) as $r) {
                            if(!$acl->has($r)) {
                                $acl->add(new Zend_Acl_Resource($r));
                            }
                        }
                    }
                    else {
                        continue;
                    }

                    $privileges = $privileges_dbtable->fetchAll('id_resource = ' . $resource['id_resource'] )->toArray();
                    $privileges_role = $groupes_dbtable->find(Zend_Auth::getInstance()->getIdentity()['ID_GROUPE'])->current()->findModel_DbTable_PrivilegeViaModel_DbTable_GroupePrivilege()->toArray();
                    array_walk($privileges_role, function(&$val, $key) use(&$privileges_role){ $val = $privileges_role[$key]['id_privilege']; });

                    foreach($list_resources_finale as $resource_finale) {
                        foreach($privileges as $privilege) {
                            if(in_array($privilege['id_privilege'], $privileges_role)) {
                                $acl->allow(Zend_Auth::getInstance()->getIdentity()['group']['LIBELLE_GROUPE'], $resource_finale, $privilege['name']);
                            }
                            else {
                                $acl->deny(Zend_Auth::getInstance()->getIdentity()['group']['LIBELLE_GROUPE'], $resource_finale, $privilege['name']);
                            }
                        }
                    }
                }
            }

            $role = Zend_Auth::getInstance()->getIdentity()['group']['LIBELLE_GROUPE'];

            // Récupération de la vue
            $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');

            // Récupération de la page active
            $page = $view->navigation($view->nav)->findActive($view->navigation($view->nav)->getContainer());

            // Si on trouve une page active
            if($page != null) {

                $page = $page['page'];

                if($page->getAction() == null) {
                    $page = $view->navigation($view->nav)->findOneBy('active', true)->findByAction($request->getActionName());
                }

                // Si la page correspond bien, on check l'ACL
                if($page !== null) {

                    // Récupération de la resource demandée par la page active
                    $resources = $this->getPageResources($page, $request);

                    // Récupération du privilège demandé par la page active
                    $privilege = $this->getPagePrivilege($page);

                    // Si il n'y a pas de privilèges associés à la page, on skip la procédure de controle
                    if($privilege != null) {

                        // Pour chaque ressources de la page, on check les permissions
                        $access_granted = false;

                        if($page->get('controller') == 'etablissement') {
                            foreach($resources as $resource) {
                                if($acl->has($resource) && $acl->isAllowed($role, $resource,  $privilege)) {
                                    $access_granted = true;
                                }
                            }
                        }
                        elseif($page->get('controller') == 'dossier') {
                            if($page->get('action') !== 'add' && $page->get('action') !== 'savenew') {
                                $access_granted_ets = false;
                                $i = 0;
                                foreach($resources as $resource) {
                                    if(explode('_', $resource)[0] == 'etablissement') {
                                        if($acl->has($resource) && $acl->isAllowed($role, $resource,  'view_ets')) {
                                            $access_granted_ets = true;
                                        }
                                        $i++;
                                    }
                                }
                                if($access_granted_ets || $i == 0) {
                                    foreach($resources as $resource) {
                                        if((explode('_', $resource)[0] == 'dossier' || explode('_', $resource)[0] == 'creations') && $acl->has($resource) && $acl->isAllowed($role, $resource, $privilege)) {
                                            $access_granted = true;
                                        }
                                    }
                                }
                            }
                            else {
                                if($acl->isAllowed($role, 'creations', 'add_dossier')) {
                                    $access_granted = true;
                                }
                            }
                        }
                        else {
                            foreach($resources as $resource) {
                                if($acl->has($resource)) {
                                    if($acl->isAllowed($role, $resource,  $privilege)) {
                                        $access_granted = true;
                                    }
                                }
                                else {
                                    $access_granted = true;
                                }
                            }
                        }

                        // Redirection vers la page d'erreur si l'accès est non autorisée
                        if(!$access_granted) {
                            $request->setControllerName('error');
                            $request->setActionName('error');
                            $error = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
                            $error->type = Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER;
                            $error->request = clone $request;
                            $error->exception = new Zend_Controller_Dispatcher_Exception('Accès non autorisé', 401);
                            $request->setParam('error_handler', $error);
                        }
                    }
                }
            }
        }
    }

    /**
     * Develop resources
     *
     * @param  $list_resources_finale
     * @return null|Zend_Acl_Resource_Interface
     */
    private function develop_resources(&$list_resources_finale) {
        for($i = 0; $i < count($list_resources_finale); $i++) {
            $resource_exploded = explode('_', $list_resources_finale[$i]);
            for($j = 0; $j < count($resource_exploded); $j++) {
                if(count(explode('-', $resource_exploded[$j])) > 1) {
                    $resource_exploded2 = explode('-', $resource_exploded[$j]);
                    for($k = 0; $k < count($resource_exploded2); $k++) {
                        $name = explode('_', $list_resources_finale[$i]);
                        $name[$j] = $resource_exploded2[$k];
                        $list_resources_finale[] = implode($name, '_');
                    }
                    unset($list_resources_finale[$i]);
                    $list_resources_finale = array_unique($list_resources_finale);
                    $list_resources_finale = array_values($list_resources_finale);
                    $this->develop_resources($list_resources_finale);
                }
            }
        }

        return array_unique($list_resources_finale);
    }

    /**
     * getPageResources
     *
     * @param  $page
     * @return null|array
     */
    private function getPageResources($page, $request = null)
    {
        if($page !== null) {
            if($page->get('controller') == 'etablissement') {
                if($page->getResource() === null && $request != null) {
                   return $this->getEtablissementPageResourses($request->getParam('id'));
                }
                else {
                    return array($page->getResource());
                }
            }
            elseif($page->get('controller') == 'dossier') {
                if($page->getResource() === null && $request != null) {
                    $model_dossier = new Model_DbTable_Dossier;
                    $id_dossier = $request->getParam('id');
                    $dossier_nature = $model_dossier->getNatureDossier($id_dossier);
                    $etablissements = $model_dossier->getEtablissementDossier($id_dossier);
                    $resources = array();
                    if(count((array) $etablissements) > 0) {
                        foreach($etablissements as $etablissement) {
                            $resources = array_merge($resources, $this->getEtablissementPageResourses($etablissement['ID_ETABLISSEMENT']));
                        }
                    }
                    $resources[] = 'dossier_' . $dossier_nature['ID_NATURE'];
                    $resources[] = 'dossier_0';


                    return $resources;
                }
                else {
                    return array($page->getResource());
                }
            }
            else {
                return $page->getResource() === null ? $page->getParent() instanceof Zend_Navigation_Page ? $this->getPageResources($page->getParent(), $request) : array(null) : array($page->getResource());
            }
        }
        else {
            return array(null);
        }
    }

    /**
     * getPagePrivilege
     *
     * @param  $page
     * @return null|string
     */
    private function getPagePrivilege($page)
    {
        if($page !== null) {
            return $page->getPrivilege() === null ? $page->getParent() instanceof Zend_Navigation_Page ? $this->getPagePrivilege($page->getParent()) : null : $page->getPrivilege();
        }
        else {
            return null;
        }
    }

    /**
     * getEtablissementPageResourses
     *
     * @param  $id_etablissement
     * @return null|array
     */
    private function getEtablissementPageResourses($id_etablissement)
    {
        $service_etablissement = new Service_Etablissement;
        $service_groupement_communes = new Service_GroupementCommunes;

        $etablissement = $service_etablissement->get($id_etablissement);

        if(count($etablissement['adresses']) > 0) {
            $groupements = $service_groupement_communes->findAll($etablissement['adresses'][0]["NUMINSEE_COMMUNE"]);
            array_walk($groupements, function(&$val, $key) use(&$array){$val = $val['ID_GROUPEMENT'];});
            $groupements = implode('-', $groupements);
            $groupements .= '-0';

            $communes = $etablissement['adresses'];
            array_walk($communes, function(&$val, $key) use(&$array){$val = $val['NUMINSEE_COMMUNE'];});
            $communes = implode('-', $communes);
            $communes .= '-0';
        }
        else {
            $groupements = '0';
            $communes = '0';
        }

        $resource = '';

        switch($etablissement['informations']['ID_GENRE']) {
            case '1':
                $resource = array();
                foreach($etablissement['etablissement_lies'] as $etablissements_enfant) {
                    $resource = array_merge($resource, $this->getEtablissementPageResourses($etablissements_enfant['ID_ETABLISSEMENT']));
                }
                break;

            case '2':
                $resource = 'etablissement_erp_';
                $resource .= ($etablissement['informations']['ID_TYPEACTIVITE'] == null ? '0' : $etablissement['informations']['ID_TYPEACTIVITE'] . '-0') . '_';
                $resource .= ($etablissement['informations']['ID_CATEGORIE'] == null ? '0' : $etablissement['informations']['ID_CATEGORIE'] . '-0') . '_';
                $resource .= ($etablissement['informations']['ID_COMMISSION'] == null ? '0' : $etablissement['informations']['ID_COMMISSION'] . '-0') . '_';
                $resource .= $groupements . '_';
                $resource .= $communes;
                break;

            case '3':
                $resource = 'etablissement_cell_';
                $resource .= ($etablissement['informations']['ID_TYPEACTIVITE'] == null ? '0' : $etablissement['informations']['ID_TYPEACTIVITE'] . '-0') . '_';
                $resource .= ($etablissement['informations']['ID_CATEGORIE'] == null ? '0' : $etablissement['informations']['ID_CATEGORIE'] . '-0');
                break;

            case '4':
                $resource = 'etablissement_hab_';
                $resource .= ($etablissement['informations']['ID_FAMILLE'] == null ? '0' : $etablissement['informations']['ID_FAMILLE'] . '-0') . '_';
                $resource .= $groupements . '_';
                $resource .= $communes;
                break;

            case '5':
                $resource = 'etablissement_igh_';
                $resource .= ($etablissement['informations']['ID_CLASSE'] == null ? '0' : $etablissement['informations']['ID_CLASSE'] . '-0') . '_';
                $resource .= ($etablissement['informations']['ID_COMMISSION'] == null ? '0' : $etablissement['informations']['ID_COMMISSION'] . '-0') . '_';
                $resource .= $groupements . '_';
                $resource .= $communes;
                break;

            case '6':
                $resource = 'etablissement_eic_';
                $resource .= $groupements . '_';
                $resource .= $communes;
                break;
        }

        $list_resources_finale = is_array($resource) ? $resource : array($resource);
        $this->develop_resources($list_resources_finale);
        return $list_resources_finale;
    }
}
