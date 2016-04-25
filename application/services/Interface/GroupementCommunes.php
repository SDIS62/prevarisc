<?php

interface Service_Interface_GroupementCommunes extends Service_Interface_Contact
{
    public function get($id_etablissement);
    public function getAllByNumInsee($numinsee);
    public function save($id_etablissement);
    public function delete($id_etablissement);
}
