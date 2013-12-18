<?php

class AdminController extends Zend_Controller_Action
{
    /**
     * @inheritdoc
     */  
    public function init()
    {
        // Définition du layout menu_left
        $this->_helper->layout->setLayout('menu_left');
    }
    
    /**
     * Index du panel d'admnistration
     *
     */
    public function indexAction() {}
}