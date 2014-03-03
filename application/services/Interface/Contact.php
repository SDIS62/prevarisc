<?php

interface Service_Interface_Contact
{
    public function getAllContacts($id);
    public function addContact($id, $nom, $prenom, $id_fonction, $societe, $fixe, $mobile, $fax, $email, $adresse, $web);
    public function addContactExistant($id, $id_contact);
    public function deleteContact($id, $id_contact);
}
