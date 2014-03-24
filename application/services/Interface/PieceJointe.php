<?php

interface Service_Interface_PieceJointe
{
    public function getAllPJ($id);
    public function addPJ($id, $file, $name = '', $description = '', $mise_en_avant = 0);
    public function deletePJ($id, $id_pj);
}
