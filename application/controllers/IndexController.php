<?php

class IndexController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $service_feed = new Service_Feed;
        $service_dashboard = new Service_Dashboard;
        $service_user = new Service_User;

        $blocsConfig = array(

            // lié aux commissions
            'nextCommissions' => array(
                'service' => $service_dashboard,
                'method'  => 'getNextCommission',
                'acl'     => array('dashboard', 'view_next_commissions'),
                'title'   => 'Prochaines commissions',
                'type'    => 'commissions',
                'height'  => 'small',
                'width'   => 'small',
            ),

            'nextCommissionsOdj' => array(
                'service' => $service_dashboard,
                'method'  => 'getNextCommission',
                'acl'     => array('dashboard', 'view_next_commissions_odj'),
                'title'   => 'Prochaines commissions',
                'type'    => 'odj',
                'height'  => 'small',
                'width'   => 'small',
            ),

            // lié aux établissements
            'ERPSuivis' => array(
                'service' => $service_dashboard,
                'method'  => 'getERPSuivis',
                'acl'     => array('dashboard', 'view_ets_suivis'),
                'title'   => 'Etablissements suivis',
                'type'    => 'etablissements',
                'height'  => 'small',
                'width'   => 'medium',
            ),
            'ERPOuvertsSousAvisDefavorable' => array(
                'service' => $service_dashboard,
                'method'  => 'getERPOuvertsSousAvisDefavorable',
                'acl'     => array('dashboard', 'view_ets_avis_defavorable'),
                'title'   => 'Etablissements sous avis défavorable',
                'type'    => 'etablissements',
                'height'  => 'small',
                'width'   => 'small',
                ),
                'ERPOuvertsSousAvisDefavorableSuivis' => array(
                'service' => $service_dashboard,
                'method'  => 'getERPOuvertsSousAvisDefavorableSuivis',
                'acl'     => array('dashboard', 'view_ets_avis_defavorable_suivis'),
                'title'   => 'Etablissements suivis sous avis défavorable',
                'type'    => 'etablissements',
                'height'  => 'small',
                'width'   => 'small',
                ),
                'ERPOuvertsSousAvisDefavorableSurCommune' => array(
                'service' => $service_dashboard,
                'method'  => 'getERPOuvertsSousAvisDefavorableSurCommune',
                'acl'     => array('dashboard', 'view_ets_avis_defavorable_sur_commune'),
                'title'   => 'Etablissements de votre commune sous avis défavorable',
                'type'    => 'etablissements',
                'height'  => 'small',
                'width'   => 'small',
                ),
                'ERPSansPreventionniste' => array(
                'service' => $service_dashboard,
                'method'  => 'getERPSansPreventionniste',
                'acl'     => array('dashboard', 'view_ets_sans_preventionniste'),
                'title'   => 'Etablissements sans préventionnistes',
                'type'    => 'etablissements',
                'height'  => 'small',
                'width'   => 'small',
                ),
                'ERPOuvertsSansProchainesVisitePeriodiques' => array(
                'service' => $service_dashboard,
                'method'  => 'getERPOuvertsSansProchainesVisitePeriodiques',
                'acl'     => array('dashboard', 'view_ets_ouverts_sans_prochaine_vp'),
                'title'   => 'Etablissements sans prochaine VP cette année',
                'type'    => 'etablissements',
                'height'  => 'small',
                'width'   => 'small',
                ),

                // lié aux dossiers
                'DossiersSuivisSansAvis' => array(
                'service' => $service_dashboard,
                'method'  => 'getDossiersSuivisSansAvis',
                'acl'     => array('dashboard', 'view_doss_suivis_sans_avis'),
                'title'   => 'Dossiers suivis sans avis du rapporteur',
                'type'    => 'dossiers',
                'height'  => 'small',
                'width'   => 'small',
                ),
                'DossiersSuivisNonVerrouilles' => array(
                'service' => $service_dashboard,
                'method'  => 'getDossiersSuivisNonVerrouilles',
                'acl'     => array('dashboard', 'view_doss_suivis_unlocked'),
                'title'   => 'Dossiers suivis non verrouillés',
                'type'    => 'dossiers',
                'height'  => 'small',
                'width'   => 'small',
                ),
                'DossierDateCommissionEchu' => array(
                'service' => $service_dashboard,
                'method'  => 'getDossierDateCommissionEchu',
                'acl'     => array('dashboard', 'view_doss_sans_avis'),
                'title'   => 'Dossiers sans avis de commission',
                'type'    => 'dossiers',
                'height'  => 'small',
                'width'   => 'small',
                ),
                'DossierAvecAvisDiffere' => array(
                'service' => $service_dashboard,
                'method'  => 'getDossierAvecAvisDiffere',
                'acl'     => array('dashboard', 'view_doss_avis_differe'),
                'title'   => 'Dossiers avec avis différés',
                'type'    => 'dossiers',
                'height'  => 'small',
                'width'   => 'small',
                ),
                'CourrierSansReponse' => array(
                'service' => $service_dashboard,
                'method'  => 'getCourrierSansReponse',
                'acl'     => array('dashboard', 'view_courrier_sans_reponse'),
                'title'   => 'Courriers sans réponse',
                'type'    => 'dossiers',
                'height'  => 'small',
                'width'   => 'small',
                ),

                // autres blocs
                'feeds' => array(
                'service' => $service_feed,
                'method'  => 'getFeeds',
                'acl'     => null,
                'title'   => 'Messages',
                'type'    => 'feeds',
                'height'  => 'small',
                'width'   => 'small',
                ),

                );

                $identity = Zend_Auth::getInstance()->getIdentity();
                $user = $service_user->find($identity['ID_UTILISATEUR']);
                $acl = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('acl');
                $profil = $user['group']['LIBELLE_GROUPE'];
                $blocs = array();
                foreach($blocsConfig as $blocId => $blocConfig) {
                    if (!$blocConfig['acl'] || ($acl->isAllowed($profil, $blocConfig['acl'][0], $blocConfig['acl'][1]))) {
                        $method = $blocConfig['method'];
                        $blocs[$blocId] = array(
                        'data' => $blocConfig['service']->$method($user),
                        'type' => $blocConfig['type'],
                        'title' => $blocConfig['title'],
                        'height' => $blocConfig['height'],
                        'width' => $blocConfig['width'],
                        );
                    }
                }

                // determine the bloc order
                // user preferences
                if (isset($user['preferences']['DASHBOARD_BLOCS'])
                && $user['preferences']['DASHBOARD_BLOCS']
                && $blocsOrder = json_decode($user['preferences']['DASHBOARD_BLOCS'])
                ) {
                    // treat the case where there will be new bloc added
                    foreach(array_keys($blocsConfig) as $defaultBloc) {
                        if (!in_array($defaultBloc, $blocsOrder)) {
                            $blocsOrder[] = $defaultBloc;
                        }
                    }
                } else {
                    $blocsOrder = array_keys($blocsConfig);
                }

                $this->view->user = $user;
                $this->view->blocs = $blocs;
                $this->view->blocsOrder = $blocsOrder;
                $this->view->inlineScript()->appendFile("/js/jquery.packery.pkgd.min.js");
                $this->_helper->layout->setLayout('index');
                $this->render('index');
            }

            public function addMessageAction()
            {
                $service_feed = new Service_Feed;
                $service_user = new Service_User;

                $this->view->groupes = $service_user->getAllGroupes();
                if ($this->_request->isPost()) {
                    try {
                        $service_feed->addMessage($this->_request->getParam('type'), $this->_request->getParam('text'), Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR'], $this->_request->getParam('conf') );
                        $this->_helper->flashMessenger(array('context' => 'success','title' => 'Message ajouté !','message' => 'Le message a bien été ajouté.'));
                    } catch (Exception $e) {
                        $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Erreur !','message' => 'Erreur lors de l\'ajout du message : ' . $e->getMessage()));
                    }
                    $this->_helper->redirector('index', 'index');
                }
            }

            public function deleteMessageAction()
            {
                $service_feed = new Service_Feed;

                try {
                    $service_feed->deleteMessage($this->_request->getParam('id'));
                    $this->_helper->flashMessenger(array('context' => 'success','title' => 'Message supprimé !','message' => 'Le message a bien été supprimé.'));
                } catch (Exception $e) {
                    $this->_helper->flashMessenger(array('context' => 'danger','title' => 'Erreur !','message' => 'Erreur lors de la suppression du message : ' . $e->getMessage()));
                }
                $this->_helper->redirector('index', 'index');
            }
        }
