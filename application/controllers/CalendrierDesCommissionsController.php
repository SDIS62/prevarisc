<?php

class CalendrierDesCommissionsController extends Zend_Controller_Action
{
    public function init()
    {

        $this->_helper->layout->setLayout('dashboard');
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('commissionselection', 'json')
                    ->addActionContext('recupevenement', 'json')
                    ->addActionContext('recupevenementodj', 'json')
                    ->addActionContext('recupdateliee', 'json')
                    ->initContext();
    }

    public function indexAction()
    {
        // Titre de la page
        $this->view->title = "Calendrier des commissions";

        if ($this->_getParam('idComm')) {
            $this->view->idComm = $this->_getParam('idComm');
        }

        // Modèle de données
        $model_typesDesCommissions = new Model_DbTable_CommissionType;
        $model_commission = new Model_DbTable_Commission;

        // On cherche tous les types de commissions
        $rowset_typesDesCommissions = $model_typesDesCommissions->fetchAll();

        // Tableau de résultats
        $array_commissions = array();

        // Pour tous les types, on cherche leur commission
        foreach ($rowset_typesDesCommissions as $row_typeDeCommission) {
            $array_results = $model_commission->fetchAll("ID_COMMISSIONTYPE = " . $row_typeDeCommission->ID_COMMISSIONTYPE )->toArray();
            $array_results2 = array();
            foreach($array_results as $item) {
                $array_results2[] = array(
                    "ID_COMMISSION" => $item["ID_COMMISSION"],
                    "LIBELLE_COMMISSION" => $item["LIBELLE_COMMISSION"],
                    "DOCUMENT_CR" => $item["DOCUMENT_CR"],
                    "ID_COMMISSIONTYPE" => $item["ID_COMMISSIONTYPE"],
                    "LIBELLE_COMMISSIONTYPE" => $row_typeDeCommission->LIBELLE_COMMISSIONTYPE
                );
            }
            $array_commissions[$row_typeDeCommission->ID_COMMISSIONTYPE] = array(
                "LIBELLE" => $row_typeDeCommission->LIBELLE_COMMISSIONTYPE,
                "ARRAY" => $array_results2
            );
        }

        $userId = Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR'];

        $url = sprintf("/api/1.0/calendar?userid=%s&key=%s", 
                        $userId,
                        getenv('PREVARISC_SECURITY_KEY'));

        if ($this->_getParam("idComm")) {
            $url .= sprintf("&commission=%s", $this->_getParam("idComm"));
        }

        $protocol = ( ! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'webcals': 'webcal';

        //$this->_helper->getHelper('Redirector')->gotoUrl($protocol . '://' . $_SERVER["HTTP_HOST"] . $url);
        //
        $this->view->url_webcal = $protocol . '://' . $_SERVER["HTTP_HOST"] . $url;

        $this->view->array_commissions = $array_commissions;
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');

        $this->view->is_admin = unserialize($cache->load('acl'))->isAllowed(Zend_Auth::getInstance()->getIdentity()['group']['LIBELLE_GROUPE'], "gestion_parametrages", "gestion_commissions");
    }

    public function recupdatelieeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $dbDateComm = new Model_DbTable_DateCommission;
        $infosDateComm = $dbDateComm->find($this->_getParam('idDate'))->current();

        //Une fois les infos de la date récupérées on peux aller chercher les date liées à cette commission pour les afficher
        if ( ! $infosDateComm['DATECOMMISSION_LIEES']) {
            $commPrincipale = $this->_getParam('idDate');
        } else {
            $commPrincipale = $infosDateComm['DATECOMMISSION_LIEES'];
        }

        $recupCommLiees = $dbDateComm->getCommissionsDateLieesMaster($commPrincipale);
        echo Zend_Json::encode($recupCommLiees);
    }

    //Gestion de l'affectation des dossier et de l'ordre du jour ODJ
    public function gestionodjAction()
    {
        //récuperation des informations concernant la date de commission concernant l'ordre du jour
        $dbDateComm = new Model_DbTable_DateCommission;
        $infosDateComm = $dbDateComm->find($this->_getParam('dateCommId'))->current();

        //Une fois les infos de la date récupérées on peux aller chercher les date liées à cette commission pour les afficher
        if (!$infosDateComm['DATECOMMISSION_LIEES']) {
            $commPrincipale = $this->_getParam('dateCommId');
        } else {
            $commPrincipale = $infosDateComm['DATECOMMISSION_LIEES'];
        }
        $this->view->recupCommLiees = $dbDateComm->getCommissionsDateLieesMaster($commPrincipale);

        //récupération des informations sur la commission
        $dbCommission = new Model_DbTable_Commission;
        $infosCommission = $dbCommission->find($infosDateComm['COMMISSION_CONCERNE'])->current();

        $dbCommissionType = new Model_DbTable_CommissionType;
        $infosCommissionType = $dbCommissionType->find($infosCommission['ID_COMMISSIONTYPE'])->current();
        
        //récuperation de tout les dossiers affectés à cette date de commission
        $dbDossierAffectation = new Model_DbTable_DossierAffectation;

        //Si on prend en compte les heures on récupère uniquement les dossiers n'ayant pas d'heure de passage
        $listeDossiersNonAffect = $dbDossierAffectation->getDossierNonAffect($this->_getParam('dateCommId'));

        $dbDossier = new Model_DbTable_Dossier;
        $dbDocUrba = new Model_DbTable_DossierDocUrba;
        $service_etablissement = new Service_Etablissement;
        $DB_prev = new Model_DbTable_DossierPreventionniste;

        foreach($listeDossiersNonAffect as $val => $ue)
        {
            //On recupere la liste des établissements qui concernent le dossier
            $listeEtab = $dbDossier->getEtablissementDossierGenConvoc($ue['ID_DOSSIER']);
            //on recupere la liste des infos des établissement

            if(count($listeEtab) > 0)
            {
                $etablissementInfos = $service_etablissement->get($listeEtab[0]['ID_ETABLISSEMENT']);
                $listeDossiersNonAffect[$val]['infosEtab'] = $etablissementInfos;
                $listeDocUrba = $dbDocUrba->getDossierDocUrba($ue['ID_DOSSIER']);
                $listeDossiersNonAffect[$val]['listeDocUrba'] = $listeDocUrba;

                $listeDossiersNonAffect[$val]['preventionnistes'] = $DB_prev->getPrevDossier( $ue['ID_DOSSIER'] );
            }
        }
		
        //Gestion de l'affichage de la date de la commission
        $date =  new Zend_Date($infosDateComm['DATE_COMMISSION'],'yyyy-MM-dd');
        $this->view->dateFr = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR,'fr');

        $this->view->infosDateComm = $infosDateComm;
        $this->view->infosCommission = $infosCommission;
        $this->view->infosCommissionType = $infosCommissionType;

