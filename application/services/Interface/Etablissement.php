<?php

interface Service_Interface_Etablissement extends Service_Interface_Contact, Service_Interface_PieceJointe, Service_Interface_TexteApplicable
{
    public function get($id_etablissement);
    public function getHistorique($id_etablissement);
    public function getDossiers($id_etablissement);
    public function getDescriptifs($id_etablissement);
    public function saveDescriptifs($id_etablissement, $historique, $descriptif, $derogations, $descriptifs_techniques);
    public function getAdresses($id_etablissement);
    public function getPlans($id_etablissement);
    public function getDiapo($id_etablissement);
    public function findAll($id_etablissement);
    public function ficheExiste($id_etablissement);
    public function save($id_etablissement);
    public function getDefaultValues($id_etablissement);
}
