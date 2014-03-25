<?php

class CalendrierDesCommissionsController extends Zend_Controller_Action
{
    public function init()
    {
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
            $array_commissions[$row_typeDeCommission->ID_COMMISSIONTYPE] = array(
                "LIBELLE" => $row_typeDeCommission->LIBELLE_COMMISSIONTYPE,
                "ARRAY" => $model_commission->fetchAll("ID_COMMISSIONTYPE = " . $row_typeDeCommission->ID_COMMISSIONTYPE )->toArray()
            );
        }
        $this->view->array_commissions = $array_commissions;
    }

    public function recupdatelieeAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $dbDateComm = new Model_DbTable_DateCommission;
        $infosDateComm = $dbDateComm->find($this->_getParam('idDate'))->current();

        //Une fois les infos de la date récupérées on peux aller chercher les date liées à cette commission pour les afficher
        if (!$infosDateComm['DATECOMMISSION_LIEES']) {
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

        //récuperation de tout les dossiers affectés à cette date de commission
        $dbDossierAffectation = new Model_DbTable_DossierAffectation;

        //Si on prend en compte les heures on récupère uniquement les dossiers n'ayant pas d'heure de passage
        $listeDossiersNonAffect = $dbDossierAffectation->getDossierNonAffect($this->_getParam('dateCommId'));


		$dbDossier = new Model_DbTable_Dossier;
		$dbDocUrba = new Model_DbTable_DossierDocUrba;
		$service_etablissement = new Service_Etablissement;
		foreach($listeDossiersNonAffect as $val => $ue)
		{
			//On recupere la liste des établissements qui concernent le dossier
			$listeEtab = $dbDossier->getEtablissementDossierGenConvoc($ue['ID_DOSSIER']);
			//$listeEtab[0]['ID_ETABLISSEMENT'];
			//on recupere la liste des infos des établissement
			if(count($listeEtab) > 0)
			{
				$etablissementInfos = $service_etablissement->get($listeEtab[0]['ID_ETABLISSEMENT']);
				$listeDossiersNonAffect[$val]['infosEtab'] = $etablissementInfos;
				$listeDocUrba = $dbDocUrba->getDossierDocUrba($ue['ID_DOSSIER']);
				$listeDossiersNonAffect[$val]['listeDocUrba'] = $listeDocUrba;
			}else{
				unset($listeDossiersNonAffect[$val]);
			}
		}

        //Gestion de l'affichage de la date de la commission
        $date =  new Zend_Date($infosDateComm['DATE_COMMISSION'],'yyyy-MM-dd');
        $this->view->dateFr = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR,'fr');

        $this->view->infosDateComm = $infosDateComm;
        $this->view->infosCommission = $infosCommission;
/*
        $dbDocUrba = new Model_DbTable_DossierDocUrba;
        $cpt = 0;
        foreach ($listeDossiersNonAffect as $dossierNonAffect) {
            $docsUrba = $dbDocUrba->find($dossierNonAffect['ID_DOSSIER'])->toArray();

            if (count($docsUrba) >= 1) {
                $listeDossiersNonAffect[$cpt]["NUM_DOCURBA"] = $docsUrba[0]['NUM_DOCURBA'];
            }

            $cpt++;
        }
*/
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
                "title" => $commissionEvent['LIBELLE_DATECOMMISSION'],
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
			//$listeEtab[0]['ID_ETABLISSEMENT'];
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
			//Zend_Debug::dump($etablissement);
		}
		//Zend_Debug::dump($listeDossiersAffect);

        foreach ($listeDossiersAffect as $dossierAffect) {
			$affichage = $dossierAffect['infosEtab']['parents'][0]["LIBELLE_ETABLISSEMENTINFORMATIONS"]." - ".$dossierAffect['infosEtab']['informations']['LIBELLE_ETABLISSEMENTINFORMATIONS' ];
			if(isset($dossierAffect['infosEtab']['adresses'][0]['LIBELLE_COMMUNE']) && $dossierAffect['infosEtab']['adresses'][0]['LIBELLE_COMMUNE'] != "")
				$affichage .= " (".$dossierAffect['infosEtab']['adresses'][0]['LIBELLE_COMMUNE'].")";
			
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

            $items[] = array(
                "id" => $dossierAffect['ID_DOSSIER'],
                "url" => "/dossier/index/id/".$dossierAffect['ID_DOSSIER'],
                "title" => $affichage,
                "start" => date($dateDebut." ".$dossierAffect['HEURE_DEB_AFFECT']),
                "end" => date($dateDebut." ".$dossierAffect['HEURE_FIN_AFFECT']),
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
                //On est sur la date maitre
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
                if ($dateD->get(Zend_Date::WEEKDAY_DIGIT) != 6 && $dateD->get(Zend_Date::WEEKDAY_DIGIT) != 0) {
                    array_push($listeDates, array(
                        "date" => $dateD->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR,'fr'),
                        "inputH" => $dateD->get(Zend_Date::YEAR."-".Zend_Date::MONTH."-".Zend_Date::DAY),
                        "heureD" => $HeureD->get('HH:mm'),
                        "heureF" => $HeureF->get('HH:mm')
                    ));
                }
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
                /**
                    ***
                    *** LIBELLE
                    ***
                    **/
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
                        $dbDateCommission->dateCommUpdateLibelle($this->_getParam('idDateComm'),$this->_getParam('data'));
                    } else {
                        //Cas d'une comm liée
                        $dbDateCommission->dateCommUpdateLibelle($commEdit->DATECOMMISSION_LIEES,$this->_getParam('data'));
                    }
                    $this->view->libelleDateComm = $this->_getParam('data');
                break;
                case "annule_libelleCom":
                    //ANNULATION Lorsque l'on annule la modification du libelle (récupération de l'ancien libelle)
                        $dbDateCommission = new Model_DbTable_DateCommission;
                    $commQtipInfo = $dbDateCommission->find($this->_getParam('idDateComm'))->current();
                    $this->view->libelleDateComm = $commQtipInfo->LIBELLE_DATECOMMISSION;
                break;
                /**
                    ***
                    *** TYPE
                    ***
                    **/
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
                /**
                    ***
                    *** DATE
                    ***
                    **/
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
                                    //echo $dateDebut->compare($dateFin)." + valeur qui ira en BD : ".$dateDebut->get(Zend_Date::DAY."-".Zend_Date::MONTH."-".Zend_Date::YEAR)."<br/>";
                                    $dateDb = $dateDebut->get(Zend_Date::YEAR."-".Zend_Date::MONTH."-".Zend_Date::DAY);
                                    if ($first != 1) {
                                        //$idOrigine = $dbDateCommission->addDateCommLiee($dateDb,$heureDebRef,$heureFinRef,$idParent,$this->_getParam('typeCom'),$this->_getParam('idComm'),$libelle);
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
            //echo $this->_getParam('gestionHeure')." idcom = ".$this->_getParam('dateCommId');

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
            //echo "changement ordre comm => ".$this->_getParam('dateCommId');
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

}
