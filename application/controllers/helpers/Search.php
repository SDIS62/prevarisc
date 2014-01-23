<?php

    class Application_Controller_Helper_Search extends Zend_Controller_Action_Helper_Abstract
    {
        private $criteres;
        private $search;

        public function direct($criteres, $page)
        {
            // Resultats HTML
            $html = null;

            // Création de l'objet recherche
            $search = new Model_DbTable_Search;

            // On set le type de recherche
            $search->setItem($criteres["item"]);

            // On lance les critères par le type d'item a rechercher
            if( array_key_exists("NUMEROID_ETABLISSEMENT", $criteres) && $criteres["NUMEROID_ETABLISSEMENT"] != "Numéro d'id" )
                $search->setCriteria("NUMEROID_ETABLISSEMENT", $criteres["NUMEROID_ETABLISSEMENT"]);

            if( array_key_exists("LIBELLE_ETABLISSEMENTINFORMATIONS", $criteres) && $criteres["LIBELLE_ETABLISSEMENTINFORMATIONS"] != "Libellé de l'établissement" )
                $search->setCriteria("LIBELLE_ETABLISSEMENTINFORMATIONS", $criteres["LIBELLE_ETABLISSEMENTINFORMATIONS"], false);

            if( array_key_exists("ID_GENRE", $criteres) && count($criteres["ID_GENRE"]) > 0 )
                $search->setCriteria("genre.ID_GENRE", $criteres["ID_GENRE"]);

            if( array_key_exists("ID_CATEGORIE", $criteres) && count($criteres["ID_CATEGORIE"]) > 0 )
                $search->setCriteria("ID_CATEGORIE", $criteres["ID_CATEGORIE"]);

            if( array_key_exists("ID_FAMILLE", $criteres) && count($criteres["ID_FAMILLE"]) > 0 )
                $search->setCriteria("ID_FAMILLE", $criteres["ID_FAMILLE"]);

            if( array_key_exists("ID_CLASSE", $criteres) && count($criteres["ID_CLASSE"]) > 0 )
                $search->setCriteria("ID_CLASSE", $criteres["ID_CLASSE"]);

            if( array_key_exists("ID_TYPE", $criteres) && count($criteres["ID_TYPE"]) > 0 )
                $search->setCriteria("type.ID_TYPE", $criteres["ID_TYPE"]);

            if( array_key_exists("ID_AVIS", $criteres) && count($criteres["ID_AVIS"]) > 0 )
                $search->setCriteria("avis.ID_AVIS", $criteres["ID_AVIS"]);

            if( array_key_exists("LIBELLE_COMMUNE", $criteres) && $criteres["LIBELLE_COMMUNE"] != "Commune" )
            {
                if( array_key_exists("ID_GENRE", $criteres) && count($criteres["ID_GENRE"]) > 0 )
                {
                    foreach($criteres["ID_GENRE"] as $genre) {
                        switch($genre)
                        {
                            case "1": 
                                $search->setCriteria("adressecommunesite.LIBELLE_COMMUNE", $criteres["LIBELLE_COMMUNE"]);
                                
                                if( array_key_exists("ID_RUE", $criteres) && $criteres["ID_RUE"] != "" )
                                    $search->setCriteria("etablissementadressesite.ID_RUE", $criteres["ID_RUE"]);
                                break;
                                
                            case "3":
                                $search->setCriteria("adressecommunecell.LIBELLE_COMMUNE", $criteres["LIBELLE_COMMUNE"]);
                                
                                if( array_key_exists("ID_RUE", $criteres) && $criteres["ID_RUE"] != "" )
                                    $search->setCriteria("etablissementadressecell.ID_RUE", $criteres["ID_RUE"]);
                                break;
                                
                            default:
                                $search->setCriteria("adressecommune.LIBELLE_COMMUNE", $criteres["LIBELLE_COMMUNE"]);
                                
                                if( array_key_exists("ID_RUE", $criteres) && $criteres["ID_RUE"] != "" )
                                    $search->setCriteria("etablissementadresse.ID_RUE", $criteres["ID_RUE"]);
                        }
                    }
                }
                else
                {
                    $search->setCriteria("LIBELLE_COMMUNE_ADRESSE_SITE", $criteres["LIBELLE_COMMUNE"], true, "orHaving");
                    $search->setCriteria("LIBELLE_COMMUNE_ADRESSE_CELLULE", $criteres["LIBELLE_COMMUNE"], true, "orHaving");
                    $search->setCriteria("LIBELLE_COMMUNE_ADRESSE_DEFAULT", $criteres["LIBELLE_COMMUNE"], true, "orHaving");
                    
                    if( array_key_exists("ID_RUE", $criteres) && $criteres["ID_RUE"] != "" )
                    {
                        $search->setCriteria("ID_RUE_SITE", $criteres["ID_RUE"], true, "having");
                        $search->setCriteria("ID_RUE_CELL", $criteres["ID_RUE"], true, "having");
                        $search->setCriteria("etablissementadresse.ID_RUE", $criteres["ID_RUE"], true, "orWhere");
                    }
                }
            }

            if( array_key_exists("LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS", $criteres) )
                $search->setCriteria("LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS", (bool) $criteres["LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS"]);
                
            if( array_key_exists("ID_STATUT", $criteres) && count($criteres["ID_STATUT"]) > 0 )
                $search->setCriteria("ID_STATUT", $criteres["ID_STATUT"]);

            if ( array_key_exists("NOM_PRENOM", $criteres) && $criteres["NOM_PRENOM"] != "Nom de l'utilisateur" ) {
                $search->setCriteria("NOM_UTILISATEURINFORMATIONS", $criteres["NOM_PRENOM"], false);
            }

            if( array_key_exists("ID_FONCTION", $criteres) && count($criteres["ID_FONCTION"]) > 0 )
                $search->setCriteria("fonction.ID_FONCTION", $criteres["ID_FONCTION"]);

            if( array_key_exists("ID_DOSSIERNATURE", $criteres) && count($criteres["ID_DOSSIERNATURE"]) > 0 )
                $search->setCriteria("ID_DOSSIERNATURE", $criteres["ID_DOSSIERNATURE"], true, "having");
                
            if( array_key_exists("NUM_DOCURBA", $criteres) && $criteres["NUM_DOCURBA"] != "Numéro de document d'urbanisme" )
                $search->setCriteria("NUM_DOCURBA", $criteres["NUM_DOCURBA"]);

            if ( array_key_exists("DATEVISITE_DOSSIER", $criteres) && $criteres["DATEVISITE_DOSSIER"] != "" ) {
                $array_date = explode("/", $criteres["DATEVISITE_DOSSIER"]);
                $date = $array_date[2]."-".$array_date[1]."-".$array_date[0];
                $search->setCriteria("DATEVISITE_DOSSIER", $date, false, "having");
            }

            // On lance la recherche
            $resultats = $search->run( isset($criteres["par"]) && $criteres["par"] == "niveau", $page );

            // On gère l'affichage
            $html = Zend_Layout::getMvcInstance()->getView()->partial("search/search.phtml", array(
                "item" => $criteres["item"],
                "niveau" => isset($criteres["par"]) && $criteres["par"] == "niveau",
                "paginator" => $resultats
            ));

            // Envoi du html sur la vue
            return $html;
        }
    }