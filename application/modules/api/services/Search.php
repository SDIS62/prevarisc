<?php

class Api_Service_Search
{
    /**
     * Recherche des établissements
     *
     * @return string
     */
    public function etablissements($label = null, $identifiant = null, $genres = null, $categories = null, $classes = null, $familles = null, $types = null, $avis_favorable = null, $statuts = null, $local_sommeil = null, $lon = null, $lat = null, $count = 10, $page = 1)
    {
        //
    }
    
    /**
     * Recherche des dossiers
     *
     * @return string
     */
    public function dossiers($num_doc_urba = null, $count = 10, $page = 1)
    {
        //
    }

    /**
     * Recherche des utilisateurs
     *
     * @return string
     */
    public function users($fonctions = null, $name = null, $count = 10, $page = 1)
    {
        // Création de l'objet recherche
        $search = new Model_DbTable_Search;

        // On set le type de recherche
        $search->setItem("utilisateur");
        $search->limit($count);

        // Critères : nom
        if($name !== null) {
           $search->setCriteria("NOM_UTILISATEURINFORMATIONS", $name, false);
        }

        // Critères : fonctions
        if($fonctions !== null) {
           $search->setCriteria("fonction.ID_FONCTION", $fonctions);
        }

        return $search->run()->getAdapter()->getItems(0, 99999999999)->toArray();
    }
}