<?php

    class IndexController extends Zend_Controller_Action
    {
        // Initialisation
        public function init()
        {
            // Gestionnaire de contexte
            $this->_helper->AjaxContext->addActionContext('about', 'html')
                                       ->initContext();
        }

        // Page d'accueil
        public function indexAction()
        {
            // Titre
            $this->view->title = "Accueil";

            // On check si l'utilisateur peut accéder à cette partie
            $this->view->droits = $this->_helper->Droits()->get();
        }

        // Le a-propos du site
        public function aboutAction()
        {
            // Titre
            $this->view->title = "A Propos";
        }

        // L'aide du site
        public function helpAction()
        {
            // Titre
            $this->view->title = "Aide";
        }

        // Rappels utilisateur
        public function rappelsAction()
        {
            // Zend_Auth::getInstance()->getIdentity()->ID_UTILISATEUR

            // Modèles
            $model_etablissement = new Model_DbTable_Etablissement;

            // Zend_Debug::Dump(Zend_Date::MONTH);

            // Tableau des rappels
            $array_rappels = null;

            $user = Zend_Auth::getInstance()->getIdentity();

            // On récupère les établissements surveillé par l'utilisateur
            $array_etablissementsSurveilles = $model_etablissement->getByUser($user->ID_UTILISATEUR);

            if ( count($array_etablissementsSurveilles) > 0 ) {

                // Tableau des rappels
                $array_rappels = array(
                    "visites_a_venir" => array()
                );

                foreach ($array_etablissementsSurveilles as $row) {

                    // On récupère la prochaine date
                    $next_visite =  $model_etablissement->getVisiteNextPeriodique( $row["ID_ETABLISSEMENT"] ) != null ? $model_etablissement->getVisiteNextPeriodique( $row["ID_ETABLISSEMENT"] ) : null;

                    if ($next_visite != null) {

                        // On substract
                        $diff = $next_visite->sub(new Zend_Date)->toValue();
                        $diff_month = floor(((($diff/60)/60)/24)/30);

                        // On check les visites a venir (-1 mois)
                        if ($diff_month == 0) {

                            $array_rappels["visites_a_venir"][] = "L'établissement <a href='/etablissement/index/id/" . $row["ID_ETABLISSEMENT"] . "'>" . $row["LIBELLE_ETABLISSEMENTINFORMATIONS"] ."</a> est bientôt sujet à une visite périodique.";
                        }

                        // On check les visites depassées
                        if ($diff_month < 0) {

                            $array_rappels["visites_a_venir"][] = "L'établissement <a href='/etablissement/index/id/" . $row["ID_ETABLISSEMENT"] . "'>" . $row["LIBELLE_ETABLISSEMENTINFORMATIONS"] ."</a> n'a pas été visité dans les temps.";
                        }
                    } elseif ($row["PERIODICITE__ETABLISSEMENTINFORMATIONS"] != 0) {

                        $array_rappels["visites_a_venir"][] = "L'établissement <a href='/etablissement/index/id/" . $row["ID_ETABLISSEMENT"] . "'>" . $row["LIBELLE_ETABLISSEMENTINFORMATIONS"] ."</a> n'a pas encore reçu de visite.";
                    }
                }
            }

            $this->view->array_rappels = $array_rappels;
        }

    }
