<?php

class Form_Login extends Zend_Form
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setMethod("post");

        $this->addElement('text', 'prevarisc_login_username', array(
            'label' => 'Nom d\'utilisateur',
            'placeholder' => 'Nom d\'utilisateur',
            'required' => true,
            'filters' => array(new Zend_Filter_HtmlEntities, new Zend_Filter_StripTags),
            'validators' => array(new Zend_Validate_StringLength(1,255))
        ));

        $this->addElement('password', 'prevarisc_login_passwd', array(
            'label' => 'Mot de passe',
            'placeholder' => 'Mot de passe',
            'required' => true,
            'filters' => array(new Zend_Filter_HtmlEntities, new Zend_Filter_StripTags),
            'validators' => array(new Zend_Validate_StringLength(1,255))
        ));

        $this->addElement(new Zend_Form_Element_Submit("Connexion", array("class" => "btn btn-primary")), 'submit');

        $this->setDecorators(array(
            'FormElements',
            'Form'
        ));

        $this->setElementDecorators(array(
            'ViewHelper',
            'Description',
            'Errors'
        ));
    }
}
