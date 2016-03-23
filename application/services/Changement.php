<?php

class Service_Changement 
{

    /**
     * Définition des balises
     */
    const BALISES = array(
        "{activitePrincipaleEtablissement}" => array(
            "description"   => "L'activité principale de l'établissement",
            "model"         => "informations",
            "champ"         => "LIBELLE_TYPEACTIVITE_PRINCIPAL"
        ),
        "{categorieEtablissement}" => array(
            "description"   => "La catégorie de l'etablissement",
            "model"         => "informations",
            "champ"         => "LIBELLE_CATEGORIE"
        ),
        "{etablissementAvis}" => array(
            "description"   => "L'avis de l'établissement",
            "model"         => "avis",
            "champ"         => ""
        ),
        "{etablissementLibelle}" => array(
            "description"   => "Le libelle de l'établissement",
            "model"         => "informations",
            "champ"         => "LIBELLE_ETABLISSEMENTINFORMATIONS"
        ),
        "{etablissementNumeroId}" => array(
            "description"   => "Le numéro Id de l'établissement",
            "model"         => "general",
            "champ"         => "NUMEROID_ETABLISSEMENT"
        ),
        "{etablissementStatut}" => array(
            "description"   => "Le statut (Ouvert ou Fermé) de l'établissement",
            "model"         => "informations",
            "champ"         => "LIBELLE_STATUT"
        ),
        "{typePrincipalEtablissement}" => array(
            "description"   => "Le type principal de l'établissement",
            "model"         => "informations",
            "champ"         => "LIBELLE_TYPE_PRINCIPAL"
        )

    );
    
    /**
     * Retourne tous les enregistrement contenus dans la table changement
     * 
     * @return array    Le résultat
     */
    public function getAll() 
    {
        $dbChangement = new Model_DbTable_Changement;
        
        return $dbChangement->findAll();
    }

    /**
     * Retourne un changement via son Id précisé en argument
     * 
     * @param  int $idChangement    L'id du changement à retourner
     * @return array                Le résultat
     */
    public function get($idChangement)
    {
        $dbChangement = new Model_DbTable_Changement;
        
        return $dbChangement->find($idChangement)->current();
    }

    /**
     * Sauvegarde les modifications apportées aux messages d'alerte 
     * par défaut
     * 
     * @param  array    $data Les données envoyés en post
     * @return void
     */
    public function save($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $message) {
                $idChangement = explode('_', $key)[0];
                $changement = $this->get($idChangement);
                $changement->MESSAGE_CHANGEMENT = $message;
                $changement->save();
            }
        }
    }

    /**
     * Retourne le tableau de balises 
     * 
     * @return array Les balises définies dans cette classe
     */
    public function getBalises()
    {
        return self::BALISES;
    }

    /**
     * Retourne l'objet du mail de changement formaté
     * 
     * @param  int      $idChangement   Id du changement
     * @param  array    $ets            Etablissement concerné
     * @return string                   L'objet formaté
     */
    public function getObjet($idChangement, $ets)
    {
        switch($idChangement) {
            case "1":
                $objet = sprintf("Passage au statut \"%s\"", 
                            $ets['informations']['LIBELLE_STATUT']);
                break;
            case "2":
                $objet = sprintf("Passage en avis \"%s\"", 
                            $this->getAvis($ets));
                break;
            case "3":
                $objet = sprintf("Changement de classement \"%s - %s %s\"", 
                            $ets['informations']['LIBELLE_CATEGORIE'],
                            $ets['informations']['LIBELLE_TYPE_PRINCIPAL'],
                            $ets['informations']['LIBELLE_TYPEACTIVITE_PRINCIPAL']
                );
                break;
            default:
                $objet = "";
        }

        $commune = "";
        if (count($ets['adresses']) > 0) {
            $commune = $ets['adresses'][0]['LIBELLE_COMMUNE'];
        }

        $libelleInfos = $ets['informations']['LIBELLE_ETABLISSEMENTINFORMATIONS'];
        if (count($ets['parents']) > 0) {
            $libelleInfos = sprintf("%s %s",
                $ets['parents'][0]['LIBELLE_ETABLISSEMENTINFORMATIONS'],
                $libelleInfos
            );
        }

        return sprintf("%s (%s) - %s",
            $libelleInfos,
            $commune,
            $objet);
    }

    /**
     * Convertit les balises dans le message avec les bonnes valeurs
     * 
     * @param  string $message Le message a envoyer avec des balises
     * @return string          Le message convertit
     */
    public function convertMessage($message, $ets)
    {
        $params = array();
        foreach(self::BALISES as $balise => $content) {
            $replacementstr = "";
            if ($content['model'] === "avis") {
                $replacementstr = $this->getAvis($ets);
            } elseif (array_key_exists($content['model'], $ets)
                && array_key_exists($content['champ'], $ets[$content['model']])) {
                $replacementstr = $ets[$content['model']][$content['champ']];    
            }
            $params[$balise] = $replacementstr;
        }

        return strtr($message, $params);
    }

    /**
     * Retourne l'avis d'un établissement formaté
     * 
     * @param  array $ets   L'établissement 
     * @return string       L'avis de l'établissement
     */
    public function getAvis($ets) 
    {
        $avis = '';
        $serviceEts = new Service_Etablissement;
        $avisType = $serviceEts->getAvisEtablissement(
            $ets['general']['ID_ETABLISSEMENT'], $ets['general']['ID_DOSSIER_DONNANT_AVIS']);

        if ($ets['presence_avis_differe'] == true && $avisType == "avisDiff") {
            $avis = "Présence d'un dossier avec avis differé";
        } elseif ($ets['avis'] != null) {
            if($ets['avis'] == 1 && $avisType == "avisDoss") {
                $avis = "Favorable" . ($ets['informations']['ID_GENRE'] == 3 ? '' : " à l'exploitation");
            } elseif ($ets['avis'] == 2  && $avisType == "avisDoss") {
                $avis = "Défavorable" . ($ets['informations']['ID_GENRE'] == 3 ? '' : " à l'exploitation"); 
            }
        } else {
            $avis = "Avis d'exploitation indisponible";
        }
        
        return $avis;
    }
}