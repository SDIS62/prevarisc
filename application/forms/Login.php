<?php

class Form_Login extends Twitter_Bootstrap_Form_Horizontal
{
    /**
     * @inheritdoc
     */   
    public function init()
    {
        $this->setMethod("post");
        
        $this->addElement('text', 'username', array(
            'label' => 'Nom d\'utilisateur',
            'placeholder' => 'Nom d\'utilisateur',
            'required' => true,
            'filters' => array(new Zend_Filter_HtmlEntities, new Zend_Filter_StripTags),
            'validators' => array(new Zend_Validate_StringLength(1,255))
        ));
        
        $this->addElement('password', 'passwd', array(
            'label' => 'Mot de passe',
            'placeholder' => 'Mot de passe',
            'required' => true,
            'filters' => array(new Zend_Filter_HtmlEntities, new Zend_Filter_StripTags),
            'validators' => array(new Zend_Validate_StringLength(1,255))
        ));
        
        $this->addElement(new Twitter_Bootstrap_Form_Element_Submit("Connexion", array(
                "buttonType" => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY
        )), 'submit');

        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Description',
            'Errors'
        ));
    }
}