<?php

	class Model_DbTable_CoucheCarto extends Zend_Db_Table_Abstract {

		protected $_name="couchecarto"; // Nom de la base
		protected $_primary="ID_COUCHECARTO"; // Nom de la base
		
		public function getList() {
		
			$select = $this->select()
				->setIntegrityCheck(false)
				->from("couchecarto")
				->joinLeft("couchecartotype", "couchecarto.ID_COUCHECARTOTYPE = couchecartotype.ID_COUCHECARTOTYPE");
			
			return ( $this->fetchAll( $select ) != null ) ? $this->fetchAll( $select ) : null;
		}
		
		public function getInteractList() {
		
			$select = $this->select()
				->setIntegrityCheck(false)
				->from("couchecarto")
				->joinLeft("couchecartotype", "couchecarto.ID_COUCHECARTOTYPE = couchecartotype.ID_COUCHECARTOTYPE")
				->where("INTERACT_COUCHECARTO = 1");
			
			$all = $this->fetchAll( $select );
			
			if(count($all) == 0)
				return array();
				
			$result = array();
			
			foreach($all as $row) {
				$result[] = $row->LAYERS_COUCHECARTO;
			}
			
			return $result;
		}

	}