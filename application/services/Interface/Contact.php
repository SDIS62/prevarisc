<?php

interface Service_Interface_Contact
{
    public function getAllContacts($id);
    public function addContact($id_etablissement, array $data);
    public function deleteContact($data);
}
