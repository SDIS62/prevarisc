<?php

class Service_Adresse
{
    /**
     * Récupération de l'ensemble des communes
     *
     * @return array
     */
    public function getAllCommunes()
    {
    	$model_commune = new Model_DbTable_AdresseCommune;
    	return $model_commune->fetchAll(null, "LIBELLE_COMMUNE")->toArray();
    }

    /**
     * Récupération d'une commune
     *
     * @return array
     */
    public function find($numinsee)
    {
        // Modèles de données
        $DB_informations = new Model_DbTable_UtilisateurInformations;
        $DB_communes = new Model_DbTable_AdresseCommune;

        // On récupère la commune
        $commune = $DB_communes->find($numinsee)->current();
        $coordonnees = $DB_informations->find($commune->ID_UTILISATEURINFORMATIONS)->current();

        return array(
            'commune' => $commune,
            'coord' => $coordonnees
        );
    }

    /**
     * Sauvegarde d'une commune
     *
     * @param array
     */
    public function save($numinsee, $request)
    {
        // Modèles de données
        $DB_informations = new Model_DbTable_UtilisateurInformations;
        $DB_communes = new Model_DbTable_AdresseCommune;

        // On récupère la commune
        $commune = $DB_communes->find($numinsee)->current();

        if ($commune->ID_UTILISATEURINFORMATIONS == 0) {
            $commune->ID_UTILISATEURINFORMATIONS = $DB_informations->insert(array_intersect_key($request, $DB_informations->info('metadata')));
        } else {
            $info = $DB_informations->find( $commune->ID_UTILISATEURINFORMATIONS )->current();

            if ($info == null) {
                $id = $DB_informations->insert(array_intersect_key($request, $DB_informations->info('metadata')));
                $commune->ID_UTILISATEURINFORMATIONS = $id;
            } else {
                $info->setFromArray(array_intersect_key($request, $DB_informations->info('metadata')))->save();
            }
        }

        $commune->save();
    }

    /**
     * Récupération des communes via le nom ou le code postal
     *
     * @param string $q Code postal ou nom d'une commune
     * @return array
     */
    public function get($q)
    {
        if(strlen($q) == 5 && is_numeric($q)) {
            $DB_adresse = new Model_DbTable_EtablissementAdresse;
            return $DB_adresse->getVilleByCP($q);
        }

        $model_adresse = new Model_DbTable_AdresseCommune;
        return $model_adresse->get($q);
    }

    /**
     * Retourne les voies par rapport à une ville
     *
     * @param int $code_insee
     * @param string $q
     * @return array
     */
    public function getVoies($code_insee, $q = '')
    {
        $DB_adresse = new Model_DbTable_EtablissementAdresse;
        return $DB_adresse->getVoies($code_insee, $q);
    }

    /**
     * Retourne le maire de la commune concernée
     *
     * @param int $numinsee le numéro insee de la commune
     * @return array les informations de la fiche contact du maire
     */
    public function getMaire($numinsee) {
        $select = new Zend_Db_Select(Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('db'));

        $select->from("adressecommune")
            ->join("utilisateurinformations", "utilisateurinformations.ID_UTILISATEURINFORMATIONS = adressecommune.ID_UTILISATEURINFORMATIONS")
            ->where("adressecommune.NUMINSEE_COMMUNE = ?", $numinsee)
            ->limit(1);

        return $select->query()->fetch();
    }
}
