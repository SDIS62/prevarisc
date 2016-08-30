<?php

class Service_Commission
{
    /**
     * Récupération de l'ensemble des commissions
     *
     * @return array
     */
    public function getAll()
    {
    	$DB_commission = new Model_DbTable_Commission;
    	return $DB_commission->fetchAllPK();
    }

    /**
     * Récupération de l'ensemble des commissions
     *
     * @return array
     */
    public function getAllView()
    {
        // Modèle de données
       $model_typesDesCommissions = new Model_DbTable_CommissionType();
       $model_commission = new Model_DbTable_Commission();
       // On cherche tous les types de commissions
       $rowset_typesDesCommissions = $model_typesDesCommissions->fetchAll();
       // Tableau de résultats
       $array_commissions = array();
       // Pour tous les types, on cherche leur commission
       foreach ($rowset_typesDesCommissions as $row_typeDeCommission) {
           $array_results = $model_commission->fetchAll("ID_COMMISSIONTYPE = " . $row_typeDeCommission->ID_COMMISSIONTYPE )->toArray();
           $array_results2 = array();
           foreach ($array_results as $item) {
             $array_results2[] = array(
               "ID_COMMISSION" => $item["ID_COMMISSION"],
               "LIBELLE_COMMISSION" => $item["LIBELLE_COMMISSION"],
               "DOCUMENT_CR" => $item["DOCUMENT_CR"],
               "ID_COMMISSIONTYPE" => $item["ID_COMMISSIONTYPE"],
               "LIBELLE_COMMISSIONTYPE" => $row_typeDeCommission->LIBELLE_COMMISSIONTYPE
             );
           }
           $array_commissions[$row_typeDeCommission->ID_COMMISSIONTYPE] = array(
               "LIBELLE" => $row_typeDeCommission->LIBELLE_COMMISSIONTYPE,
               "ARRAY" => $array_results2
           );
       }
       return $array_commissions;
    }

    public function getCommissionsAndTypes()
    {
        // Modèle de données
        $model_typesDesCommissions = new Model_DbTable_CommissionType;
        $model_commission = new Model_DbTable_Commission;

        // On cherche tous les types de commissions
        $rowset_typesDesCommissions = $model_typesDesCommissions->fetchAll();

        // Tableau de résultats
        $array_commissions = array();

        // Pour tous les types, on cherche leur commission
        foreach ($rowset_typesDesCommissions as $row_typeDeCommission) {
            $array_commissions[$row_typeDeCommission->ID_COMMISSIONTYPE] = array(
                "LIBELLE" => $row_typeDeCommission->LIBELLE_COMMISSIONTYPE,
                "ARRAY" => $model_commission->fetchAll("ID_COMMISSIONTYPE = " . $row_typeDeCommission->ID_COMMISSIONTYPE )->toArray()
            );
        }
        return $array_commissions;
    }
}
