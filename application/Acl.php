<?php

class Acl extends Zend_Acl
{
    /**
    * Returns true if and only if the Role has access to the Resource
    *
    * @param  Zend_Acl_Role_Interface|string     $role
    * @param  Zend_Acl_Resource_Interface|string $resource
    * @param  string                             $privilege
    * @uses   Zend_Acl::get()
    * @uses   Zend_Acl_Role_Registry::get()
    * @return boolean
    */
    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
        if(!$this->has($resource)) {
            return false;
        }

        return parent::isAllowed($role, $resource, $privilege);
    }

    /**
     * Retourne vrai si la requête est acceptée
     *
     * @param  Zend_Controller_Request_Abstract     $request
     * @return boolean
     */
    public function isAllowedRequest(Zend_Controller_Request_Abstract $request)
    {
        // Récupération de la vue
        $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');

        // Récupération de la page
        $page = $view->navigation($view->nav)->findOneByController($request->getControllerName());
        if($page !== null) { $page = $page->findOneByAction($request->getActionName()); }
        if($page == null)  { return true; }

        // Récupération du user
        $user = Zend_Auth::getInstance()->getIdentity();

        if(empty($user)) {
            return false;
        }

        // Récupération de la resource / privilège demandée par la page active
        $role = $user['group']['LIBELLE_GROUPE'];
        $resources = $this->getPageResources($page, $request);
        $privilege = $this->getPagePrivilege($page, $request);

        // Si la page n'est pas protégé par une ressource / privilège, on autorise l'accès
        if($resources == null || $privilege == null) {
           return true;
        }

        // Controle des pages "établissement"
        if($page->get('controller') == 'etablissement') {
            foreach($resources as $resource) {
                if($this->isAllowed($role, $resource, $privilege)) {
                    return true;
                }
            }
            if(in_array('editsite', $resources) && $page->get('action') == 'edit') {
              if($this->isAllowed($role, "creations", "add_etablissement")) {
                  return true;
              }
            }
        }

        // Controle des pages "dossier"
        if($page->get('controller') == 'dossier') {
            if($page->get('action') !== 'add' && $page->get('action') !== 'savenew') {
                $access_granted_ets = false;
                $i = 0;
                foreach($resources as $resource) {
                    if(explode('_', $resource)[0] == 'etablissement') {
                        if($this->isAllowed($role, $resource,  'view_ets')) {
                            $access_granted_ets = true;
                            break;
                        }
                        $i++;
                    }
                }
                if($access_granted_ets || $i == 0) {
                    foreach($resources as $resource) {
                        if((explode('_', $resource)[0] == 'dossier' || explode('_', $resource)[0] == 'creations') && $this->isAllowed($role, $resource, $privilege)) {
                            return true;
                        }
                    }
                }
            }
            else {
                if($this->isAllowed($role, 'creations', 'add_dossier')) {
                    return true;
                }
            }
        }

        // Controle des pages générales
        if($page->get('controller') !== 'dossier' && $page->get('controller') !== 'etablissement') {
            foreach($resources as $resource) {
                if($this->has($resource)) {
                    if($this->isAllowed($role, $resource,  $privilege)) {
                        return true;
                    }
                }
                else {
                    return true;
                }
            }
        }

        return false;
    }

    /**
    * getPageResources
    *
    * @param  $page
    * @param  $request
    * @return null|array
    */
    private function getPageResources($page, $request = null)
    {
        if($page != null) {
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
    * @param  $request
    * @return null|string
    */
    private function getPagePrivilege($page, $request = null)
    {
        if($page != null) {
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
            $resource = array('editsite');
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

            case '7':
            $resource = 'etablissement_camp_';
            $resource .= $groupements . '_';
            $resource .= $communes;
            break;

            case '8':
            $resource = 'etablissement_temp_';
            $resource .= $groupements . '_';
            $resource .= $communes;
            break;

            case '9':
            $resource = 'etablissement_iop_';
            $resource .= $groupements . '_';
            $resource .= $communes;
            break;

            case '10':
            $resource = 'etablissement_zone_';
            $resource .= ($etablissement['informations']['ID_CLASSEMENT'] == null ? '0' : $etablissement['informations']['ID_CLASSEMENT'] . '-0') . '_';
            $resource .= $groupements . '_';
            $resource .= $communes;
            break;
        }

        $develop_resources = function($resources) {
            if(!is_array($resources)) {
                $resources = array($resources);
            }
            for($i = 0; $i < count($resources); $i++) {
                $resource_exploded = explode('_', $resources[$i]);
                for($j = 0; $j < count($resource_exploded); $j++) {
                    $resource_exploded2 = explode('-', $resource_exploded[$j]);
                    if(!empty($resource_exploded) && count($resource_exploded2) > 1) {
                        for($k = 0; $k < count($resource_exploded2); $k++) {
                            $name = $resource_exploded;
                            $name[$j] = $resource_exploded2[$k];
                            $resources[] = implode($name, '_');
                        }
                        unset($resources[$i]);
                    }
                }
            }
            return array_unique($resources);
        };

        return $develop_resources($resource);
    }
}
