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

    public function indexAction()
    {
        $text = file_get_contents(APPLICATION_PATH . DS . '..' . DS . 'docs' . DS . 'about.md');
        $this->view->text = \Michelf\Markdown::defaultTransform($text);
    }

    public function tosAction()
    {
        $text = file_get_contents(APPLICATION_PATH . DS . '..' . DS . 'docs' . DS . 'tos.md');
        $this->view->text = \Michelf\Markdown::defaultTransform($text);
    }

    public function supportAction()
    {
        $text = file_get_contents(APPLICATION_PATH . DS . '..' . DS . 'docs' . DS . 'support.md');
        $this->view->text = \Michelf\Markdown::defaultTransform($text);
    }

    public function devAction()
    {
        $text = file_get_contents(APPLICATION_PATH . DS . '..' . DS . 'docs' . DS . 'dev.md');
        $this->view->text = \Michelf\Markdown::defaultTransform($text);
    }
}
