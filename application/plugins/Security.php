<?php

class Plugin_Security extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if ($request->getControllerName() != 'error') {

            $params = array_merge($request->getParams(), $_GET, $_POST);

            $filters = array(new Zend_Filter_HtmlEntities, new Zend_Filter_StripTags);

            $input = new Zend_Filter_Input($filters, array(), $params);

            if (@!$input->isValid()) {
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                $redirector->gotoRouteAndExit(array(), 'error', true);
            }

        }

    }
}
