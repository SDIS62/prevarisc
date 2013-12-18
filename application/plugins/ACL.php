<?php
class Plugin_ACL extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // Si l'utilisateur n'est pas connecté, alors on le redirige vers la page de login (si il ne s'y trouve pas encore)
        if ( !Zend_Auth::getInstance()->hasIdentity() && $request->getActionName() != "login" ) 
        {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $redirector->gotoSimple('login', 'user', 'default');
        }
        elseif(Zend_Auth::getInstance()->hasIdentity())
        {
            // On update la dernière action effectuée par l'utilisateur
            $model_user = new Model_DbTable_Utilisateur;
            $user = $model_user->find(Zend_Auth::getInstance()->getIdentity()->ID_UTILISATEUR)->current();
            $user->LASTACTION_UTILISATEUR = date("Y:m-d H:i:s");
            $user->save();

            // Chargement des ACL
            
            $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');

            // Chargement des ACL
            if(($acl = unserialize($cache->load('acl'))) === false)
            {
                // Liste des ressources
                $resources_dbtable = new Model_DbTable_Resource;
                $resources = $resources_dbtable->fetchAll();

                // Liste des groupes
                $groupes_dbtable = new Model_DbTable_Groupe;
                $groups = $groupes_dbtable->fetchAll();
                
                // Configuration des ACL
                $acl = new Zend_Acl();
                
                // ajouts des ressources
                foreach($resources as $resource)
                {
                    $acl->add(new Zend_Acl_Resource($resource->name));
                }

                // ajouts des roles (les groupes d'utilisateurs) et on fixe leurs règles
                foreach($groups as $role)
                {
                    $acl->addRole(new Zend_Acl_Role($role->LIBELLE_GROUPE));
                    
                    $privileges = $role->findModel_DbTable_PrivilegeViaModel_DbTable_GroupePrivilege();
                    $resources = array();

                    foreach($privileges as $privilege)
                    {
                        $resource_privilege = $privilege->findParentModel_DbTable_Resource()->toArray();
                        
                        if(!array_key_exists($resource_privilege['id_resource'], $resources))
                        {
                            $resources[$resource_privilege['id_resource']] = $resource_privilege;
                            $resources[$resource_privilege['id_resource']]["privileges"] = array();
                        }
                        
                        $resources[$resource_privilege['id_resource']]["privileges"][$privilege->id_privilege] = $privilege->name;
                    }

                    foreach($resources as $group_resource)
                    {
                        $acl->allow($role->LIBELLE_GROUPE, $group_resource["name"], $group_resource["privileges"]);
                    }
                }
                
                // Sauvegarde en cache
                $cache->save(serialize($acl));
            }
            
            $identity = Zend_Auth::getInstance()->getIdentity();
            $role = $identity->LIBELLE_GROUPE;
            
            // Récupération de la vue
            $view = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');
            
            // Récupération de la page active
            $page = $view->navigation($view->nav)->findOneBy('active', true);
            
            // Si la page correspond bien, on check l'ACL
            if($page !== null)
            {
                // Récupération de la resource demandée par la page active
                $resource = $this->getPageResource($page);
                
                // Récupération du privilège demandé par la page active
                $privilege = $this->getPagePrivilege($page);

                // check les permissions !
                if (!$acl->isAllowed($role, $resource, $privilege) && $resource !== null)
                {
                    $request->setControllerName('error');
                    $request->setActionName('not-allowed');
                }
            }
        }
    }
    
    /**
     * getPageResource
     *
     * @param  $page
     * @return null|Zend_Acl_Resource_Interface
     */  
    private function getPageResource($page)
    {
        if($page !== null)
        {
            return $page->getResource() === null ?
                $page->getParent() instanceof Zend_Navigation_Page ? $this->getPageResource($page->getParent()) : null :
                $page->getResource();
        }
        else
        {
            return null;
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
        return $page->getPrivilege();
    }
}