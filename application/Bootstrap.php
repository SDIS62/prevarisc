<?php

	class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

		protected function _initAutoload() {
		
			$moduleLoader = new Zend_Application_Module_Autoloader(array(
				'namespace' => '',
				'basePath' => APPLICATION_PATH));
				
			$front = Zend_Controller_Front::getInstance();
			$front->registerPlugin(new Plugin_GlobalAction());

			return $moduleLoader;
		}
		
		protected function _initMyView() {
		
			// Instance de la vue
			$this->bootstrap('view');
			$view = $this->getResource('view');
			
			// Doctype
			$view->doctype('XHTML1_STRICT');

			// Titre
			$view->headTitle('Prevarisc')
				 ->setSeparator(' | ');
			
			// Balises META
			$view->headMeta()	->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
								->appendHttpEquiv('Content-Language', 'fr-FR');
							 
			// Liens vers le CSS
			$view->headLink()	->appendStylesheet('/css/reset.css')
								->appendStylesheet('/css/text.css')
								->appendStylesheet('/css/form.css')
								->appendStylesheet('/css/grid_fluid.css')
								->appendStylesheet('/css/sprite.css')
								->appendStylesheet('/css/main.css')
								->appendStylesheet('/css/jquery/jquery-ui-1.8.11.custom.css')
								->appendStylesheet('/css/jquery/jquery.tablesorter.css')
								->appendStylesheet('/css/jquery/jquery.multiselect.css')
								->appendStylesheet('/css/jquery/jquery.fullcalendar.css')
								->appendStylesheet('/css/jquery/jquery.fancybox-1.3.4.css')
								->appendStylesheet('/css/jquery/jquery.tipsy.css')
								->headLink(array('rel' => 'shortcut icon', 'href' => '/images/favicon.ico', 'type' => 'image/x-icon'), 'PREPEND')
								->headLink(array('rel' => 'icon', 'href' => '/images/favicon_32.png', 'sizes' => '32x32'));
									
			// Lien vers les scripts Javascript
			$view->inlineScript()	->appendFile('/js/jquery.min.js')
									->appendFile('/js/jquery-ui.min.js')
									->appendFile('/js/jquery.fullcalendar.js')
									->appendFile('/js/jquery.autocomplete.min.js')
									->appendFile('/js/jquery.timeentry.js')
									->appendFile('/js/jquery.dateentry.js')
									->appendFile('/js/jquery.elastic.js')
									->appendFile('/js/jquery.toggletext.js')
									->appendFile('/js/jquery.multiselect.min.js')
									->appendFile('/js/jquery.tablesorter.js')
									->appendFile('/js/jquery.tablesorter.pager.js')
									->appendFile('/js/jquery.tipsy.js')
									->appendFile('/js/jquery.fancybox-1.3.4.js');
		}
		
		protected function _initRouter() {

			$config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/routes.xml');
			$router = Zend_Controller_Front::getInstance()->getRouter();
			$router->addConfig($config, 'routes');
		}
		
		protected function _initActionHelpers() {
		
			Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/controllers/helpers', 'Application_Controller_Helper_');
		}
	}