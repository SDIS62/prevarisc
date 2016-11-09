<?php

class Plugin_ACL extends Zend_Controller_Plugin_Abstract
{
    private static $acl = null;

    public static function getAcl() {

        if (self::$acl == null) {
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

                    $privileges_role = $role->findModel_DbTable_PrivilegeViaModel_DbTable_GroupePrivilege()->toArray();
                    array_walk($privileges_role, function(&$val, $key) use(&$privileges_role){ $val = $privileges_role[$key]['id_privilege']; });

                    foreach($resources_dbtable->fetchAll()->toArray() as $resource) {

                        if(explode('_', $resource['name'])[0] == 'etablissement') {
                            continue;
                        }

                        if(!$acl->has($resource['name'])) {
                            $acl->add(new Zend_Acl_Resource($resource['name']));
                        }

                        $privileges = $privileges_dbtable->fetchAll('id_resource = ' . $resource['id_resource'] )->toArray();

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
            self::$acl = $acl;
        }

        return self::$acl;
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // Si l'utilisateur est connecté avec l'application mobile, on utilise le partage d'un token
        if($request->getParam('key') === getenv('PREVARISC_SECURITY_KEY')) {
            return ;
        }
        
        if (getenv('PREVARISC_CAS_ENABLED') == 1) {
                
            if (getenv('PREVARISC_DEBUG_ENABLED') == 1) {
                // Enable debugging
                phpCAS::setDebug();
                // Enable verbose error messages. Disable in production!
                phpCAS::setVerbose(true);
            }

            // Initialize phpCAS
            if (!phpCAS::isInitialized()) {
                phpCAS::client(getenv('PREVARISC_CAS_VERSION') ?  : CAS_VERSION_2_0, getenv('PREVARISC_CAS_HOST'), (int) getenv('PREVARISC_CAS_PORT'), getenv('PREVARISC_CAS_CONTEXT'), false);

                 phpCAS::setLang(PHPCAS_LANG_FRENCH);
            }
            if (getenv('PREVARISC_CAS_NO_SERVER_VALIDATION') == 1) {
                phpCAS::setNoCasServerValidation();
            }

            // force CAS authentication
            phpCAS::forceAuthentication();
        }

        // Si l'utilisateur n'est pas connecté, alors on le redirige vers la page de login (si il ne s'y trouve pas encore)
        if ( !Zend_Auth::getInstance()->hasIdentity() && !in_array($request->getActionName(), array("login", "error")))  {
            $redirect = $_SERVER['REQUEST_URI'] == '/' ? [] : ['redirect' => urlencode($_SERVER['REQUEST_URI'])];
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->gotoSimple('login', 'session', 'default', $redirect);
        }
        else if(Zend_Auth::getInstance()->hasIdentity()) {

            $service_user = new Service_User;

            // On update la dernière action effectuée par l'utilisateur
            $utilisateur = Zend_Auth::getInstance()->getIdentity();
            if(!$utilisateur)
            {
                $request->setControllerName('error');
                $request->setActionName('error');
                $error = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
                $error->type = Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER;
                $error->request = clone $request;
                $error->exception = new Zend_Controller_Dispatcher_Exception('Accès non autorisé', 401);
                $request->setParam('error_handler', $error);
                return ;
            }

            $service_user->updateLastActionDate($utilisateur['ID_UTILISATEUR']);

            $acl = self::getAcl();

            // On adapte les ressources en fonction de l'utilisateur pour la page établissement
            if($request->getControllerName() == 'etablissement' || $request->getControllerName() == 'dossier') {

                // Liste des ressources
                $resources_dbtable = new Model_DbTable_Resource;
                $privileges_dbtable = new Model_DbTable_Privilege;
                $groupes_dbtable = new Model_DbTable_Groupe;

                $groupements = (array) $utilisateur['groupements'];
                array_walk($groupements, function(&$val, $key) use(&$groupements){ $val = $groupements[$key]['ID_GROUPEMENT']; });
                $groupements = implode('-', $groupements);

                $commissions = (array) $utilisateur['commissions'];
                array_walk($commissions, function(&$val, $key) use(&$commissions){ $val = $commissions[$key]['ID_COMMISSION']; });
                $commissions = implode('-', $commissions);


                $privileges_role = $groupes_dbtable->find($utilisateur['ID_GROUPE'])->current()->findModel_DbTable_PrivilegeViaModel_DbTable_GroupePrivilege()->toArray();
                array_walk($privileges_role, function(&$val, $key) use(&$privileges_role){ $val = $privileges_role[$key]['id_privilege']; });

                // On ajoute les ressources spécialisées
                foreach($resources_dbtable->fetchAll()->toArray() as $resource) {
                    if(explode('_', $resource['name'])[0] == 'etablissement') {

                        $resource_exploded = explode('_', $resource['name']);

                        switch($resource_exploded[1]) {
                            case 'erp':
                                if($resource_exploded[4] == '1') $resource_exploded[4] = $commissions;
                                if($resource_exploded[5] == '1') $resource_exploded[5] = $groupements;
                                if($resource_exploded[6] == '1') $resource_exploded[6] = $utilisateur['NUMINSEE_COMMUNE'];
                                break;
                            case 'hab':
                                if($resource_exploded[3] == '1') $resource_exploded[3] = $groupements;
                                if($resource_exploded[4] == '1') $resource_exploded[4] = $utilisateur['NUMINSEE_COMMUNE'];
                                break;
                            case 'igh':
                                if($resource_exploded[3] == '1') $resource_exploded[3] = $commissions;
                                if($resource_exploded[4] == '1') $resource_exploded[4] = $groupements;
                                if($resource_exploded[5] == '1') $resource_exploded[5] = $utilisateur['NUMINSEE_COMMUNE'];
                                break;
                            case 'eic':
                                if($resource_exploded[2] == '1') $resource_exploded[2] = $groupements;
                                if($resource_exploded[3] == '1') $resource_exploded[3] = $utilisateur['NUMINSEE_COMMUNE'];
                                break;
                            case 'camp':
                                if($resource_exploded[2] == '1') $resource_exploded[2] = $groupements;
                                if($resource_exploded[3] == '1') $resource_exploded[3] = $utilisateur['NUMINSEE_COMMUNE'];
                                break;
                            case 'temp':
                                if($resource_exploded[2] == '1') $resource_exploded[2] = $groupements;
                                if($resource_exploded[3] == '1') $resource_exploded[3] = $utilisateur['NUMINSEE_COMMUNE'];
                                break;
                            case 'iop':
                                if($resource_exploded[2] == '1') $resource_exploded[2] = $groupements;
                                if($resource_exploded[3] == '1') $resource_exploded[3] = $utilisateur['NUMINSEE_COMMUNE'];
                                break;
                            case 'zone':
                                if($resource_exploded[3] == '1') $resource_exploded[3] = $groupements;
                                if($resource_exploded[4] == '1') $resource_exploded[4] = $utilisateur['NUMINSEE_COMMUNE'];
                                break;
                        }

                        $resource_imploded = implode($resource_exploded, '_');
                        $list_resources_finale =  array($resource_imploded);

			$resources = new ResourceContainer($list_resources_finale);
                        foreach($resources as $r) {
                            if(!$acl->has($r)) {
                                $acl->add(new Zend_Acl_Resource($r));
                            }
                        }
                    }
                    else {
                        continue;
                    }

                    $privileges = $privileges_dbtable->fetchAll('id_resource = ' . $resource['id_resource'] )->toArray();

                    foreach($resources as $resource_finale) {
                        foreach($privileges as $privilege) {
                            if($acl->has($resource_finale)) {
                                if(in_array($privilege['id_privilege'], $privileges_role)) {
                                    $acl->allow($utilisateur['group']['LIBELLE_GROUPE'], $resource_finale, $privilege['name']);
                                }
                                else {
                                    $acl->deny($utilisateur['group']['LIBELLE_GROUPE'], $resource_finale, $privilege['name']);
                                }
                            }
                        }
                    }
                }
            }

            $role = $utilisateur['group']['LIBELLE_GROUPE'];

            // Récupération de la vue
            $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');

            // Récupération de la page active
            $page = $view->navigation($view->nav)->findActive($view->navigation($view->nav)->getContainer());

            try {
                // Si on trouve une page active
                if($page != null) {

                    $page = $page['page'];

                    if($page->getAction() == null) {
                        $page = $view->navigation($view->nav)->findOneBy('active', true)->findByAction($request->getActionName());
                    }

                    // Si la page correspond bien, on check l'ACL
                    if($page !== null) {

                        // Récupération de la resource demandée par la page active
                        $resources = new ResourceContainer($this->getPageResources($page, $request));

                        // Récupération du privilège demandé par la page active
                        $privilege = $this->getPagePrivilege($page);

                        // Si il n'y a pas de privilèges associés à la page, on skip la procédure de controle
                        if($privilege != null) {

                            // Pour chaque ressources de la page, on check les permissions
                            $access_granted = false;

                            if($page->get('controller') == 'etablissement') {
                                foreach($resources as $resource) {
                                    if($acl->has($resource) && $acl->isAllowed($role, $resource, $privilege)) {
                                        $access_granted = true;
                                        break;
                                    }
                                }
                                if($resources->hasNonDeveloppedResource('editsite') && $page->get('action') == 'edit') {
                                  if($acl->has("creations") && $acl->isAllowed($role, "creations", "add_etablissement")) {
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
                                                break;
                                            }
                                            $i++;
                                        }
                                    }
                                    if($access_granted_ets || $i == 0) {
                                        foreach($resources as $resource) {
                                            if((explode('_', $resource)[0] == 'dossier' || explode('_', $resource)[0] == 'creations') && $acl->has($resource) && $acl->isAllowed($role, $resource, $privilege)) {
                                                $access_granted = true;
                                                break;
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
                                            break;
                                        }
                                    }
                                    else {
                                        $access_granted = true;
                                    }
                                }
                            }

                            // Redirection vers la page d'erreur si l'accès est non autorisée
                            if(!$access_granted) {
                                if($request->getControllerName() == 'error') return;
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
            } catch(Zend_Acl_Role_Registry_Exception $acle) {
                Zend_Auth::getInstance()->clearIdentity();
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->gotoSimple('login', 'session', 'default');
            }
        }
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

                    if ($id_dossier = $request->getParam('id')) {
                        $model_dossier = new Model_DbTable_Dossier;
                        $dossier_nature = $model_dossier->getNatureDossier($id_dossier);
                        $etablissements = $model_dossier->getEtablissementDossier2($id_dossier);
                        $resources = array();
                        if(count((array) $etablissements) > 0) {
                            foreach($etablissements as $etablissement) {
                                $resources = array_merge($resources, $this->getEtablissementPageResourses($etablissement['ID_ETABLISSEMENT']));
                            }
                        }
                        $resources[] = 'dossier_' . $dossier_nature['ID_NATURE'];
                    }

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

        $groupements = array();
        $communes = array();
        $etablissements = array($id_etablissement => $etablissement);

        switch($etablissement['informations']['ID_GENRE']) {
            case '1':
                foreach($etablissement['etablissement_lies'] as $etablissements_enfant) {
                    $etablissements[$etablissements_enfant['ID_ETABLISSEMENT']]['informations'] = $etablissements_enfant;
                }
            default:
                break;
        }

        // on récupère les communes des adresses puis les groupements rattachés aux communes de ces adresses
        // pour chacun des établissement : 1 pour un ERP, 1+N pour un site (+ ses enfants)
        $ids_etablissement = array_keys($etablissements);
        $groupement_adresses = $service_groupement_communes->findGroupementForEtablissement($ids_etablissement);

        foreach($groupement_adresses as $groupement_adresse) {
            if ($groupement_adresse['ID_GROUPEMENT']) {
                $groupements[$groupement_adresse['ID_ETABLISSEMENT']][] = $groupement_adresse['ID_GROUPEMENT'];
            }
            if ($groupement_adresse['NUMINSEE_COMMUNE'] && !isset($communes[$groupement_adresse['ID_ETABLISSEMENT']])) {
                $communes[$groupement_adresse['ID_ETABLISSEMENT']][] = $groupement_adresse['NUMINSEE_COMMUNE'];
            }
        }

        foreach($ids_etablissement as $id) {
            $groupements[$id][] = '0';
            $groupements[$id] = implode('-', $groupements[$id]);

            $communes[$id][] = '0';
            $communes[$id] = implode('-', $communes[$id]);
        }

        $resource = '';

        switch($etablissement['informations']['ID_GENRE']) {
            case '1':
                $resource = array('editsite');

                foreach($ids_etablissement as $id) {
                    $new_resource = 'etablissement_erp_';
                    $new_resource .= ($etablissements[$id]['informations']['ID_TYPEACTIVITE'] == null ? '0' : $etablissements[$id]['informations']['ID_TYPEACTIVITE'] . '-0') . '_';
                    $new_resource .= ($etablissements[$id]['informations']['ID_CATEGORIE'] == null ? '0' : $etablissements[$id]['informations']['ID_CATEGORIE'] . '-0') . '_';
                    $new_resource .= ($etablissements[$id]['informations']['ID_COMMISSION'] == null ? '0' : $etablissements[$id]['informations']['ID_COMMISSION'] . '-0') . '_';
                    $new_resource .= $groupements[$id] . '_';
                    $new_resource .= $communes[$id];

                    $resource[] = $new_resource;
                }

                $resource = array_unique($resource);

                break;

            case '2':
                $resource = 'etablissement_erp_';
                $resource .= ($etablissement['informations']['ID_TYPEACTIVITE'] == null ? '0' : $etablissement['informations']['ID_TYPEACTIVITE'] . '-0') . '_';
                $resource .= ($etablissement['informations']['ID_CATEGORIE'] == null ? '0' : $etablissement['informations']['ID_CATEGORIE'] . '-0') . '_';
                $resource .= ($etablissement['informations']['ID_COMMISSION'] == null ? '0' : $etablissement['informations']['ID_COMMISSION'] . '-0') . '_';
                $resource .= $groupements[$id_etablissement] . '_';
                $resource .= $communes[$id_etablissement];
                break;

            case '3':
                $resource = 'etablissement_cell_';
                $resource .= ($etablissement['informations']['ID_TYPEACTIVITE'] == null ? '0' : $etablissement['informations']['ID_TYPEACTIVITE'] . '-0') . '_';
                $resource .= ($etablissement['informations']['ID_CATEGORIE'] == null ? '0' : $etablissement['informations']['ID_CATEGORIE'] . '-0');
                break;

            case '4':
                $resource = 'etablissement_hab_';
                $resource .= ($etablissement['informations']['ID_FAMILLE'] == null ? '0' : $etablissement['informations']['ID_FAMILLE'] . '-0') . '_';
                $resource .= $groupements[$id_etablissement] . '_';
                $resource .= $communes[$id_etablissement];
                break;

            case '5':
                $resource = 'etablissement_igh_';
                $resource .= ($etablissement['informations']['ID_CLASSE'] == null ? '0' : $etablissement['informations']['ID_CLASSE'] . '-0') . '_';
                $resource .= ($etablissement['informations']['ID_COMMISSION'] == null ? '0' : $etablissement['informations']['ID_COMMISSION'] . '-0') . '_';
                $resource .= $groupements[$id_etablissement] . '_';
                $resource .= $communes[$id_etablissement];
                break;

            case '6':
                $resource = 'etablissement_eic_';
                $resource .= $groupements[$id_etablissement] . '_';
                $resource .= $communes[$id_etablissement];
                break;

            case '7':
                $resource = 'etablissement_camp_';
                $resource .= $groupements[$id_etablissement] . '_';
                $resource .= $communes[$id_etablissement];
                break;

            case '8':
                $resource = 'etablissement_temp_';
                $resource .= $groupements[$id_etablissement] . '_';
                $resource .= $communes[$id_etablissement];
                break;

            case '9':
                $resource = 'etablissement_iop_';
                $resource .= $groupements[$id_etablissement] . '_';
                $resource .= $communes[$id_etablissement];
                break;

            case '10':
                $resource = 'etablissement_zone_';
                $resource .= ($etablissement['informations']['ID_CLASSEMENT'] == null ? '0' : $etablissement['informations']['ID_CLASSEMENT'] . '-0') . '_';
                $resource .= $groupements[$id_etablissement] . '_';
                $resource .= $communes[$id_etablissement];
                break;
        }
        $list_resources_finale = is_array($resource) ? $resource : array($resource);
        //$this->develop_resources($list_resources_finale);

        return $list_resources_finale;
    }

}

/**
 * Container de ressource non developpées
 * qui ne les développent qu'à la demande, cette fonction coûtant cher en exécution
 */
class ResourceContainer implements Iterator {

    protected $resources = array();

    protected $developped_resources = array();

    protected $resources_index = 0;

    protected $developped_resources_index = 0;

    public function __construct(array $resources = array()) {
        $this->resources = $resources;
    }

    public function current() {
        if (isset($this->developped_resources[$this->developped_resources_index])) {
            return $this->developped_resources[$this->developped_resources_index];
        } else {
            return null;
        }
    }

    public function key() {
        return 0;
    }

    public function next() {
        $this->developped_resources_index++;
        if (!isset($this->developped_resources[$this->developped_resources_index])) {
            $this->resources_index++;
            if (isset($this->resources[$this->resources_index])) {
                $this->developped_resources_index = 0;
                $this->developped_resources = array($this->resources[$this->resources_index]);
                $this->develop_resources($this->developped_resources);
            }
        }
    }

    public function rewind() {
        $this->developped_resources = array();
        $this->resources_index = 0;
        $this->developped_resources_index = 0;

        if (count($this->resources) > 0) {
            $this->developped_resources = array($this->resources[$this->resources_index]);
            $this->develop_resources($this->developped_resources);
        }
    }

    public function valid() {
        return isset($this->developped_resources[$this->developped_resources_index])
        && isset($this->resources[$this->resources_index]);
    }

    public function hasNonDeveloppedResource($resource) {
        return in_array($resource, $this->resources);
    }

    public function develop_resources(&$list_resources_finale) {
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

}