        $this->view->listeDossierNonAffect = $listeDossiersNonAffect;
    }

    public function resizeodjAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender();
            $heureFin = new Zend_Date($this->_getParam('dateFin'),Zend_Date::ISO_8601,'en');

            $dbDossierAffectation = new Model_DbTable_DossierAffectation;
            $dossierAffectationUpdate = $dbDossierAffectation->find($this->_getParam('dateCommId'),$this->_getParam('idDossier'))->current();

            $dossierAffectationUpdate->HEURE_FIN_AFFECT = $heureFin->get('HH:mm');
            $dossierAffectationUpdate->save();
            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'L\'événement a bien été modifié',
                'message' => ''
            ));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de la modification de l\'événement',
                'message' => $e->getMessage()
            ));
        }
    }

    public function dropodjAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender();

            $heureDeb = new Zend_Date($this->_getParam('heureDebut'),Zend_Date::ISO_8601,'en');
            $heureFin = new Zend_Date($this->_getParam('heureFin'),Zend_Date::ISO_8601,'en');

            $dbDossierAffectation = new Model_DbTable_DossierAffectation;
            $dossierAffectationUpdate = $dbDossierAffectation->find($this->_getParam('dateCommId'),$this->_getParam('idDossier'))->current();

            $dossierAffectationUpdate->HEURE_DEB_AFFECT = $heureDeb->get('HH:mm');
            $dossierAffectationUpdate->HEURE_FIN_AFFECT = $heureFin->get('HH:mm');

            $dossierAffectationUpdate->save();
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur inattendue',
                'message' => $e->getMessage()
            ));
        }
    }

    public function commissionselectionAction()
    {
        //Utilisée pour l'auto complétion
        if (isset($_GET['q'])) {
            $commissions = new Model_DbTable_Commission;
            $this->view->commissionsListe = $commissions->fetchAll("LIBELLE_COMMISSION LIKE '%".$_GET['q']."%'")->toArray();
        }
    }

    public function recupevenementAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        //Permet la récupération des différents éléments du calendrier pour la commission concernée
        $dateDebut = new Zend_Date(substr($this->_request->start, 0, -3), Zend_Date::TIMESTAMP);
        $dateDebut = $dateDebut->get(Zend_Date::YEAR."-".Zend_Date::MONTH."-".Zend_Date::DAY);

        $dateFin = new Zend_Date(substr($this->_request->end, 0, -3), Zend_Date::TIMESTAMP);
        $dateFin = $dateFin->get(Zend_Date::YEAR."-".Zend_Date::MONTH."-".Zend_Date::DAY);

        $dbDateCommission = new Model_DbTable_DateCommission;

        $items = array();
        $requete = "COMMISSION_CONCERNE = '" . $this->_getParam('idComm') . "' AND DATE_COMMISSION BETWEEN '" . $dateDebut . "'	AND '" . $dateFin . "'";

        if ($this->_getParam('type')) {
            $requete .= " AND ID_COMMISSIONTYPEEVENEMENT = '".$this->_getParam('type')."'";
        }
        foreach ($dbDateCommission->fetchAll($requete)->toArray() as $commissionEvent) {
            $items[] = array(
                "id" => $commissionEvent['ID_DATECOMMISSION'],
                "title" => "   ".$commissionEvent['LIBELLE_DATECOMMISSION'],
                "start" => date($commissionEvent['DATE_COMMISSION']." ".$commissionEvent['HEUREDEB_COMMISSION']),
                "end" => date($commissionEvent['DATE_COMMISSION']." ".$commissionEvent['HEUREFIN_COMMISSION']),
                "url" => "commission/id/".$commissionEvent['ID_DATECOMMISSION'],
                "className" => "display-".$commissionEvent['ID_COMMISSIONTYPEEVENEMENT'],
                "allDay" => false,
            );
        }
        $this->view->items = $items;
    }

    public function recupevenementodjAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        //Permet la récupération des différents éléments du calendrier pour la commission concernée
        $dateDebut = new Zend_Date(substr($this->_request->start, 0, -3), Zend_Date::TIMESTAMP);
        $dateDebut = $dateDebut->get(Zend_Date::YEAR."-".Zend_Date::MONTH."-".Zend_Date::DAY);

        $dateFin = new Zend_Date(substr($this->_request->end, 0, -3), Zend_Date::TIMESTAMP);
        $dateFin = $dateFin->get(Zend_Date::YEAR."-".Zend_Date::MONTH."-".Zend_Date::DAY);

        $dbDossierAffect = new Model_DbTable_DossierAffectation;
        $listeDossiersAffect = $dbDossierAffect->getDossierAffect($this->_getParam('dateCommId'));

        $items = array();

		$dbDossier = new Model_DbTable_Dossier;
		$dbDocUrba = new Model_DbTable_DossierDocUrba;
		$service_etablissement = new Service_Etablissement;

		foreach($listeDossiersAffect as $val => $ue)
		{
			//On recupere la liste des établissements qui concernent le dossier
			$listeEtab = $dbDossier->getEtablissementDossierGenConvoc($ue['ID_DOSSIER']);
			//on recupere la liste des infos des établissement
			if(count($listeEtab) > 0)
			{
				$etablissementInfos = $service_etablissement->get($listeEtab[0]['ID_ETABLISSEMENT']);
				$listeDossiersAffect[$val]['infosEtab'] = $etablissementInfos;
				$listeDocUrba = $dbDocUrba->getDossierDocUrba($ue['ID_DOSSIER']);
				$listeDossiersAffect[$val]['listeDocUrba'] = $listeDocUrba;
			}else{
				unset($listeDossiersAffect[$val]);
			}
		}

        foreach ($listeDossiersAffect as $dossierAffect) {
			$affichage = "";
			if(isset($dossierAffect['infosEtab']['parents'][0]["LIBELLE_ETABLISSEMENTINFORMATIONS"])){
				$affichage .= $dossierAffect['infosEtab']['parents'][0]["LIBELLE_ETABLISSEMENTINFORMATIONS"]." - ";
			}
			$affichage = $dossierAffect['infosEtab']['informations']['LIBELLE_ETABLISSEMENTINFORMATIONS' ];
			
			$nbAdresse = count($dossierAffect['infosEtab']['adresses']);
			if($nbAdresse != 0){
				$affichage .= " (";
				foreach($dossierAffect['infosEtab']['adresses'] as $commune){
					$affichage .= $commune['LIBELLE_COMMUNE'];
					if($nbAdresse != 1)
						$affichage .= ", ";
					$nbAdresse--;
				}
				$affichage .= ") ";
			}else{
				$affichage .= " ( adresse non renseignée )";
			}
			
            if ($dossierAffect['LIBELLE_DOSSIERNATURE'] != "") {
                $affichage .= " - ".$dossierAffect['LIBELLE_DOSSIERNATURE'];
            }

            if ($dossierAffect['OBJET_DOSSIER'] != "") {
                $affichage .= " - Objet : ".$dossierAffect['OBJET_DOSSIER'];
            }

			if(isset($dossierAffect['listeDocUrba']) && count($dossierAffect['listeDocUrba']) > 0)
			{
				$affichage .= " - Doc urbanisme : ";
				foreach($dossierAffect['listeDocUrba'] as $val => $ue){
					$affichage .= $ue['NUM_DOCURBA']." . ";
				}
			}
			
			$DB_prev = new Model_DbTable_DossierPreventionniste;
			$preventionnistes = $DB_prev->getPrevDossier( $dossierAffect['ID_DOSSIER'] );
            if(count($preventionnistes) > 0){
				$affichage .= " (".$preventionnistes[0]['NOM_UTILISATEURINFORMATIONS']." ".$preventionnistes[0]['PRENOM_UTILISATEURINFORMATIONS'].")";
			}

			if($dossierAffect['VERROU_DOSSIER'] == 0){
				$color = "#e2a420";
			}else if($dossierAffect['VERROU_DOSSIER'] == 1){
				$color = "#83bff6";
			}

            $items[] = array(
                "id" => $dossierAffect['ID_DOSSIER'],
                "url" => "/dossier/index/id/".$dossierAffect['ID_DOSSIER'],
                "title" => "   ".$affichage,
                "start" => date($dateDebut." ".$dossierAffect['HEURE_DEB_AFFECT']),
                "end" => date($dateDebut." ".$dossierAffect['HEURE_FIN_AFFECT']),
				"backgroundColor" => $color,
                "allDay" => false,
            );

        }

        $this->view->items = $items;
    }

    public function affectedossodjAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender();

            $dbDossierAffect = new Model_DbTable_DossierAffectation;
            $dossAffect = $dbDossierAffect->find($this->_getParam('dateCommId'),$this->_getParam('idDossier'))->current();

            $dateAttribDoss = new Zend_Date($this->_getParam('datadebut'),Zend_Date::ISO_8601,'en');
            $dossAffect->HEURE_DEB_AFFECT = $dateAttribDoss->get('HH:mm');

            $dateAttribDoss->add("5", Zend_Date::MINUTE);
            $dossAffect->HEURE_FIN_AFFECT = $dateAttribDoss->get('HH:mm');
            $dossAffect->save();

			$DBdossier = new Model_DbTable_Dossier;
            $dossier = $DBdossier->find($this->_getParam('idDossier'))->current();
			//On retourne la valeur du verrou pour pour savoir la couleur à afficher dans le calendrier
			echo $dossier['VERROU_DOSSIER'];
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de l\'affectation du dossier',
                'message' => $e->getMessage()
            ));
        }
    }

    public function dialogcommAction()
    {
        $this->view->do = $this->_getParam('do');
        if ($this->view->do == 'edit') {
            //récupération de l'id de la date sur laquelle on clique
            $this->view->dateClick = $this->_getParam('idDateComm');

            //Récupération du nom de la commission
            $dbCommissions = new Model_DbTable_Commission;
            $commFind = $dbCommissions->find($this->_getParam('idComm'));
            $commSelect = $commFind->current();
            $this->view->libelleCom = $commSelect->LIBELLE_COMMISSION;

            $dbDateCommission = new Model_DbTable_DateCommission;
            $commQtipInfo = $dbDateCommission->find($this->_getParam('idDateComm'))->current();

            //Permet d'afficher les types d'evenements présents dans la BD (visite etc...)
            $dbCommTypeEvenement = new Model_DbTable_CommissionTypeEvenement;
            $libelleCom = $dbCommTypeEvenement->find($commQtipInfo['ID_COMMISSIONTYPEEVENEMENT'])->current();
            $this->view->typeComLibelle = $libelleCom->LIBELLE_COMMISSIONTYPEEVENEMENT;

            $this->view->idParam = $this->_getParam('idComm');
            $this->view->idTypeSelect = $commQtipInfo->ID_COMMISSIONTYPEEVENEMENT;

            if ($commQtipInfo->DATECOMMISSION_LIEES == NULL) {
                //On est sur la date principale
                $this->view->listeDates = $dbDateCommission->getCommissionsQtypListing($this->_getParam('idDateComm'));
                $this->view->dateCommission = $commQtipInfo->ID_DATECOMMISSION;
            } else {
                //On est sur une date liée
                $this->view->listeDates = $dbDateCommission->getCommissionsQtypListing($commQtipInfo->DATECOMMISSION_LIEES);
                $this->view->dateCommission = $dbDateCommission->find($commQtipInfo->DATECOMMISSION_LIEES)->current()->ID_DATECOMMISSION;
            }
            //Récupération du libelle de la Commission selectionnee
            $this->view->libelleDateComm = $commQtipInfo->LIBELLE_DATECOMMISSION;
        } elseif ($this->view->do == 'newComm') {
            $dbCommissions = new Model_DbTable_Commission;
            $commFind = $dbCommissions->find($this->_getParam('idComm'));
            $commSelect = $commFind->current();
            $this->view->libelleCom = $commSelect->LIBELLE_COMMISSION;

            //Permet d'afficher les types d'evenements présents dans la BD (visite etc...)
            $dbTypeEvenement = new Model_DbTable_CommissionTypeEvenement;
            $this->view->listeCommType = $dbTypeEvenement->getCommListe();

            //Récupération de la date de début puis création des variables envoyées à la vue
            $dateD = new Zend_Date($this->_getParam('dateD'),Zend_Date::DATES,'en');
            $this->view->dateCommD = $dateD->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR,'fr');
            $this->view->dateSelectD = $dateD->get(Zend_Date::YEAR."/".Zend_Date::MONTH."/".Zend_Date::DAY);

            //Récupération de la date de fin puis création des variables envoyées à la vue
            $dateF = new Zend_Date($this->_getParam('dateF'),Zend_Date::DATES,'en');
            $this->view->dateCommF = $dateF->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR,'fr');
            $this->view->dateSelectF = $dateF->get(Zend_Date::YEAR."/".Zend_Date::MONTH."/".Zend_Date::DAY);

            //Récupération des heures de début et de fin. si 00:00 toutes les 2 il s'agit de journées entières
            $HeureD = new Zend_Date($this->_getParam('dateD'),Zend_Date::ISO_8601,'en');
            $HeureF = new Zend_Date($this->_getParam('dateF'),Zend_Date::ISO_8601,'en');

            //Liste des dates selectionnées dans un tableau puis envoyées à la vue
            $listeDates = array();
            while ($dateD->compare($dateF) <= 0) {
                array_push($listeDates, array(
                    "date" => $dateD->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR,'fr'),
                    "inputH" => $dateD->get(Zend_Date::YEAR."-".Zend_Date::MONTH."-".Zend_Date::DAY),
                    "heureD" => $HeureD->get('HH:mm'),
                    "heureF" => $HeureF->get('HH:mm')
                ));
                $dateD->addDay(1);
            }
            //Envoi à la vue la liste des dates selectionnées
            $this->view->listeDates = $listeDates;

            if ($this->_getParam('libelleCom')) {
                $this->view->libelleCom = $this->_getParam('libelleCom');
            } else {
                $this->view->libelleCom = "";
            }

            if ($this->_getParam('type')) {
                $this->view->typeDoss = $this->_getParam('type');
            } else {
                $this->view->typeDoss = "";
            }

        } else {
            switch ($this->view->do) {
                case "addDateN":
                    //affiche une ligne suplémentaire dans le tableau de résumé des dates
                    $dateAjoutee = new Zend_Date($this->_getParam('date'),Zend_Date::DATES,'fr');
                    $this->view->dateAjoutee = $dateAjoutee->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR,'fr');
                    $this->view->dateAjouteeInput = $dateAjoutee->get(Zend_Date::YEAR."-".Zend_Date::MONTH."-".Zend_Date::DAY);
                break;
                //LIBELLE
                case "libelleCom":
                    //EDITION Permet de charger le formulaire de modification pour le libellé
                    $dbDateCommission = new Model_DbTable_DateCommission;
                    $commQtipInfo = $dbDateCommission->find($this->_getParam('idDateComm'))->current();
                    $this->view->libelleDateComm = $commQtipInfo->LIBELLE_DATECOMMISSION;
                break;
                case "valid_libelleCom":
                    //VALIDATION Lorsque l'on modifie le libellé de la commission programmée
                    $dbDateCommission = new Model_DbTable_DateCommission;
                    $commEdit = $dbDateCommission->find($this->_getParam('idDateComm'))->current();
                    if ($commEdit->DATECOMMISSION_LIEES == NULL) {
                        //On est sur la date maitre
                        $dbDateCommission->dateCommUpdateLibelle($this->_getParam('idDateComm'),  addslashes($this->_getParam('data')));
                    } else {
                        //Cas d'une comm liée
                        $dbDateCommission->dateCommUpdateLibelle($commEdit->DATECOMMISSION_LIEES,addslashes($this->_getParam('data')));
                    }
                    $this->view->libelleDateComm = $this->_getParam('data');
                break;
                case "annule_libelleCom":
                    //ANNULATION Lorsque l'on annule la modification du libelle (récupération de l'ancien libelle)
                        $dbDateCommission = new Model_DbTable_DateCommission;
                    $commQtipInfo = $dbDateCommission->find($this->_getParam('idDateComm'))->current();
                    $this->view->libelleDateComm = $commQtipInfo->LIBELLE_DATECOMMISSION;
                break;
                //TYPE
                case "typeCom":
                    //EDITION Permet de charger le type de commission
                        $dbTypeEvenement = new Model_DbTable_CommissionTypeEvenement;
                        $this->view->listeCommType = $dbTypeEvenement->getCommListe();

                        $dbDateCommission = new Model_DbTable_DateCommission;
                    $commEdit = $dbDateCommission->find($this->_getParam('idDateComm'))->current();
                        $this->view->typeComSelect = $commEdit['ID_COMMISSIONTYPEEVENEMENT'];
                break;
                case "valid_typeCom":
                    //VALIDATION Permet de valider le changement de type des commissions concernées
                        $idTypeSelect = $this->_getParam('typeSelect');
                        $dbDateCommission = new Model_DbTable_DateCommission;
                        $LigneComm = $dbDateCommission->find($this->_getParam('idDateComm'))->current();
                        if ($LigneComm->DATECOMMISSION_LIEES != NULL) {
                        $idUtile = $LigneComm->DATECOMMISSION_LIEES;
                    } else {
                        $idUtile = $LigneComm->ID_DATECOMMISSION;
                    }
                        $dbDateCommission->dateCommUpdateType($idUtile, $idTypeSelect);
                        $dbTypeEvenement = new Model_DbTable_CommissionTypeEvenement;
                        $infoType = $dbTypeEvenement->find($idTypeSelect)->current();
                        $this->view->libelleType = $infoType['LIBELLE_COMMISSIONTYPEEVENEMENT'];
                break;
                case "annule_typeCom":
                    //ANNULATION Permet de ne rien modifier concernant le type ré-affiche le type non modifié
                        $idDateComm = $this->_getParam('idDateComm');
                        $dbDateCommission = new Model_DbTable_DateCommission;
                        $commEdit = $dbDateCommission->find($idDateComm)->current();
                        $typeComSelect = $commEdit['ID_COMMISSIONTYPEEVENEMENT'];

                        $dbTypeEvenement = new Model_DbTable_CommissionTypeEvenement;
                        $infoType = $dbTypeEvenement->find($typeComSelect)->current();
                        $this->view->libelleType = $infoType['LIBELLE_COMMISSIONTYPEEVENEMENT'];
                break;
                //DATE
                case "dateComm":
                    //EDITION Permet de charger le formulaire de modification pour une date
                    $dbDateCommission = new Model_DbTable_DateCommission;
                    $tabInfos = $dbDateCommission->find($this->_getParam('idDateComm'))->current();
                    $this->view->dateCommDetail = $tabInfos;
                    $this->view->first = $this->_getParam('first');
                break;
                case "valid_dateCom":
                    //VALIDATION Lorsque l'on modifie la date
                    $HeureD = new Zend_Date($this->_getParam('hd'),'HH:mm','en');
                    $HeureF = new Zend_Date($this->_getParam('hf'),'HH:mm','en');
                    $date = new Zend_Date($this->_getParam('date'),Zend_Date::DATES,'fr');

                    $dbDateCommission = new Model_DbTable_DateCommission;
                    $updateDateComm = $dbDateCommission->find($this->_getParam('idDateComm'))->current();
                    $updateDateComm->HEUREDEB_COMMISSION = $HeureD->get('HH:mm');
                    $updateDateComm->HEUREFIN_COMMISSION = $HeureF->get('HH:mm');
                    $updateDateComm->DATE_COMMISSION = $date->get(Zend_Date::YEAR."-".Zend_Date::MONTH."-".Zend_Date::DAY);
                    $updateDateComm->save();
                    
                    $dbDateCommission->updateDependingDossierDates($updateDateComm);
                    
                    $this->view->updateDateComm = $updateDateComm;
                    $this->view->first = $this->_getParam('first');
                break;
                case "annule_dateCom":
                    //ANNULATION Lorsque l'on annule la modification une date
                    $dbDateCommission = new Model_DbTable_DateCommission;
                    $tabInfos = $dbDateCommission->find($this->_getParam('idDateComm'))->current();
                    $this->view->dateCommDetail	= $tabInfos;
                    $this->view->first = $this->_getParam('first');
                break;
                case "supp_dateCom":
                    //ANNULATION Lorsque l'on annule la modification une date
                    $dbDateCommission = new Model_DbTable_DateCommission;
                    $dateCommSupp = $dbDateCommission->find($this->_getParam('idDateComm'))->current();
                    if ($dateCommSupp->DATECOMMISSION_LIEES != NULL) {
                        $dateCommSupp->delete();
                    } else {
                        $this->_helper->viewRenderer->setNoRender();
                    }
                break;
                case "addDateS":
                    $date = new Zend_Date($this->_getParam('date'),Zend_Date::DATES,'fr');
                    $dateCommLiee = $this->_getParam('idDateCommLiee');
                    //verifier si la date liéee contien une date liee. Si oui on récup cette id et on insere si non on prend l'id et on insere
                    $dbDateCommission = new Model_DbTable_DateCommission;
                    $LigneComm = $dbDateCommission->find($this->_getParam('idDateCommLiee'))->current();
                    if ($LigneComm->DATECOMMISSION_LIEES != NULL) {
                        $idUtile = $LigneComm->DATECOMMISSION_LIEES;
                    } else {
                        $idUtile = $LigneComm->ID_DATECOMMISSION;
                    }
                    $LigneComm = $dbDateCommission->find($idUtile)->current();
                    $newDate = $dbDateCommission->createRow();
                    $newDate->DATE_COMMISSION= $date->get(Zend_Date::YEAR."-".Zend_Date::MONTH."-".Zend_Date::DAY);
                        $newDate->DATECOMMISSION_LIEES = $idUtile;
                        $newDate->ID_COMMISSIONTYPEEVENEMENT = $LigneComm->ID_COMMISSIONTYPEEVENEMENT;
                        $newDate->COMMISSION_CONCERNE = $LigneComm->COMMISSION_CONCERNE;
                        $newDate->LIBELLE_DATECOMMISSION = $LigneComm->LIBELLE_DATECOMMISSION;
                        $newDate->save();
                        $this->view->TabNouvelleDate = $newDate;
                        $this->view->nouvelleDate = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR,'fr');;
                break;
                case "makeDefaut":
                    //Cas utilisé pour rendre une date de commission comme celle par défaut

                    $idDateCom = $this->_getParam('idDateComm');
                    //on récupere l'enregistrement de la date que l'on va faire devenir par défaut
                    $dbDateCommission = new Model_DbTable_DateCommission;
                    $newMasterComm = $dbDateCommission->find($idDateCom)->current();

                    //fonction fait le changement des autres dates
                    $dbDateCommission->changeMasterDateComm($newMasterComm['DATECOMMISSION_LIEES'],$newMasterComm['ID_DATECOMMISSION']);

                    $newMasterComm->DATECOMMISSION_LIEES = NULL;
                    $newMasterComm->save();
                break;
                default:
                    //cas théoriquement jamais utilisé
                    echo "Erreur mauvais choix";

                break;
            }
        }
    }

    public function adddatecommAction()
    {
        //affiche une ligne suplémentaire dans le tableau de résumé des dates
        $dateAjoutee = new Zend_Date($this->_getParam('date'),Zend_Date::DATES,'fr');
        $this->view->dateAjoutee = $dateAjoutee->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR,'fr');
        $this->view->dateAjouteeInput = $dateAjoutee->get(Zend_Date::YEAR."-".Zend_Date::MONTH."-".Zend_Date::DAY);
    }

    public function adddatedialogcommAction()
    {
        echo "date ajoutée";
    }

    public function adddatesAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender();

            $libelle = $this->_getParam('libelle_comm');
            $idComm = $this->_getParam('idComm');
            $typeComm = $this->_getParam('typeCom');

            $dbDateCommission = new Model_DbTable_DateCommission;

            $premiereDate = true;
            $listeDates = array();

            if ($this->_getParam('repeat') == 'on') {
                //Cas d'une seule date avec une périodicité selectionnée
                $periodicite = $this->_getParam('periodicite');

                $dateFin = new Zend_Date($this->_getParam('dateFin'),'dd.MM.yyyy');

                $joursAdd = $periodicite*7;

                foreach ($_POST as $var => $val) {
                    $varExplode1 = explode("_",$var);
                    if (count($varExplode1) == 2) {
                        //il n'y à que la premiere date sélectionnée (de début) qui est composée d'un "_"
                        $varExplode2 = explode("-",$varExplode1[1]);
                        if (count($varExplode2) == 3) {
                            //on s'assure que c'est bien une date jj/mm/aaaa
                            //Ici insertion la premiere dates dans la base de données
                            if ($varExplode1[0]=='D') {
                                $idOrigine = $dbDateCommission->addDateComm($varExplode1[1],$this->_getParam('D_'.$varExplode1[1]),$this->_getParam('F_'.$varExplode1[1]),$this->_getParam('idComm'),$this->_getParam('typeCom'),$libelle);
                                $dateDebut = new Zend_Date($varExplode1[1],'yyyy-MM-dd');
                                $idCalendrierTab = $idOrigine;
                                $idParent = $idOrigine;
                                $heureDebRef = $this->_getParam('D_'.$varExplode1[1]);
                                $heureFinRef = $this->_getParam('F_'.$varExplode1[1]);

                                //on prend garde de ne pas inserer le père ajouté ci dessus
                                $first = 1;
                                while ($dateDebut->compare($dateFin) <= 0) {

                                    //on liste toutes les dates jusqu'a la date de fin
                                    $dateDb = $dateDebut->get(Zend_Date::YEAR."-".Zend_Date::MONTH."-".Zend_Date::DAY);
                                    if ($first != 1) {
                                        $idOrigine = $dbDateCommission->addDateComm($dateDb,$heureDebRef,$heureFinRef,$this->_getParam('idComm'),$this->_getParam('typeCom'),$this->_getParam('libelle_comm'));
                                        $idCalendrierTab = $idOrigine;
                                    } else {
                                        $first = 0;
                                    }
                                    array_push($listeDates, array(
                                        "id" => $idCalendrierTab,
                                        "title" => $libelle,
                                        "start" => $dateDb." ".$heureDebRef,
                                        "end" => $dateDb." ".$heureFinRef,
                                        "url" => "calendrier-des-commissions/id/".$idCalendrierTab,
                                        "className" => "display-".$typeComm
                                    ));
                                    $dateDebut->addDay(7*$periodicite);
                                }
                            }
                        }
                    }
                }
            } else {
                //Cas de plusieurs dates selectionnées
                foreach ($_POST as $var => $val) {
                    $varExplode1 = explode("_",$var);
                    if (count($varExplode1) == 2) {
                        //on est dans le cas d'une date
                        $varExplode2 = explode("-",$varExplode1[1]);
                        if (count($varExplode2) == 3) {
                            //Ici insertion des dates dans la base de données
                            if ($varExplode1[0]=='D') {
                                if ($premiereDate == true) {
                                    $idOrigine = $dbDateCommission->addDateComm($varExplode1[1],$this->_getParam('D_'.$varExplode1[1]),$this->_getParam('F_'.$varExplode1[1]),$this->_getParam('idComm'),$this->_getParam('typeCom'),$this->_getParam('libelle_comm'));
                                    $idCalendrierTab = $idOrigine;
                                    $premiereDate = false;
                                } else {
                                    $idCalendrierTab = $dbDateCommission->addDateCommLiee($varExplode1[1],$this->_getParam('D_'.$varExplode1[1]),$this->_getParam('F_'.$varExplode1[1]),$idOrigine,$this->_getParam('typeCom'),$this->_getParam('idComm'),$this->_getParam('libelle_comm'));
                                }
                            } //fin $premiereDate && $varExplode1[0]=='D'

                            if ($varExplode1[0]=='D') {
                                array_push($listeDates, array(
                                    "id" => $idCalendrierTab,
                                    "title" => $libelle,
                                    "start" => $varExplode1[1]." ".$this->_getParam('D_'.$varExplode1[1]),
                                    "end" => $varExplode1[1]." ".$this->_getParam('F_'.$varExplode1[1]),
                                    "url" => "calendrier-des-commissions/id/".$idCalendrierTab,
                                    "className" => "display-".$typeComm
                                ));
                            } //fin $varExplode1[0]=='D'

                        } //fin count = 3
                    } //fin count = 2
                } //fin foreach
            }
            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'Les dates ont bien été sauvegardées',
                'message' => ''
            ));
            echo json_encode($listeDates);
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur inattendue lors de la sauvegarde des dates',
                'message' => $e->getMessage()
            ));
        }
    }

    public function deplacecommissiondateAction()
    {
            
        try {
            $date = new Zend_Date($_POST['debut'],Zend_Date::DATES,'en');

            $dbDateCommission = new Model_DbTable_DateCommission;
            $commUpdate = $dbDateCommission->find($_POST['idComm'])->current();
            $commUpdate->DATE_COMMISSION = $date->get(Zend_Date::YEAR."-".Zend_Date::MONTH."-".Zend_Date::DAY);
            if ($this->_getParam('debut')) {
                $HeureD = new Zend_Date($this->_getParam('debut'),Zend_Date::ISO_8601,'en');
                $commUpdate->HEUREDEB_COMMISSION = $HeureD->get('HH:mm');
            }
            if ($this->_getParam('fin')) {
                $HeureF = new Zend_Date($this->_getParam('fin'),Zend_Date::ISO_8601,'en');
                $commUpdate->HEUREFIN_COMMISSION = $HeureF->get('HH:mm');
            }
            $commUpdate->save();

            $dbDateCommission->updateDependingDossierDates($commUpdate);
            
            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'L\'événement a bien été déplacé',
                'message' => ''
            ));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors du déplacement de l\'événement',
                'message' => $e->getMessage()
            ));
        }
    }

    public function resizecommissiondateAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender();
            $heureFin = new Zend_Date($this->_getParam('fin'),Zend_Date::ISO_8601,'en');

            $dbDateCommission = new Model_DbTable_DateCommission;
            $commResize = $dbDateCommission->find($this->_getParam('idComm'))->current();
            $commResize->HEUREFIN_COMMISSION = $heureFin->get('HH:mm');
            $commResize->save();
            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'L\'événement a bien été modifié',
                'message' => ''
            ));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de la modification de l\'événement',
                'message' => $e->getMessage()
            ));
        }
    }

    public function gestionheuresAction()
    {
        try {
            //Permet de prendre en compte ou non les horraires de passage en commission
            $this->_helper->viewRenderer->setNoRender();

            if ($this->_getParam('gestionHeure') == "non") {
                $gestionHeure = 0;
            } elseif ($this->_getParam('gestionHeure') == "oui") {
                $gestionHeure = 1;
            }

            //On selectionne la commission concernée et en fonction des paramettres on prend en comptes les heures ou pas
            $dbDateCommission = new Model_DbTable_DateCommission;
            $dateCommConcerne = $dbDateCommission->find($this->_getParam('dateCommId'))->current();
            $dateCommConcerne->GESTION_HEURES = $gestionHeure;
            $dateCommConcerne->save();

            $dbDossierAffectation = new Model_DbTable_DossierAffectation;
            if ($gestionHeure == 0) {
                //Si on ne prend pas en compte les heures, on passe en revue chacuns des dossiers concernés par la commission
                //Récupération de l'ensemble des dossiers
                $listeDossiersConcernes = $dbDossierAffectation->getAllDossierAffect($this->_getParam('dateCommId'));
                //Pour chacun d'entre eux on passe les champs HEURE_DEB_AFFECT et HEURE_FIN_AFFECT à NULL
                //On créee un compteur afin de les classer dans l'ordre souhaité
                $nbDossier = 0;
                foreach ($listeDossiersConcernes as $lib => $val) {
                    //si l'heure de début ou de fin sont différent de NULL on les passe à NULL
                    if ($val['ID_DOSSIER_AFFECT'] != NULL || $val['ID_DOSSIER_AFFECT'] != NULL) {
                        $dossierEdit = $dbDossierAffectation->find($this->_getParam('dateCommId'),$val['ID_DOSSIER_AFFECT'])->current();
                        $dossierEdit->HEURE_DEB_AFFECT = NULL;
                        $dossierEdit->HEURE_FIN_AFFECT = NULL;
                        $dossierEdit->NUM_DOSSIER = $nbDossier;
                        $dossierEdit->save();
                    }
                    $nbDossier++;
                }
            } else {

                //Récupération de l'ensemble des dossiers
                $listeDossiersConcernes = $dbDossierAffectation->getAllDossierAffect($this->_getParam('dateCommId'));
                //Pour chacun d'entre eux on passe les champs HEURE_DEB_AFFECT et HEURE_FIN_AFFECT à NULL
                //On créee un compteur afin de les classer dans l'ordre souhaité
                foreach ($listeDossiersConcernes as $lib => $val) {
                    $dossierEdit = $dbDossierAffectation->find($this->_getParam('dateCommId'),$val['ID_DOSSIER_AFFECT'])->current();
                    $dossierEdit->HEURE_DEB_AFFECT = NULL;
                    $dossierEdit->HEURE_FIN_AFFECT = NULL;
                    $dossierEdit->NUM_DOSSIER = "0";
                    $dossierEdit->save();
                }
            }
            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'La modification de l\'événement a bien été enregistrée',
                'message' => ''
            ));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de la modification de l\'événement',
                'message' => $e->getMessage()
            ));
        }
    }

    public function changementordreAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender();
            $stringUpdate = $this->_getParam('ordreDossier');

            $dossierId = explode(",",$stringUpdate);
			
            $dbDossierAffectation = new Model_DbTable_DossierAffectation;

            $numDossier = 0;
            foreach ($dossierId as $idDossier) {
                $updateOrdreDossier = $dbDossierAffectation->find($this->_getParam('dateCommId'),$idDossier)->current();
                $updateOrdreDossier->HEURE_DEB_AFFECT = NULL;
                $updateOrdreDossier->HEURE_FIN_AFFECT = NULL;
                $updateOrdreDossier->NUM_DOSSIER = $numDossier;
                $updateOrdreDossier->save();
                $numDossier++;
            }
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur inattendue',
                'message' => $e->getMessage()
            ));
        }
    }

	public function generationconvocAction()
    {
        try {
            $dateCommId = $this->_getParam("dateCommId");
            $this->view->idComm = $dateCommId;
            //on recupere le type de commission (salle / visite / groupe de visite)
            $dbDateComm = new Model_DbTable_DateCommission;
            $commissionInfo = $dbDateComm->find($dateCommId)->current()->toArray();
            //1 = salle . 2 = visite . 3 = groupe de visite
            $this->view->typeCommission = $commissionInfo['ID_COMMISSIONTYPEEVENEMENT'];
			//On récupère la liste des dossiers
            //Suivant si l'on prend en compte les heures ou non on choisi la requete à effectuer
            $dbDateCommPj = new Model_DbTable_DateCommissionPj;
            
            if ($commissionInfo['GESTION_HEURES'] == 1) {
                    //prise en compte heures
                    $listeDossiers = $dbDateCommPj->getDossiersInfosByHour($dateCommId);
            } else {
                    //prise en compte ordre
                    $listeDossiers = $dbDateCommPj->getDossiersInfosByOrder($dateCommId);
            }

			//Récupération des membres de la commission
			$model_membres = new Model_DbTable_CommissionMembre;
			$listeMembres = $model_membres->get($commissionInfo['COMMISSION_CONCERNE']);
			foreach($listeMembres as $var => $membre){
				$listeMembres[$var]['infosFiles'] = $model_membres->fetchAll("ID_COMMISSIONMEMBRE = " . $membre['id_membre']);
			}

			$this->view->informationsMembre = $listeMembres;

			$this->view->membresFiles = $model_membres->fetchAll("ID_COMMISSION = " . $listeDossiers[0]["COMMISSION_DOSSIER"]);

            //On récupère le nom de la commission
            $model_commission = new Model_DbTable_Commission;
            $this->view->commissionInfos = $model_commission->find($commissionInfo["COMMISSION_CONCERNE"])->toArray();

            //afin de récuperer les informations des communes (adresse des mairies etc)
            $model_adresseCommune = new Model_DbTable_AdresseCommune;
            $model_utilisateurInfo = new Model_DbTable_UtilisateurInformations;

			$dbDossier = new Model_DbTable_Dossier;
			$dbDocUrba = new Model_DbTable_DossierDocUrba;
			$service_etablissement = new Service_Etablissement;
			foreach($listeDossiers as $val => $ue)
			{
                $listePrev = $dbDossier->getPreventionnistesDossier($ue['ID_DOSSIER']);
                if (count($listePrev) > 0) {
                    $listeDossiers[$val]['preventionnistes'] = $listePrev;
                }
				//On recupere la liste des établissements qui concernent le dossier
				$listeEtab = $dbDossier->getEtablissementDossierGenConvoc($ue['ID_DOSSIER']);
				//on recupere la liste des infos des établissement
				if(count($listeEtab) > 0)
				{
					$etablissementInfos = $service_etablissement->get($listeEtab[0]['ID_ETABLISSEMENT']);
					$listeDossiers[$val]['infosEtab'] = $etablissementInfos;
					$listeDocUrba = $dbDocUrba->getDossierDocUrba($ue['ID_DOSSIER']);
					$listeDossiers[$val]['listeDocUrba'] = $listeDocUrba;
				}else{
					unset($listeDossiers[$val]);
				}
			}

			$libelleCommune = "";
            $tabCommune = array();
            $numCommune = 0;
			foreach($listeDossiers as $val => $ue)
			{
				if($numCommune == 0){
					if(count($ue['infosEtab']["adresses"]) > 0){
						$libelleCommune = $ue['infosEtab']['adresses'][0]['LIBELLE_COMMUNE'];
						$adresseCommune = $model_adresseCommune->find($ue['infosEtab']['adresses'][0]['NUMINSEE_COMMUNE'])->toArray();
						$communeInfo = $model_utilisateurInfo->find($adresseCommune[0]["ID_UTILISATEURINFORMATIONS"])->toArray();
						$tabCommune[$numCommune] = array($libelleCommune,$communeInfo);
					}
					$numCommune++;
				}

				$existe = 0;
				foreach($tabCommune as $tabKey => $value){
					if(count($ue['infosEtab']["adresses"]) > 0){
						if(isset($value[0]) && $value[0] == $ue['infosEtab']['adresses'][0]['LIBELLE_COMMUNE']){
							$existe = 1;
						}
					}
				}

				if($existe == 0){
					if(count($ue['infosEtab']["adresses"]) > 0){
                                            
						$libelleCommune = $ue['infosEtab']['adresses'][0]['LIBELLE_COMMUNE'];
						$adresseCommune = $model_adresseCommune->find($ue['infosEtab']['adresses'][0]['NUMINSEE_COMMUNE'])->toArray();
						$communeInfo = $model_utilisateurInfo->find($adresseCommune[0]["ID_UTILISATEURINFORMATIONS"])->toArray();
						$tabCommune[$numCommune] = array($libelleCommune,$communeInfo);
					}
					$numCommune++;
				}

			}
            $this->view->listeCommunes = $tabCommune;
            $this->view->dossierComm = $listeDossiers;

            //récuperation du nom de la commission
            $this->view->nomComm = $listeDossiers[0]["LIBELLE_DATECOMMISSION"];
            $this->view->dateComm = $listeDossiers[0]["DATE_COMMISSION"];
            $this->view->heureDeb = $listeDossiers[0]["HEUREDEB_COMMISSION"];
            
            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'Le document a bien été généré',
                'message' => ''
            ));

        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de la génération du document',
                'message' => $e->getMessage()
            ));
        }
    }

    public function generationodjAction()
    {
		$dateCommId = $this->_getParam("dateCommId");
		$this->view->idComm = $dateCommId;

		//On récupère la liste des dossiers
		//Suivant si l'on prend en compte les heures ou non on choisi la requete à effectuer
		$dbDateComm = new Model_DbTable_DateCommission;
		$commSelect = $dbDateComm->find($dateCommId)->current();
		$dbDateCommPj = new Model_DbTable_DateCommissionPj;

		if ($commSelect['GESTION_HEURES'] == 1) {
			//prise en compte heures
			$listeDossiers = $dbDateCommPj->getDossiersInfosByHour($dateCommId);
		} elseif ($commSelect['GESTION_HEURES'] == 0) {
			//prise en compte ordre
			$listeDossiers = $dbDateCommPj->getDossiersInfosByOrder($dateCommId);
		}

		//Récupération des membres de la commission
		$model_membres = new Model_DbTable_CommissionMembre;

		$this->view->membresFiles = $model_membres->fetchAll("ID_COMMISSION = " . $listeDossiers[0]["COMMISSION_DOSSIER"]);

		//On récupère le nom de la commission
		$model_commission = new Model_DbTable_Commission;
		$this->view->commissionInfos = $model_commission->find($listeDossiers[0]["COMMISSION_DOSSIER"])->toArray();

		//afin de récuperer les informations des communes (adresse des mairies etc)
		$model_adresseCommune = new Model_DbTable_AdresseCommune;
		$model_utilisateurInfo = new Model_DbTable_UtilisateurInformations;

		$dbDossier = new Model_DbTable_Dossier;
		$dbDocUrba = new Model_DbTable_DossierDocUrba;
		$service_etablissement = new Service_Etablissement;

		foreach($listeDossiers as $val => $ue)
		{
                    $listeDossiers[$val]['preventionnistes'] = array();
                    $listeDossiers[$val]['listeDocUrba'] = array();
                    $listeDossiers[$val]['infosEtab'] = array();
                    
                    $listePrev = $dbDossier->getPreventionnistesDossier($ue['ID_DOSSIER']);
                    if (count($listePrev) > 0) {
                        $listeDossiers[$val]['preventionnistes'] = $listePrev;
                    }
                    
                    //On recupere la liste des établissements qui concernent le dossier
                    $listeEtab = $dbDossier->getEtablissementDossierGenConvoc($ue['ID_DOSSIER']);
                    //on recupere la liste des infos des établissement
                    if (count($listeEtab) > 0) {
                        $etablissementInfos = $service_etablissement->get($listeEtab[0]['ID_ETABLISSEMENT']);
                        $listeDossiers[$val]['infosEtab'] = $etablissementInfos;

                        $listeDocUrba = $dbDocUrba->getDossierDocUrba($ue['ID_DOSSIER']);
                        $listeDossiers[$val]['listeDocUrba'] = $listeDocUrba;
                    }
		}

		$libelleCommune = "";
		$tabCommune[] = array();
		$numCommune = 0;
		foreach($listeDossiers as $val => $ue)
		{
			if($numCommune == 0){
				if(isset($ue['infosEtab']['adresses'][0])){
					$libelleCommune = $ue['infosEtab']['adresses'][0]['LIBELLE_COMMUNE'];
					$adresseCommune = $model_adresseCommune->find($ue['infosEtab']['adresses'][0]['NUMINSEE_COMMUNE'])->toArray();
				}
				if(isset($adresseCommune[0]["ID_UTILISATEURINFORMATIONS"]))
					$communeInfo = $model_utilisateurInfo->find($adresseCommune[0]["ID_UTILISATEURINFORMATIONS"])->toArray();

				if(isset($libelleCommune) && isset($communeInfo))
					$tabCommune[$numCommune] = array($libelleCommune,$communeInfo);
				$numCommune++;
			}

			$existe = 0;
			foreach($tabCommune as $tabKey => $value){
				if(isset($ue['infosEtab']['adresses'][0]['LIBELLE_COMMUNE']))
				{
					if(isset($value[0]) && $value[0] == $ue['infosEtab']['adresses'][0]['LIBELLE_COMMUNE']){
						$existe = 1;
					}
				}else{
					$existe = 1;
				}
			}

			if($existe == 0){
				$libelleCommune = $ue['infosEtab']['adresses'][0]['LIBELLE_COMMUNE'];
				$adresseCommune = $model_adresseCommune->find($ue['infosEtab']['adresses'][0]['NUMINSEE_COMMUNE'])->toArray();
				$communeInfo = $model_utilisateurInfo->find($adresseCommune[0]["ID_UTILISATEURINFORMATIONS"])->toArray();
				$tabCommune[$numCommune] = array($libelleCommune,$communeInfo);
				$numCommune++;
			}

		}
		//Zend_Debug::dump($listeDossiers);
                $listeMembres = $model_membres->get($listeDossiers[0]["COMMISSION_DOSSIER"]);
                //Zend_Debug::dump($listeMembres);
                foreach($listeMembres as $var => $membre){
                        $listeMembres[$var]['infosFiles'] = $model_membres->fetchAll("ID_COMMISSIONMEMBRE = " . $membre['id_membre']);
                }

                $this->view->informationsMembre = $listeMembres;
		$this->view->listeCommunes = $tabCommune;
		$this->view->dossierComm = $listeDossiers;
                $this->view->dateComm = $listeDossiers[0]["DATE_COMMISSION"];
		$this->view->heureDeb = $listeDossiers[0]["HEUREDEB_COMMISSION"];
    }

	public function generationpvAction()
    {
		$dateCommId = $this->_getParam("dateCommId");
		$this->view->idComm = $dateCommId;
		//Suivant si l'on prend en compte les heures ou non on choisi la requete à effectuer
		$dbDateComm = new Model_DbTable_DateCommission;
		$commSelect = $dbDateComm->find($dateCommId)->current();
		
		$commissionInfo = $dbDateComm->find($dateCommId)->current()->toArray();
		//1 = salle . 2 = visite . 3 = groupe de visite
		//on recupere le type de commission (salle / visite / groupe de visite)
		$commissionInfo = $dbDateComm->find($dateCommId)->current()->toArray();
		$this->view->dateComm = $commissionInfo['DATE_COMMISSION'];
		
		//On récupère le nom de la commission
		$model_commission = new Model_DbTable_Commission;
		$this->view->commissionInfos = $model_commission->find($commissionInfo["COMMISSION_CONCERNE"])->toArray();
		$model_membres = new Model_DbTable_CommissionMembre;
		$this->view->membresFiles = $model_membres->fetchAll("ID_COMMISSION = " . $commissionInfo['COMMISSION_CONCERNE'] ." AND ID_GROUPEMENT IS NULL");
		$dbDateCommPj = new Model_DbTable_DateCommissionPj;

		//afin de récuperer les informations des communes (adresse des mairies etc)
		$model_adresseCommune = new Model_DbTable_AdresseCommune;
		$model_utilisateurInfo = new Model_DbTable_UtilisateurInformations;
	
		if($commissionInfo['GESTION_HEURES'] == 1){
			$listeDossiers = $dbDateCommPj->TESTRECUPDOSSHEURE($dateCommId);
		}else{
			$listeDossiers = $dbDateCommPj->TESTRECUPDOSS($dateCommId);
		}
		
		$dbDossier = new Model_DbTable_Dossier;
		$dbDocUrba = new Model_DbTable_DossierDocUrba;
		$service_etablissement = new Service_Etablissement;
		$dbDossierContact = new Model_DbTable_DossierContact;
		foreach($listeDossiers as $val => $ue)
		{
			//On recupere la liste des établissements qui concernent le dossier
			$listeEtab = $dbDossier->getEtablissementDossierGenConvoc($ue['ID_DOSSIER']);

			//on recupere la liste des infos des établissement
			if(isset($listeEtab[0]['ID_ETABLISSEMENT'])){
				$etablissementInfos = $service_etablissement->get($listeEtab[0]['ID_ETABLISSEMENT']);
				$listeDossiers[$val]['infosEtab'] = $etablissementInfos;
			}

			$listeDocUrba = $dbDocUrba->getDossierDocUrba($ue['ID_DOSSIER']);
			$listeDossiers[$val]['listeDocUrba'] = $listeDocUrba;

            $service_dossier = new Service_Dossier();
            $listeDossiers[$val]['prescriptionReglDossier'] = $service_dossier->getPrescriptions((int) $ue['ID_DOSSIER'],0);
            $listeDossiers[$val]['prescriptionExploitation'] = $service_dossier->getPrescriptions((int) $ue['ID_DOSSIER'],1);
            $listeDossiers[$val]['prescriptionAmelioration'] = $service_dossier->getPrescriptions((int) $ue['ID_DOSSIER'],2);
		}
		$this->view->dossierComm = $listeDossiers;
    }

	public function generationcompterenduAction()
    {
		$dateCommId = $this->_getParam("dateCommId");
		$this->view->idComm = $dateCommId;
		//Suivant si l'on prend en compte les heures ou non on choisi la requete à effectuer
		$dbDateComm = new Model_DbTable_DateCommission;
		$commSelect = $dbDateComm->find($dateCommId)->current();
		$commissionInfo = $dbDateComm->find($dateCommId)->current()->toArray();
		$this->view->dateComm = $commissionInfo['DATE_COMMISSION'];
		//1 = salle . 2 = visite . 3 = groupe de visite
		//on recupere le type de commission (salle / visite / groupe de visite)
		$commissionInfo = $dbDateComm->find($dateCommId)->current()->toArray();

		//On récupère le nom de la commission
		$model_commission = new Model_DbTable_Commission;
		$this->view->commissionInfos = $model_commission->find($commissionInfo["COMMISSION_CONCERNE"])->toArray();
		$model_membres = new Model_DbTable_CommissionMembre;
		
		$this->view->membresFiles = $model_membres->fetchAll("ID_COMMISSION = " . $commissionInfo['COMMISSION_CONCERNE']);
		$dbDateCommPj = new Model_DbTable_DateCommissionPj;

		//afin de récuperer les informations des communes (adresse des mairies etc)
		$model_adresseCommune = new Model_DbTable_AdresseCommune;
		$model_utilisateurInfo = new Model_DbTable_UtilisateurInformations;

		if ($commissionInfo['GESTION_HEURES'] == 1){
			$listeDossiers = $dbDateCommPj->TESTRECUPDOSSHEURE($dateCommId);
		} else {
			$listeDossiers = $dbDateCommPj->TESTRECUPDOSS($dateCommId);
		}
		
		$dbDossier = new Model_DbTable_Dossier;
		$dbDocUrba = new Model_DbTable_DossierDocUrba;
		$service_etablissement = new Service_Etablissement;
		$dbDossierContact = new Model_DbTable_DossierContact;
		foreach($listeDossiers as $val => $ue)
		{
			//On recupere la liste des établissements qui concernent le dossier
			$listeEtab = $dbDossier->getEtablissementDossierGenConvoc($ue['ID_DOSSIER']);
			//on recupere la liste des infos des établissement
			if(isset($listeEtab[0]['ID_ETABLISSEMENT'])){
				$etablissementInfos = $service_etablissement->get($listeEtab[0]['ID_ETABLISSEMENT']);
				$listeDossiers[$val]['infosEtab'] = $etablissementInfos;
			}
			$listeDocUrba = $dbDocUrba->getDossierDocUrba($ue['ID_DOSSIER']);
			$listeDossiers[$val]['listeDocUrba'] = $listeDocUrba;
		}
		$this->view->dossierComm = $listeDossiers;
    }

	public function alertsuppressionAction()
    {
		$this->view->commissionId = $this->_getParam("commissionId");
		$this->view->dateCommission = $this->_getParam("dateCommission");

	}

	public function validsuppressionAction()
    {
		$this->_helper->viewRenderer->setNoRender();
		$this->view->commissionId = $this->_getParam("commissionId");
		$this->view->dateCommission = $this->_getParam("dateCommission");

		$dbDossierAffect = new Model_DbTable_DossierAffectation;
                $listeDossiersAffect = $dbDossierAffect->getDossierAffect($this->_getParam('dateCommission'));
		$listeDossierNonAffect = $dbDossierAffect->getDossierNonAffect($this->_getParam('dateCommission'));
		$listeDossiers = array_merge($listeDossiersAffect, $listeDossierNonAffect);
		//On supprime les dates de commission et de visite dans les dossiers
		$dbDossier = new Model_DbTable_Dossier;
		foreach($listeDossiers as $dossier){
			$dossier = $dbDossier->find($dossier['ID_DOSSIER'])->current();
			$dossier['DATECOMM_DOSSIER'] = NULL;
			$dossier->save();
		}
		//On supprime ensuite les liens dans dossier affectation
		$dbDossierAffectation = new Model_DbTable_DossierAffectation;
		$whereDossAffect = $dbDossierAffectation->getAdapter()->quoteInto('ID_DATECOMMISSION_AFFECT = ?', $this->_getParam('dateCommission'));
		$dbDossierAffectation->delete($whereDossAffect);
		//On supprime toute les pièces jointes physiquement et dans la base de données
		$dbDateCommPj = new Model_DbTable_DateCommissionPj;
		$listePj = $dbDateCommPj->getPjInfos($this->_getParam('dateCommission'));
        $store = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('dataStore');
		foreach($listePj as $pj)
		{
            $path = $store->getFilePath($pj, 'dateCommission', $this->_getParam("dateCommission"));
            if(file_exists($path))
                unlink($path);
		}

		$whereDateCommPj = $dbDateCommPj->getAdapter()->quoteInto('ID_DATECOMMISSION = ?', $this->_getParam('dateCommission'));
		$dbDateCommPj->delete($whereDateCommPj);

		$dbDateComm = new Model_DbTable_DateCommission;
		$dateComm = $dbDateComm->find($this->_getParam('dateCommission'))->current();
		$dateComm->delete();
	}

    public function exportoutlookAction() {
        $idDateComm = $this->_getParam("dateCommId");

        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        header("Content-Type: text/Calendar");
        header("Content-Disposition: inline; filename=calendar.ics");

        $ics = "";

        if (null != $idDateComm) {

            $dbDateCommission = new Model_DbTable_DateCommission;
            $dbCommission = new Model_DbTable_Commission;

            $commissionEvent = $dbDateCommission->find($idDateComm)->toArray();

            if (null != $commissionEvent || count($commissionEvent) > 0) {
                $row = $commissionEvent[0];
                $dateStart = str_replace("-", "", $row['DATE_COMMISSION']);
                $dateStart .= "T" . str_replace(":", "", $row['HEUREDEB_COMMISSION']);
                $dateEnd = str_replace("-", "", $row['DATE_COMMISSION']);
                $dateEnd .= "T" . str_replace(":", "", $row['HEUREFIN_COMMISSION']);

                $descriptifAdd = "";
                $commissionArray = $dbCommission->find($row['COMMISSION_CONCERNE'])->toArray();
                if (null != $commissionArray) {
                    $commission = $commissionArray[0];
                    $descriptifAdd .= " / Commission : " . $commission['LIBELLE_COMMISSION'];
                }

                $ics .= "BEGIN:VCALENDAR\n";

                $ics .= $idDateComm;
                $ics .= "VERSION:2.0\n";
                $ics .= "PRODID:SDIS62/Prevarisc\n";
                $ics .= "METHOD:REQUEST\n"; // required by Outlook
                $ics .= "BEGIN:VEVENT\n";
                $ics .= "ORGANIZER:prevarisc@atos.net\n";
                $ics .= "DTSTART:" . $dateStart . "\n";
                $ics .= "DTEND:" . $dateEnd . "\n";
                $ics .= "SUMMARY:" . $row['LIBELLE_DATECOMMISSION'] . "\n";
                $ics .= "DESCRIPTION:" . $row['LIBELLE_DATECOMMISSION'] . $descriptifAdd . "\n";
                $ics .= "UID:" . date('Ymd') . "T" . date('His') . "-" . rand() . "prevarisc\n";
                $ics .= "SEQUENCE:0\n";
                $ics .= "DTSTAMP:" . date('Ymd') . "T" . date('His') ."\n";
                $ics .= "END:VEVENT\n";
                $ics .= "END:VCALENDAR\n";
            }
        }

        echo $ics;
    }

    public function exportoutlookmoisAction() {
        $idComm = $this->_getParam("CommId");
        $mois =   $this->_getParam("Mois");
        $annee =  $this->_getParam("Annee");

        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $ics = "";

        if ($idComm != null && $mois !=null && $annee !=null) {

            $dbDateCommission = new Model_DbTable_DateCommission;
            $dbCommission = new Model_DbTable_Commission;
            $dossiersaff = new Model_DbTable_DossierAffectation;

            $commissions=$dbDateCommission->getMonthCommission($mois,$annee,$idComm);
            $commissionArray=$dbCommission->getLibelleCommissions($idComm);

            $libellecommission = $commissionArray[0];

            if (!empty($commissions)) {
                header("Content-Type: text/Calendar");
                header("Content-Disposition: inline; filename=calendar_".$mois."_".$annee."_".$libellecommission['LIBELLE_COMMISSION'].".ics");

                $ics .= "BEGIN:VCALENDAR\n";
                $ics .= "VERSION:2.0\n";
                $ics .= "PRODID:SDIS62/Prevarisc\n";

                foreach($commissions as $commissiondujour)
               {

                $dateStart = str_replace("-", "", $commissiondujour['DATE_COMMISSION']);
                $dateStart .= "T" . str_replace(":", "", $commissiondujour['HEUREDEB_COMMISSION']);
                $dateEnd = str_replace("-", "", $commissiondujour['DATE_COMMISSION']);
                $dateEnd .= "T" . str_replace(":", "", $commissiondujour['HEUREFIN_COMMISSION']);
                $descriptifAdd = "";

                $descriptifAdd .= " / Commission : " . $libellecommission['LIBELLE_COMMISSION'];

                $dossieraffecte=$dossiersaff->getListDossierAffect($commissiondujour['ID_DATECOMMISSION']);

                if(!empty($dossieraffecte))
                {
                $descriptifAdd  .= " / Ordre du jour : ";
                foreach($dossieraffecte as $dossier)
                {
                 $descriptifAdd .=$dossier['OBJET_DOSSIER'].";";
                }
                }

                $ics .= "BEGIN:VEVENT\n";
                $ics .= "ORGANIZER:prevarisc@atos.net\n";
                $ics .= "DTSTART:" . $dateStart . "\n";
                $ics .= "DTEND:" . $dateEnd . "\n";
                $ics .= "SUMMARY:" . $commissiondujour['LIBELLE_DATECOMMISSION'] . "\n";
                $ics .= "DESCRIPTION:" . $commissiondujour['LIBELLE_DATECOMMISSION'] . $descriptifAdd . "\n";
                $ics .= "END:VEVENT\n";
               }
               $ics .="END:VCALENDAR\n";
            }
        }
        echo $ics;
    }
}
