<?php
	class Zend_View_Helper_DateJqueryToBd
	{
		public function dateJqueryToBd($date1)
		{
			$dateTab = explode("/",$date1);
			return $dateTab[2]."-".$dateTab[1]."-".$dateTab[0];
		}
	}
?>