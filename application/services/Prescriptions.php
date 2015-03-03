<?php

class Service_Prescriptions
{
    /* GESTION DES TEXTES */
    public function getTextesListe(){
        $dbPrescTextes = new Model_DbTable_PrescriptionTexteListe;
        $liste_textes = $dbPrescTextes->getAllTextes();

        return $liste_textes;
    } //FIN getTextesListe

    public function getTexte($id_texte){
        $dbPrescTextes = new Model_DbTable_PrescriptionTexteListe;
        $texteInfo = $dbPrescTextes->getTexte($id_texte);

        return $texteInfo;
    } //FIN getTexte

    public function saveTexte($post, $idTexte = null){
        $dbPrescTextes = new Model_DbTable_PrescriptionTexteListe;
        if($idTexte == null){
            $texte = $dbPrescTextes->createRow();
            $texte->LIBELLE_TEXTE = $post['LIBELLE_TEXTE'];
            $texte->VISIBLE_TEXTE = $post['VISIBLE_TEXTE'];
            $texte->save();
        }else{
            $texte = $dbPrescTextes->find($idTexte)->current();
            $texte->LIBELLE_TEXTE = $post['LIBELLE_TEXTE'];
            $texte->VISIBLE_TEXTE = $post['VISIBLE_TEXTE'];
            $texte->save();
        }
    } //FIN saveTexte

    public function replaceTexte($newId, $oldId){
        $dbPrescTextes = new Model_DbTable_PrescriptionTexteListe;
        if($newId != '' && $oldId != ''){
            $dbPrescTextes->replace($newId,$oldId);
        }
    }

    /* GESTION DES ARTICLES */
    public function getArticlesListe(){
        $dbPrescArticles = new Model_DbTable_PrescriptionArticleListe;
        $liste_articles = $dbPrescArticles->getAllArticles();

        return $liste_articles;
    } //FIN getArticlesListe

    public function getArticle($id_article){
        $dbPrescArticles = new Model_DbTable_PrescriptionArticleListe;
        $articleInfo = $dbPrescArticles->getArticle($id_article);

        return $articleInfo;
    } //FIN getArticle

    public function saveArticle($post, $idArticle = null){
        $dbPrescArticles = new Model_DbTable_PrescriptionArticleListe;
        if($idArticle == null){
            $texte = $dbPrescArticles->createRow();
            $texte->LIBELLE_ARTICLE = $post['LIBELLE_ARTICLE'];
            $texte->VISIBLE_ARTICLE = $post['VISIBLE_ARTICLE'];
            $texte->save();
        }else{
            $texte = $dbPrescArticles->find($idArticle)->current();
            $texte->LIBELLE_ARTICLE = $post['LIBELLE_ARTICLE'];
            $texte->VISIBLE_ARTICLE = $post['VISIBLE_ARTICLE'];
            $texte->save();
        }
    } //FIN saveArticle

    public function replaceArticle($newId, $oldId){
        $dbPrescArticles = new Model_DbTable_PrescriptionArticleListe;
        if($newId != '' && $oldId != ''){
                $dbPrescArticles->replace($newId,$oldId);
        }
    }

    /* GESTION DES PRESCRIPTIONS */
    public function savePrescription($post, $idPrescription = null){
        $dbPrescRegl = new Model_DbTable_PrescriptionRegl();
        $dbPrescReglAssoc = new Model_DbTable_PrescriptionReglAssoc();

        if ($idPrescription == null) {
            $prescRegl = $dbPrescRegl->createRow();
        }else{
            $prescRegl = $dbPrescRegl->find($idPrescription)->current();
        }

        $prescRegl->PRESCRIPTIONREGL_LIBELLE = $post['PRESCRIPTION_LIBELLE'];
        $prescRegl->PRESCRIPTIONREGL_TYPE = $post['PRESCRIPTIONREGL_TYPE'];
        $prescRegl->PRESCRIPTIONREGL_VISIBLE = $post['PRESCRIPTIONREGL_VISIBLE'];
        $prescRegl->save();

        if($idPrescription != null){
            $prescAssocDelete = $dbPrescReglAssoc->getAdapter()->quoteInto('ID_PRESCRIPTIONREGL = ?', $post['idPrescription']);
            $dbPrescReglAssoc->delete($prescAssocDelete);
        }

        $nombreAssoc = count($post['texte']);
        for ($i = 0; $i< $nombreAssoc; $i ++) {
            $newAssoc = $dbPrescReglAssoc->createRow();
            $newAssoc->ID_PRESCRIPTIONREGL = $prescRegl->ID_PRESCRIPTIONREGL;
            $newAssoc->NUM_PRESCRIPTIONASSOC = $i + 1;
            if($post['texte'][$i] == 0 || $post['texte'][$i] == ''){
                $texe = 1;
            }else{
                $texe = $post['texte'][$i];
            }
            $newAssoc->ID_TEXTE = $texe;
            if($post['article'][$i] == 0 || $post['article'][$i] == ''){
                $article = 1;
            }else{
                $article = $post['article'][$i];
            }
            $newAssoc->ID_ARTICLE = $article;
            $newAssoc->save();
        }
    } //FIN savePrescription

    public function getPrescriptions($type){
        $dbPrescRegl = new Model_DbTable_PrescriptionRegl();
        $listePrescDossier = $dbPrescRegl->recupPrescRegl($type);
        //Zend_Debug::dump($listePrescDossier);

        $dbPrescReglAssoc = new Model_DbTable_PrescriptionReglAssoc();
        //Zend_Debug::dump($listePrescDossier);

        $prescriptionArray = array();
        foreach ($listePrescDossier as $val => $ue) {
                $assoc = $dbPrescReglAssoc->getPrescriptionReglAssoc($ue['ID_PRESCRIPTIONREGL']);
                array_push($prescriptionArray, $assoc);
        }
        return $prescriptionArray;
    } //FIN getPrescriptions

    public function getPrescriptionInfo($idPrescription,$type){
        if($type == 'rappel-reg'){
            $dbPrescAssoc = new Model_DbTable_PrescriptionReglAssoc();
            return $dbPrescAssoc->getPrescriptionReglAssoc($idPrescription);
        }
    } //FIN getPrescriptionInfo
    
} //FIN SERVICE
