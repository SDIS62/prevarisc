<?php

class PlatauController extends Zend_Controller_Action
{
    /**
     * @var Model_PlatauConsultationMapper
     */
    private $platauConsultationMapper;

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('selectionabreviation', 'json')
            ->addActionContext('retryExportPec', 'json')
            ->addActionContext('retryExportAvis', 'json')
            ->initContext()
        ;

        $this->platauConsultationMapper = new Model_PlatauConsultationMapper();
    }

    public function retryExportPecAction(): void
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $platauConsultation = $this->platauConsultationMapper->find($this->getParam('id'), new Model_PlatauConsultation());
        $platauConsultation->setStatutPec('to_export');

        $this->platauConsultationMapper->save($platauConsultation);
    }

    public function retryExportAvisAction(): void
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $platauConsultation = $this->platauConsultationMapper->find($this->getParam('id'), new Model_PlatauConsultation());
        $platauConsultation->setStatutAvis('to_export');

        $this->platauConsultationMapper->save($platauConsultation);
    }
}
