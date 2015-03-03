<?php

class Service_GroupementCommunes
{
    /**
     * Récupération de tous les groupements
     *
     * @param int numinsee Optionnel
     * @return array
     */
    public function findAll($num_insee = null)
    {
        $model_groupement = new Model_DbTable_Groupement;

        if($num_insee !== null) {
            return $model_groupement->getGroupementParVille($num_insee);
        }

        return $model_groupement->fetchAll()->toArray();
    }
    
    public function findGroupementAndGroupementType($num_insee = null)
    {
        $model_groupement = new Model_DbTable_Groupement;

        if($num_insee !== null) {
            return $model_groupement->getGroupementParVille($num_insee);
        }
        return $model_groupement->getAllWithTypes();
    }
    
    public function findGroupementForEtablissement(array $ids_etablissement = array())
    {
        $model_groupement = new Model_DbTable_Groupement;

        return $model_groupement->getByEtablissement($ids_etablissement);
    }
}