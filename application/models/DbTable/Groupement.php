<?php

class Model_DbTable_Groupement extends Zend_Db_Table_Abstract
{
    protected $_name="groupement"; // Nom de la base
    protected $_primary = "ID_GROUPEMENT"; // Clé primaire
    protected $_referenceMap = array(
            "groupementtype" => array(
                "columns" => "ID_GROUPEMENT",
                "refTableClass" => "Model_DbTable_GroupementType",
                "refColumns" => "ID_GROUPEMENTTYPE",
            ),
            "groupementcommune" => array(
                "columns" => "ID_GROUPEMENT",
                "refTableClass" => "Model_DbTable_GroupementCommune",
                "refColumns" => "ID_GROUPEMENT",
                "onDelete" => self::CASCADE
            ),
            "groupementpreventionniste" => array(
                "columns" => "ID_GROUPEMENT",
                "refTableClass" => "Model_DbTable_GroupementPreventionniste",
                "refColumns" => "ID_GROUPEMENT",
                "onDelete" => self::CASCADE
            ),
        );

    public function get($id)
    {
        $select = $this	->select()
                        ->setIntegrityCheck(false)
                        ->from("groupement", "LIBELLE_GROUPEMENT")
                        ->joinInner("groupementtype", "groupement.ID_GROUPEMENTTYPE = groupementtype.ID_GROUPEMENTTYPE", "LIBELLE_GROUPEMENTTYPE")
                        ->joinLeft("utilisateurinformations", "utilisateurinformations.ID_UTILISATEURINFORMATIONS = groupement.ID_UTILISATEURINFORMATIONS")
                        ->where("groupement.ID_GROUPEMENT = '$id'");

        return ( $this->fetchRow( $select ) != null ) ? $this->fetchRow( $select ) : null;
    }

    public function getByLibelle($libelle)
    {
        $expLibelle = $this->getAdapter()->quote($libelle);
        $select = "SELECT groupement.*, groupementtype.LIBELLE_GROUPEMENTTYPE, utilisateurinformations.*
                    FROM groupement
                    INNER JOIN groupementtype ON groupement.ID_GROUPEMENTTYPE = groupementtype.ID_GROUPEMENTTYPE
                    LEFT JOIN utilisateurinformations ON utilisateurinformations.ID_UTILISATEURINFORMATIONS = groupement.ID_UTILISATEURINFORMATIONS
                    WHERE (groupement.LIBELLE_GROUPEMENT = " . $expLibelle . ");";
        //echo $select;
        //Zend_Debug::dump($DB_information->fetchRow($select));

        return $this->getAdapter()->fetchAll($select);
    }

    public function getByLibelle2($libelle, $libelleGroupementType)
    {
        $expLibelle = $this->getAdapter()->quote($libelle);
        $expLibelleGroupementType = $this->getAdapter()->quote($libelleGroupementType);
        $select = "SELECT groupement.*, groupementtype.LIBELLE_GROUPEMENTTYPE, utilisateurinformations.*
                    FROM groupement
                    INNER JOIN groupementtype ON groupement.ID_GROUPEMENTTYPE = groupementtype.ID_GROUPEMENTTYPE
                    LEFT JOIN utilisateurinformations ON utilisateurinformations.ID_UTILISATEURINFORMATIONS = groupement.ID_UTILISATEURINFORMATIONS
                    WHERE (groupement.LIBELLE_GROUPEMENT = " . $expLibelle . " AND groupementtype.LIBELLE_GROUPEMENTTYPE = " . $expLibelleGroupementType . ");";

       return $this->getAdapter()->fetchAll($select);
    }

    public function deleteGroupement($id)
    {
        $this->getAdapter()->query("DELETE FROM `groupementcommune` WHERE `groupementcommune`.`ID_GROUPEMENT` = $id;");
        $this->getAdapter()->query("DELETE FROM `groupementpreventionniste` WHERE `groupementpreventionniste`.`ID_GROUPEMENT` = $id;");
        $this->getAdapter()->query("DELETE FROM `groupement` WHERE `groupement`.`ID_GROUPEMENT` = $id;");
    }

