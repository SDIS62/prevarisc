<?php
    class Model_DbTable_News extends Zend_Db_Table_Abstract
    {
        protected $_name="news";
        protected $_primary = "ID_NEWS";

        // Donne le fil d'actualité
        public function getNews( $id_groupe, $page = 1, $timestamp = null )
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("news")
                ->join("newsgroupe", "news.ID_NEWS = newsgroupe.ID_NEWS", null)
                ->join("utilisateur", "news.ID_UTILISATEUR = utilisateur.ID_UTILISATEUR")
                ->join("utilisateurinformations", "utilisateurinformations.ID_UTILISATEURINFORMATIONS = utilisateur.ID_UTILISATEURINFORMATIONS")
                ->where("newsgroupe.ID_GROUPE IN ( ".$id_groupe." )")
                ->order("ID_NEWS DESC");

            if( $timestamp )
                $select->where("news.ID_NEWS >= $timestamp");

            if ($page == null) {
                $liste = $this->fetchAll($select)->toArray();
            } else {
                $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($this->fetchAll($select)->toArray()));
                $paginator->setItemCountPerPage(25);
                $paginator->setCurrentPageNumber($page);
                $liste = $paginator;
            }

            $result = $liste;

            $droits = (array) Zend_Controller_Action_HelperBroker::getStaticHelper('Droits')->get();

            foreach ($result as &$row) {

                $row = array_merge($row, $droits);
            }

            return $result;
        }

        // Ajout d'un message
        public function add($type, $text, $destinataires)
        {
            // La clé unique du fil d'actualité
            $date = new Zend_Date();

            // Ajout du message
            $this->getAdapter()->query( "INSERT INTO news VALUES( '" . $date->get(Zend_Date::TIMESTAMP) . "', '$type', ".$this->getAdapter()->quote($text).", '" . Zend_Auth::getInstance()->getIdentity()->ID_UTILISATEUR . "');" );

            // Ajout des destinataires du message
            foreach($destinataires as $value)
                $this->getAdapter()->query( "INSERT INTO newsgroupe VALUES( '" . $date->get(Zend_Date::TIMESTAMP) . "', '$value' );" );
        }

        public function deleteNews($id_news)
        {
            $this->getAdapter()->query( "DELETE FROM newsgroupe WHERE ID_NEWS = " . $id_news );
            $this->delete( "ID_NEWS = " . $id_news );

        }
    }
