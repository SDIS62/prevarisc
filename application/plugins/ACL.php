<?php

class Plugin_ACL extends Zend_Controller_Plugin_Abstract
{
    /**
    * Contrôle des ACL pour l'utilisateur connecté
    *
    * @param  Zend_Controller_Request_Abstract $request
    * @return void
    */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');

        // Les routes sans protection sont ignorées
        if (in_array($request->getControllerName(), array('error', 'session'))) {
            return;
        }

        // Autorisation spéciale aux applications tierces via le partage d'un token
        if($request->getParam('key') === getenv('PREVARISC_SECURITY_KEY')) {
            return;
        }

        // Récupération de l'utilisateur connecté
        $user = Zend_Auth::getInstance()->getIdentity();

        // Restriction des accès aux utilisateurs non connectés
        if (empty($user)) {
            $redirector->gotoSimple('login', 'session', 'default');
            return;
        }

        // On met à jour la date de la dernière action effectuée
        $user_service = new Service_User();
        $user_service->updateLastActionDate($user['ID_UTILISATEUR']);

        // Chargement de l'ACL pour l'utilisateur
        $acl = $this->loadAcl($user);

        // On check les permissions
        if(!$acl->isAllowedRequest($request)) {
            if($request->getControllerName() == 'error') return;
            $request->setModuleName('default');
            $request->setControllerName('error');
            $request->setActionName('error');
            $error = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
            $error->type = Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER;
            $error->request = clone $request;
            $error->exception = new Zend_Controller_Dispatcher_Exception('Accès non autorisé', 401);
            $request->setParam('error_handler', $error);
        }
    }

    /**
    * Chargement des ACL
    *
    * @param  $user
    * @return Zend_Acls
    */
    public function loadAcl($user)
    {
        $acl = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('acl');

        if(!$acl->hasRole($user['group']['LIBELLE_GROUPE'])) {
            $acl->addRole(new Zend_Acl_Role($user['group']['LIBELLE_GROUPE']));
        }

        $user_service = new Service_User();
        $privileges = $user_service->getGroupPrivileges($user);

        foreach($privileges as $resource) {

            if(!$acl->has($resource['name_resource'])) {
                $acl->add(new Zend_Acl_Resource($resource['name_resource']));
            }

            $acl->allow($user['group']['LIBELLE_GROUPE'], $resource['name_resource'], $resource['name_privilege']);

        }

        return $acl;
    }
}
