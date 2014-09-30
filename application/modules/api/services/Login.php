<?php

class Api_Service_Login
{
    /**
     * login
     *
     * @return string
     */ 
    public function login($username = null,$password = null)
    {
    $Service_login = new Service_Login;
    $results = $Service_login->login($username,$password);	
    return $results;
    }
}
