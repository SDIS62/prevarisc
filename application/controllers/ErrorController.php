<?php

class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $this->_helper->layout->setLayout('error');

        $errors = $this->_getParam('error_handler');
        
        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'Vous avez atteint la page d\'erreur';
            return;
        }
        
        // On envoie le bon code erreur en fonction du type
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // Type de l'erreur :  404 error
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Page introuvable';
                break;

            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER:
                if($errors->exception->getCode() == 401) {
                    $this->getResponse()->setHttpResponseCode(401);
                    $priority = Zend_Log::NOTICE;
                    $this->render('not-allowed');
                }
                break;
                
            default:
                // Type de l'erreur : application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = 'L\'application a levée une erreur';
                break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->log($this->view->message, $priority, $errors->exception);
            $log->log('Request Parameters', $priority, $errors->request->getParams());
        }
        
        // Si l'affichage des exceptions est activé, on envoie un message
        $this->view->showException = $this->getInvokeArg('displayExceptions');
        $this->view->exception = $errors->exception;
        
        // On envoie la requête de l'erreur sur la vue
        $this->view->request   = $errors->request;
    }
    
    /**
     * Récupération des logs
     *
     * @return Zend_Log
     */
    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }
}