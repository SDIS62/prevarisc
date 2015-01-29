<?php

    class Service_Prescriptions
    {
        public function getTextesListe()
        {
            $dbPrescTextes = new Model_DbTable_PrescriptionTexteListe;
            $liste_textes = $dbPrescTextes->getAllTextes();

            return $liste_textes;
        }

        public function getTexte($id_texte)
        {
            $dbPrescTextes = new Model_DbTable_PrescriptionTexteListe;
            $texteInfo = $dbPrescTextes->getTexte($id_texte);

            return $texteInfo;
        }

        public function saveTexte($post)
        {
            $dbPrescTextes = new Model_DbTable_PrescriptionTexteListe;
            if($post['action'] == 'add'){
                $texte = $dbPrescTextes->createRow();
                $texte->LIBELLE_TEXTE = $post['LIBELLE_TEXTE'];
                $texte->VISIBLE_TEXTE = $post['VISIBLE_TEXTE'];
                $texte->save();
            }else if($post['action'] == 'edit'){
                $texte = $dbPrescTextes->find($post['id_texte'])->current();
                $texte->LIBELLE_TEXTE = $post['LIBELLE_TEXTE'];
                $texte->VISIBLE_TEXTE = $post['VISIBLE_TEXTE'];
                $texte->save();
            }else if($post['action'] == 'replace'){
                if($post['id_texte'] != '' && $post['idTexteReplace'] != ''){
                    $dbPrescTextes->replace($post['id_texte'],$post['idTexteReplace']);
                }
            }
        }
        
    }
