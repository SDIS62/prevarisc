<?php
	class Application_Layout_Helper_Header extends Zend_Controller_Action_Helper_Abstract {
	
		protected $auth_user;
		protected $helper_avatar;

		public function __construct() {
		
			// On récupère l'instance utilisateur
			$this->auth_user = Zend_Auth::getInstance()->getIdentity();
			
			// On récupère l'helper avatar
			require_once(APPLICATION_PATH . "/views/helpers/Avatar.php");
			$this->helper_avatar = new Application_View_Helper_Avatar;
		}
	
		public function header() {
		
			// Ligne de déco.
			echo "<p id='layout-header-connexion'><a href='/user/logout'><img src='/images/logout.png' alt='deconnexion' /></a></p>";

			// Menu
			echo "<div id='layout-header' >";
			echo "<h1>Prevarisc</h1>";
			
			if(strtoupper(APPLICATION_ENV) == "DEVELOPMENT")
				echo "<span id='version_flag'>DEVELOPPEUR</span>";
			
			echo "<ul id='layout-header-menu'>";
			echo "<li><a href='/'>Accueil</a></li>";
			echo "<li><a href='/statistiques'>Extractions - Statistiques</a></li>";
			echo "<li><a href='/search'>Recherche</a></li>";
			echo "<li><a href='/user/me'>".$this->auth_user->NOM_UTILISATEURINFORMATIONS . " " . $this->auth_user->PRENOM_UTILISATEURINFORMATIONS;
			
			// Avatar de l'utilisateur
			$this->helper_avatar->avatar($this->auth_user->ID_UTILISATEUR);
			
			echo "</a></li>";
			echo "</ul>";
			echo "</div>";
		}
	}