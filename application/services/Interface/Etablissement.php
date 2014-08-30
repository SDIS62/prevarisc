<?php

interface Service_Interface_Etablissement extends Service_Interface_Contact, Service_Interface_PieceJointe, Service_Interface_TexteApplicable
{
    public function get($id_etablissement);
    public function getHistorique($id_etablissement);
    public function getDossiers($id_etablissement);
    public function getDescriptifs($id_etablissement);
    public function saveDescriptifs($id_etablissement, $historique, $descriptif, $derogations, array $descriptifs_techniques);
    public function findAll($libelle, $id_genre, $enfants = true);
    public function ficheExiste($id_etablissement, $date);
    public function save($id_genre, array $data, $id_etablissement = null, $date = '');
    public function getDefaultValues($genre, $categorie = null, $local_sommeil = null, $classe = null);
}