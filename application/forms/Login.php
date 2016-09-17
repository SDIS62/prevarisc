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
            'validators' => array(new Zend_Validate_StringLength(1,255)),
            'autocomplete' => getenv('PREVARISC_ENFORCE_SECURITY') ? 'off' : 'on',
        ));

        $password_validators = array(
            new Zend_Validate_StringLength(1,255),
        );
        
        if (getenv('PREVARISC_ENFORCE_SECURITY') == 1) {
            $regex_validator = new Zend_Validate_Regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,}$/');
            $regex_validator->setMessage( 
                    "Votre mot de passe doit contenir au moins 8 caractères "
                    . "dont 1 minuscule, 1 majuscule, 1 chiffre et 1 caractère spécial. "
                    . "Si celui-ci est définit dans un système externe à Prévarisc, merci de le changer dans ce système.",
                    Zend_Validate_Regex::NOT_MATCH);
            $password_validators[] = $regex_validator;
        }
        
        $this->addElement('password', 'prevarisc_login_passwd', array(
            'label' => 'Mot de passe',
            'placeholder' => 'Mot de passe',
            'required' => true,
            'filters' => array(new Zend_Filter_HtmlEntities, new Zend_Filter_StripTags),
            'validators' => $password_validators,
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