    public function getPreventionnistes($id)
    {
        $select = $this	->select()
                        ->setIntegrityCheck(false)
                        ->from("groupementpreventionniste")
                        ->join("utilisateur", "utilisateur.ID_UTILISATEUR = groupementpreventionniste.ID_UTILISATEUR")
                        ->join("utilisateurinformations", "utilisateurinformations.ID_UTILISATEURINFORMATIONS = utilisateur.ID_UTILISATEURINFORMATIONS")
                        ->where("groupementpreventionniste.ID_GROUPEMENT = '$id'")
                        ->order("utilisateurinformations.NOM_UTILISATEURINFORMATIONS ASC");

        return $this->fetchAll($select)->toArray();

    }

    public function getPreventionnistesByGpt($groupements)
    {
        $preventionnistes_par_gpt = array();

        foreach ($groupements as $groupement) {

            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("groupementpreventionniste", null)
                ->join("utilisateur", "utilisateur.ID_UTILISATEUR = groupementpreventionniste.ID_UTILISATEUR")
                ->join("utilisateurinformations", "utilisateurinformations.ID_UTILISATEURINFORMATIONS = utilisateur.ID_UTILISATEURINFORMATIONS")
                ->where("groupementpreventionniste.ID_GROUPEMENT = ?", $groupement['ID_GROUPEMENT'])
                ->order("utilisateurinformations.NOM_UTILISATEURINFORMATIONS ASC");

            $rowset = $this->fetchAll($select);

            if($rowset == null) {
                continue;
            }

            $preventionnistes_par_gpt[$groupement['ID_GROUPEMENT']] = $rowset->toArray();
        }

        return $preventionnistes_par_gpt;
    }

    public function getGroupementParVille($code_insee)
    {
        $select = $this	->select()
                        ->setIntegrityCheck(false)
                        ->from("groupement")
                        ->joinInner("groupementcommune", "groupementcommune.ID_GROUPEMENT = groupement.ID_GROUPEMENT", null)
                        ->joinInner("groupementtype", "groupementtype.ID_GROUPEMENTTYPE = groupement.ID_GROUPEMENTTYPE", "LIBELLE_GROUPEMENTTYPE")
                        ->where("groupementcommune.NUMINSEE_COMMUNE = '$code_insee'")
                        ->order('groupementtype.ID_GROUPEMENTTYPE ASC')
                        ->order('LIBELLE_GROUPEMENT ASC');

        return $this->fetchAll($select)->toArray();
    }

    public function getAllWithTypes()
    {
        $select = $this	->select()
                        ->distinct()
                        ->setIntegrityCheck(false)
                        ->from("groupement")
                        ->joinLeft("groupementcommune", "groupementcommune.ID_GROUPEMENT = groupement.ID_GROUPEMENT", null)
                        ->joinLeft("groupementtype", "groupementtype.ID_GROUPEMENTTYPE = groupement.ID_GROUPEMENTTYPE", "LIBELLE_GROUPEMENTTYPE")
                        ->order('groupementtype.ID_GROUPEMENTTYPE ASC')
                        ->order('LIBELLE_GROUPEMENT ASC');

        return $this->fetchAll($select)->toArray();
    }

    public function getByEtablissement(array $ids_etablissement = array()) {

        $select = $this	->select()
                        ->setIntegrityCheck(false)
                        ->from("etablissementadresse", array("etablissementadresse.ID_ETABLISSEMENT"))
                        ->joinLeft("groupementcommune", "etablissementadresse.NUMINSEE_COMMUNE = groupementcommune.NUMINSEE_COMMUNE", array(
                            "groupementcommune.ID_GROUPEMENT",
                            "groupementcommune.NUMINSEE_COMMUNE",
                        ))
                        ->where("etablissementadresse.ID_ETABLISSEMENT IN(?)", $ids_etablissement)
                        ->group(array('etablissementadresse.ID_ETABLISSEMENT', 'groupementcommune.ID_GROUPEMENT'));

        return $this->fetchAll($select)->toArray();
    }
}
