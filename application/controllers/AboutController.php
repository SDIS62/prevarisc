<?php

class AboutController extends Zend_Controller_Action
{
    /**
     * @inheritdoc
     */
    public function postDispatch()
    {
        // on rend la vue générique
        $this->render('display-text');
    }

    /**
     * A propos du central
     *
     */
    public function indexAction()
    {
        $text = file_get_contents(APPLICATION_PATH . DS . '..' . DS . 'docs' . DS . 'about.md');
        $this->view->text = \Michelf\Markdown::defaultTransform($text);
    }

    /**
     * Conditions d'utilisation
     *
     */
    public function tosAction()
    {
        $text = file_get_contents(APPLICATION_PATH . DS . '..' . DS . 'docs' . DS . 'tos.md');
        $this->view->text = \Michelf\Markdown::defaultTransform($text);
    }

    /**
     * Aide
     *
     */
    public function supportAction()
    {
        $text = file_get_contents(APPLICATION_PATH . DS . '..' . DS . 'docs' . DS . 'support.md');
        $this->view->text = \Michelf\Markdown::defaultTransform($text);
    }

    /**
     * Support pour les développeurs
     *
     */
    public function devAction()
    {
        $text = file_get_contents(APPLICATION_PATH . DS . '..' . DS . 'docs' . DS . 'dev.md');
        $this->view->text = \Michelf\Markdown::defaultTransform($text);
    }
}
