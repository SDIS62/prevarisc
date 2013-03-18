<?php

class Model_DbTable_PrescriptionArticle extends Zend_Db_Table_Abstract
{

    protected $_name="prescriptionarticle"; // Nom de la base
    protected $_primary = "ID_ARTICLE"; // Clé primaire

    public function selectArticle($crit)
    {
        //Autocomplétion sur la liste des articles
        $select = "SELECT *
            FROM prescriptionarticle
            WHERE LIBELLE_ARTICLE LIKE '".$crit."%';
        ";

        return $this->getAdapter()->fetchAll($select);
    }

    public function verifArticleExiste($article)
    {
        $select = "SELECT ID_ARTICLE
            FROM prescriptionarticle
            WHERE LIBELLE_ARTICLE LIKE '".$article."';
        ";

        return $this->getAdapter()->fetchRow($select);
    }

}
