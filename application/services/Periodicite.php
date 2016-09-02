<?php

class Service_Periodicite
{
    /**
     * Récupération du tableau des periodicités formaté.
     *
     * @return array
     */
    public function getAll()
    {
        // Model
    	$perio_model = new Model_DbTable_Periodicite();

        // Les périodicités
        $tableau = $perio_model->fetchAll()->toArray();

        // On formate le tableau des périodicité pour la vue
        $result = array();
        for ($i=0; $i < count($tableau); $i++) {
            // Sans local sommeil
            $result[$tableau[$i]["ID_CATEGORIE"]][$tableau[$i]["ID_TYPE"]][$tableau[$i]["LOCALSOMMEIL_PERIODICITE"]] = $tableau[$i]["PERIODICITE_PERIODICITE"];
            // Avec local (on exclu igh == categ à 0)
            if($tableau[$i]["ID_CATEGORIE"] != 0) {
                $result[$tableau[$i++]["ID_CATEGORIE"]][$tableau[$i]["ID_TYPE"]][$tableau[$i]["LOCALSOMMEIL_PERIODICITE"]] = $tableau[$i]["PERIODICITE_PERIODICITE"];
            }
        }

        return $result;
    }

    /**
     * Sauvegarde du tableau des périodicités
     *
     * @param array
     */
    public function save($request)
    {
        // Récupération de la configuration
        $options = Zend_Registry::get('options');

        // Model
        $perio_model = new Model_DbTable_Periodicite();

        foreach ($request as $key => $value ) {
            $result = explode("_", $key);
            $item = $perio_model->find($result[0], $result[1], $result[2])->current() == null ? $perio_model->createRow() : $perio_model->find($result[0], $result[1], $result[2])->current();
            $item->ID_CATEGORIE = $result[0];
            $item->ID_TYPE = $result[1];
            $item->LOCALSOMMEIL_PERIODICITE = $result[2];
            $item->PERIODICITE_PERIODICITE = in_array($result[1], $options['types_sans_local_sommeil']) ? 0 : $value;
            $item->save();
        }
    }

    /**
     * Application du tableau des periodicités sur tous les établissements
     */
    public function apply()
    {
        $perio_model = new Model_DbTable_Periodicite();
        $perio_model->apply();
    }
}
