<?php

class AdminController extends Zend_Controller_Action
{
    /**
     * Index du panel d'admnistration
     *
     */
    public function indexAction()
    {
        // Définition du layout about
        $this->_helper->layout->setLayout('menu_left');
    }
}