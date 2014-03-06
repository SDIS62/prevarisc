<?php

class Model_DbTable_EtsTextesAppl extends Zend_Db_Table_Abstract
{
    protected $_name="etablissementtextapp";
	protected $_primary = array("ID_TEXTESAPPL","ID_ETABLISSEMENT");
		
    public function recupTextes($id_etablissement)
	{
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from("etablissementtextapp", null)
            ->join("textesappl", "etablissementtextapp.ID_TEXTESAPPL = textesappl.ID_TEXTESAPPL", array("ID_TEXTESAPPL", "LIBELLE_TEXTESAPPL"))
            ->join('typetextesappl','textesappl.ID_TYPETEXTEAPPL = typetextesappl.ID_TYPETEXTEAPPL')
            ->where("ID_ETABLISSEMENT = ?", $id_etablissement);

        $results = $this->fetchAll($select);

        return $results !== null ? $results->toArray() : array();
	}
}
