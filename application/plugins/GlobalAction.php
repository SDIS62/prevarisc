<?php
class Plugin_GlobalAction extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // Si l'utilisateur n'est pas connecté, alors on le redirige vers la page de login (si il ne s'y trouve pas encore)
        if ( !Zend_Auth::getInstance()->hasIdentity() && $request->getActionName() !="login" ) 
        {
                $request->setControllerName("user");
                $request->setActionName("login");
        }
        elseif(Zend_Auth::getInstance()->hasIdentity())
        {
            // On update la dernière action effectuée par l'utilisateur
            $model_user = new Model_DbTable_Utilisateur;
            $user = $model_user->find(Zend_Auth::getInstance()->getIdentity()->ID_UTILISATEUR)->current();
            $user->LASTACTION_UTILISATEUR = date("Y:m-d H:i:s");
            $user->save();
        }
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        // Ajax : désactive le layout quand une requete ajax est envoyée
        if( $request->isXmlHttpRequest() )
        {
            Zend_Layout::getMvcInstance()->disableLayout();
        }
    }

}