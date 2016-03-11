<?php

class Api_CalendarController extends Zend_Controller_Action
{    

    public function indexAction() 
    {
        header("Content-Type: text/Calendar");
        header("Content-Disposition: inline; filename=calendar.ics");
        
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $server = new SDIS62_Rest_Server;
        $server->setClass("Api_Service_Calendar");
        $server->handle($this->_request->getParams());
    }
}