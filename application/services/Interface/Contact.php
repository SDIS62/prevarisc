<?php

interface Service_Interface_Contact
{
    public function getAllContacts($id);
    public function saveContact($data);
    public function deleteContact($data);
}
