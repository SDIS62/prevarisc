<?php

class Acl extends Zend_Acl
{
    /**
    * Returns true if and only if the user has access to the Resource
    *
    * @param  Zend_Acl_Resource_Interface|string $resource
    * @param  string                             $privilege
    */
    public function isUserAllowed($resource = null, $privilege = null)
    {
        try {
            $role = Zend_Auth::getInstance()->getIdentity()['group']['LIBELLE_GROUPE'];
            return parent::isAllowed($role, $resource, $privilege);
        }
        catch(Exception $e) {
            return false;
        }
    }
}
