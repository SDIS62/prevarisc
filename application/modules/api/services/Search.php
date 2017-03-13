<?php

class Api_Service_Search
{
    /**
     * Recherche des établissements
     *
     * @param string $label
     * @param string $identifiant
     * @param string|array $genre
     * @param string|array $categorie
     * @param string|array $classe
     * @param string|array $famille
     * @param string|array $types_activites
     * @param bool $avis_favorable
     * @param string|array $statuts
     * @param bool $local_sommeil
     * @param float $lon
     * @param float $lat
     * @param int $parent
     * @param int $count Par défaut 10, max 1000
     * @param int $page par défaut = 1
     * @return string
     */
    public function etablissements($label = null, $identifiant = null, $genres = null, $categories = null, $classes = null, $familles = null, $types_activites = null, $avis_favorable = null, $statuts = null, $local_sommeil = null, $lon = null, $lat = null, $parent = null, $commissions = null, $groupements_territoriaux = null, $count = 10, $page = 1)
    {
        $service_search = new Service_Search;
        $results = $service_search->etablissements($label, $identifiant, $genres, $categories, $classes, $familles, $types_activites, $avis_favorable, $statuts, $local_sommeil, $lon, $lat, $parent, null, null, $commissions, $groupements_territoriaux, $count, $page);
        return $results;
    }

    /**
     * Recherche des dossiers
     *
     * @param array $types
     * @param string $objet
     * @param string $num_doc_urba
     * @param int $parent Id d'un dossier parent
     * @param bool $avis_differe Avis différé
     * @param int $count Par défaut 10, max 100
     * @param int $page par défaut = 1
     * @return array
     */
    public function dossiers($types = null, $objet = null, $num_doc_urba = null, $parent = null, $avis_differe = null, $count = 10, $page = 1)
    {
        $service_search = new Service_Search;
        $results = $service_search->dossiers($types, $objet, $num_doc_urba, $parent, $avis_differe, $count, $page);
        return $results;
    }

    /**
     * Recherche des utilisateurs
     *
     * @param string|array $fonctions
     * @param string $name
     * @param int|array $groups
     * @param bool $actif Optionnel
     * @param int $count Par défaut 10, max 100
     * @param int $page par défaut = 1
     * @return array
     */
    public function users($fonctions = null, $name = null, $groups = null, $actif = true, $count = 10, $page = 1)
    {
        $service_search = new Service_Search;
        $results = $service_search->users($fonctions, $name, $groups, $actif, $count, $page);
        return $results;
    }
}
