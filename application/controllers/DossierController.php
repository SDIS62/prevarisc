<?php

class DossierController extends Zend_Controller_Action
{
    private $id_dossier;

    //liste des champs à afficher en fonction de la nature
    private $listeChamps = array(
    //ETUDES
        //PC - OK
        "1" => array("type","DATEINSERT","OBJET","NUMDOCURBA","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","SERVICEINSTRUC","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","COORDSSI","DATESDIS","PREVENTIONNISTE","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION"),
        //AT - OK
        "2" => array("type","DATEINSERT","OBJET","NUMDOCURBA","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","SERVICEINSTRUC","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","COORDSSI","DATESDIS","PREVENTIONNISTE","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION"),
        //Dérogation - OK
        "3" => array("type","DATEINSERT","OBJET","NUMDOCURBA","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","SERVICEINSTRUC","COMMISSION","DESCGEN","JUSTIFDEROG","MESURESCOMPENS","MESURESCOMPLE","DESCEFF","DATECOMM","AVIS","COORDSSI","DATESDIS","PREVENTIONNISTE","DEMANDEUR","REGLEDEROG","INCOMPLET","HORSDELAI","AVIS_COMMISSION"),
        //Cahier des charges fonctionnel du SSI - OK
        "4" => array("type","DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DATECOMM","AVIS","COORDSSI","DATESDIS","PREVENTIONNISTE","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION"),
        //Cahier des charges de type T - OK
        "5" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION"),
        //Salon type T - OK
        "6" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","CHARGESEC","PREVENTIONNISTE","DEMANDEUR","INCOMPLET", "HORSDELAI","AVIS_COMMISSION"),
        //RVRMD (diag sécu) - OK
        "7" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION"),
        //Documents divers - OK
        "8" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","DATEPREF","DATEREP","PREVENTIONNISTE","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION"),
        //Changement de DUS - OK
        "9" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION"),
        //Suivi organisme formation SSIAP - OK
        "10" => array("DATEINSERT","OBJET","NUMCHRONO","AVIS","DATESDIS","DATEPREF","DATEREP","PREVENTIONNISTE","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION"),
        //Demande de registre de sécurité CTS - OK
        "11" => array("DATEINSERT","OBJET","NUMCHRONO","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","DATEPREF","PREVENTIONNISTE","DEMANDEUR","INCOMPLET", "HORSDELAI","AVIS_COMMISSION"),
        //Demande d'implantation CTS < 6mois - OK
        "12" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR","INCOMPLET", "HORSDELAI","AVIS_COMMISSION"),
        //Demande d'implantation CTS > 6mois - OK
        "13" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR","INCOMPLET", "HORSDELAI","AVIS_COMMISSION"),
        //Permis d'aménager - OK
        "14" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","DESCGEN","DESCEFF","DATESDIS","PREVENTIONNISTE","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION"),
        //Permis de démolir - OK
        "15" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION"),
        //CR de visite des organismes d'ins.... - OK
        "16" => array("DATEINSERT","OBJET","NUMCHRONO","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","DATEPREF","PREVENTIONNISTE","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION"),
        //Etude suite a un avis ne se prononce pas - OK MAIS VOIR POUR PARTICULARITé TABLEAU
        "17" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION"),
        //Utilisation exceptionnelle de locaux - OK
        "18" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR","INCOMPLET", "HORSDELAI","AVIS_COMMISSION"),
        //Levée de réserves - OK
        "19" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR","AVIS_COMMISSION"),
        //Echéncier de travaux - OK
        "46" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION"),
    //VISITE DE COMMISSION
        //Réception de travaux - OK
        "20" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATEVISITE","AVIS","COORDSSI","PREVENTIONNISTE","NPSP","NPEA","APPALV","AVIS_COMMISSION"),
		//Avant ouverture - OK
        "47" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATEVISITE","AVIS","COORDSSI","PREVENTIONNISTE","NPSP","NPEA","APPALV","AVIS_COMMISSION"),
        //Périodique - OK
        "21" => array("DATEINSERT","COMMISSION","DESCGEN","DESCEFF","DATEVISITE","AVIS","PREVENTIONNISTE","DIFFEREAVIS","NPSP","NPEA","APPALV","AVIS_COMMISSION"),
        //Chantier - OK
        "22" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATEVISITE","COORDSSI","PREVENTIONNISTE","AVIS_COMMISSION"),
        //Controle - OK
        "23" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATEVISITE","AVIS","COORDSSI","PREVENTIONNISTE","DIFFEREAVIS","NPSP","NPEA","APPALV","AVIS_COMMISSION"),
        //Inopinéee - OK
        "24" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATEVISITE","AVIS","PREVENTIONNISTE","DIFFEREAVIS","NPSP","NPEA","APPALV","AVIS_COMMISSION"),
    //GROUPE DE VISITE
        //Réception de travaux - OK
        "25" => array("type","DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATECOMM","DATEVISITE","AVIS","COORDSSI","PREVENTIONNISTE","NPSP","NPEA","APPALV","AVIS_COMMISSION"),
		//Avant ouverture - OK
        "48" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATECOMM","DATEVISITE","AVIS","COORDSSI","PREVENTIONNISTE","NPSP","NPEA","APPALV","AVIS_COMMISSION"),
        //Périodique - OK
        "26" => array("DATEINSERT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","DATEVISITE","AVIS","PREVENTIONNISTE","DIFFEREAVIS","NPSP","NPEA","APPALV","AVIS_COMMISSION"),
        //Chantier - OK
        "27" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATECOMM","DATEVISITE","COORDSSI","PREVENTIONNISTE","AVIS_COMMISSION"),
        //Controle - OK
        "28" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATECOMM","DATEVISITE","AVIS","COORDSSI","PREVENTIONNISTE","DIFFEREAVIS","NPSP","NPEA","APPALV","AVIS_COMMISSION"),
        //Inopinéee - OK
        "29" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATECOMM","DATEVISITE","AVIS","PREVENTIONNISTE","DIFFEREAVIS","NPSP","NPEA","APPALV","AVIS_COMMISSION"),
    //REUNION
        //Visite ou sur site - OK"DATEVISITE",
        "30" => array("DATEINSERT","OBJET","PREVENTIONNISTE","DEMANDEUR"),
        //Locaux SDIS - OK
        "31" => array("DATEINSERT","OBJET","DATEREUN","PREVENTIONNISTE","DEMANDEUR"),
        //Exterieur SDIS - OK
        "32" => array("DATEINSERT","OBJET","DATEREUN","PREVENTIONNISTE","DEMANDEUR"),
        //Téléphonique - OK
        "33" => array("DATEINSERT","OBJET","DATEREUN","PREVENTIONNISTE","DEMANDEUR"),
    //COURRIER/COURRIEL
        //Arrivée - OK
        "34" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","DATEREP","PREVENTIONNISTE","DATESDIS","DEMANDEUR"),
        //Départ - OK
        "35" => array("DATEINSERT","OBJET","NUMCHRONO","DATEREP","PREVENTIONNISTE","DEMANDEUR"),
        //En transit (gestion des dossiers en interne....  - OK
        "36" => array("DATEINSERT","OBJET","DATEMAIRIE","DATESECRETARIAT","PREVENTIONNISTE","DEMANDEUR","DATEENVTRANSIT"),
    //INTERVENTION
        //Incendie - OK
        "37" => array("DATEINSERT","OBJET","OPERSDIS","RCCI","REX","NUMINTERV","DATEINTERV","DUREEINTERV","PREVENTIONNISTE"),
        //SAP - OK
        "38" => array("DATEINSERT","OBJET","OPERSDIS","RCCI","REX","NUMINTERV","DATEINTERV","DUREEINTERV","PREVENTIONNISTE"),
        //Intervention div - OK
        "39" => array("DATEINSERT","OBJET","OPERSDIS","RCCI","REX","NUMINTERV","DATEINTERV","DUREEINTERV","PREVENTIONNISTE"),
    //ARRETE
        //Ouverture - OK
        "40" => array("DATEINSERT","DATESIGN","PREVENTIONNISTE"),
        //Fermeture - OK
        "41" => array("DATEINSERT","OBJET","DATESIGN","PREVENTIONNISTE"),
        //Mise en demeure - OK
        "42" => array("DATEINSERT","OBJET","DATESIGN","PREVENTIONNISTE"),
        //Mise en demeure de l'exploitant - OK
        "43" => array("DATEINSERT","OBJET","DATESIGN","PREVENTIONNISTE"),
        //GN6 - OK
        "44" => array("DATEINSERT","OBJET","DATESIGN","PREVENTIONNISTE"),
        //Courrier - OK
        "45" => array("DATEINSERT","OBJET","DATESIGN","PREVENTIONNISTE"),
    );

    public function init()
    {
        $this->_helper->layout->setLayout('dossier');

        // Actions à effectuées en AJAX
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('selectiontexte', 'json')
                        ->addActionContext('selectionarticle', 'json')
                        ->addActionContext('selectionabreviation', 'json')
                        ->addActionContext('selectionetab', 'json')
                        ->initContext();

        if( !isset($this->view->action) )
			$this->view->action = $this->_request->getActionName();

        $this->view->idDossier = ($this->_getParam("id"));

        $id_dossier = null;
        $id_dossier = $this->_getParam("id");
        if ($id_dossier == null) { $id_dossier = $this->_getParam("idDossier"); }

        if ($id_dossier != null) {

            //Si on à l'id d'un dossier, on récupére tous les établissements liés à ce dossier
            $DBdossier = new Model_DbTable_Dossier;
            $dossier = $DBdossier->find($id_dossier)->current();

            $DBdossierType = new Model_DbTable_DossierType;
            $libelleType = $DBdossierType->find($dossier->TYPE_DOSSIER)->current();

            $this->view->objetDossier = $dossier->OBJET_DOSSIER;
			$this->view->idTypeDossier = $dossier->TYPE_DOSSIER;
            $this->view->libelleType = $libelleType['LIBELLE_DOSSIERTYPE'];
			
			$natureDossier = $DBdossier->getDossierTypeNature($id_dossier);
			$this->view->natureDossier = $natureDossier[0]['ID_NATURE'];
			
            $this->view->idDossier = ($this->_getParam("id"));
        }
		
		
    }

    public function indexAction()
    {
        $this->view->do = "new";
        if ($this->_getParam("id")) {
            $this->view->do = "edit";
            $this->view->idDossier = ($this->_getParam("id"));
			
        }

        $this->view->idEtablissement = $this->_getParam("id_etablissement");
        if ( isset($this->view->idEtablissement)) {
            $DBetablissement = new Model_DbTable_Etablissement;
            $this->view->etablissementLibelle = $DBetablissement->getLibelle($this->_getParam("id_etablissement"));
        }
        //echo $this->view->idEtablissement;

		
		
        $this->_forward('general');
    }

    public function pieceJointeAction()
    {
		
		
        $this->_forward("index", "piece-jointe", null, array(
            "type" => "dossier",
            "id" => $this->_request->id,
        ));
		
		
    }

    public function addAction()
    {
        $this->view->action = "add";
        $this->_forward('index');
    }

    public function generalAction()
    {
		//Zend_Debug::dump(Zend_Auth::getInstance()->getIdentity());
		$this->view->idUser = Zend_Auth::getInstance()->getIdentity()->ID_UTILISATEUR;
        //On récupère tous les types de dossier
        $DBdossierType = new Model_DbTable_DossierType;
        $this->view->dossierType = $DBdossierType ->fetchAll();
        //Récupération de la liste des avis pour la génération du select
        $DBlisteAvis = new Model_DbTable_Avis;
        $this->view->listeAvis = $DBlisteAvis->getAvis();
        //Zend_Debug::dump($this->view->listeAvis);
        if ($this->_getParam("idEtablissement"))
		{
            $this->view->idEtablissement = $this->_getParam("idEtablissement");
        }else{
			
		}
		
		
		/***************
			RECUPERATIONS INFOS ETABLISSEMENT (cellule ou etab pour generation des avis)
		******************/
		if ($this->_getParam("id_etablissement")) {
			//echo "ICI ON EST DANS UN ETAB (creation dossier)";
			$DBetab = new Model_DbTable_Etablissement;
			$etabTab = $DBetab->getInformations($this->_getParam("id_etablissement"));
			$etablissement = $etabTab->toArray();
			//Zend_Debug::dump($etablissement);
			$this->view->genre = $etablissement['ID_GENRE'];
			$commissionEtab = $etablissement['ID_COMMISSION'];
			$idEtablissement = $this->_getParam("id_etablissement");
		} elseif ((int) $this->_getParam("id")) {
			//echo "ICI ON EST DANS UN DOSS EXISTANT (consultation/edit dossier)";
			$DBdossier = new Model_DbTable_Dossier;
			$tabEtablissement = $DBdossier->getEtablissementDossier((int) $this->_getParam("id"));
			$this->view->listeEtablissement = $tabEtablissement;
			$DBetab = new Model_DbTable_Etablissement;
			$etablissement = $DBetab->getInformations($tabEtablissement[0]['ID_ETABLISSEMENT'])->toArray();
			//Zend_Debug::dump($etablissement);
			$this->view->genre = $etablissement['ID_GENRE'];
			$commissionEtab = $etablissement['ID_COMMISSION'];
			$idEtablissement = $tabEtablissement[0]['ID_ETABLISSEMENT'];
		}
		$this->view->commissionEtab = $commissionEtab;
		$genreInfo = $this->view->genre;
		$this->view->idEtablissement = $idEtablissement;
		
        $today = new Zend_Date();
        $this->view->dateToday = $today->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
		
        $DBdossierCommission = new Model_DbTable_Commission;
        //$this->view->commissionsInfos = $DBdossierCommission->getAllCommissions();
	
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
		
        if ((int) $this->_getParam("id"))
		{
            //Cas d'affichage des infos d'un dossier existant
            $this->view->do = 'edit';
            //On récupère l'id du dossier
            $idDossier = (int) $this->_getParam("id");
            $this->view->idDossier = $idDossier;
            //Récupération de tous les champs de la table dossier
            $DBdossier = new Model_DbTable_Dossier;
            $this->view->infosDossier = $DBdossier->find($idDossier)->current();
            //Zend_Debug::dump($this->view->infosDossier->toArray());
			
			//récuperation des informations sur le créateur du dossier
			$DB_user = new Model_DbTable_Utilisateur;
			$DB_informations = new Model_DbTable_UtilisateurInformations;
			if($this->view->infosDossier['CREATEUR_DOSSIER']){
				$user = $DB_user->find( $this->view->infosDossier['CREATEUR_DOSSIER'] )->current();
				$this->view->user_info = $DB_informations->find( $user->ID_UTILISATEURINFORMATIONS )->current();
			}else{
				$this->view->user_info = "";
			}
			//Zend_Debug::dump($this->view->user_info->toArray());

            //Conversion de la date d'insertion du dossier
            if ($this->view->infosDossier['DATEINSERT_DOSSIER'] != '') {
                $date = new Zend_Date($this->view->infosDossier['DATEINSERT_DOSSIER'], Zend_Date::DATES);
                $this->view->infosDossier['DATEINSERT_DOSSIER'] = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                $this->view->DATEINSERT_INPUT = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
            }

            //Conversion de la date de dépot en mairie pour l'afficher
            if ($this->view->infosDossier['DATEMAIRIE_DOSSIER'] != '') {
                $date = new Zend_Date($this->view->infosDossier['DATEMAIRIE_DOSSIER'], Zend_Date::DATES);
                $this->view->infosDossier['DATEMAIRIE_DOSSIER'] = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                $this->view->DATEMAIRIE_INPUT = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
            }
            //Conversion de la date de dépot en secrétariat pour l'afficher
            if ($this->view->infosDossier['DATESECRETARIAT_DOSSIER'] != '') {
                $date = new Zend_Date($this->view->infosDossier['DATESECRETARIAT_DOSSIER'], Zend_Date::DATES);
                $this->view->infosDossier['DATESECRETARIAT_DOSSIER'] = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                $this->view->DATESECRETARIAT_INPUT = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
            }
			
			//Conversion de la date de dépot en secrétariat pour l'afficher
            if ($this->view->infosDossier['DATEENVTRANSIT_DOSSIER'] != '') {
                $date = new Zend_Date($this->view->infosDossier['DATEENVTRANSIT_DOSSIER'], Zend_Date::DATES);
                $this->view->infosDossier['DATEENVTRANSIT_DOSSIER'] = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                $this->view->DATEENVTRANSIT_INPUT = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
            }

                //Conversion de la date de réception SDIS
                if ($this->view->infosDossier['DATESDIS_DOSSIER'] != '') {
                    $date = new Zend_Date($this->view->infosDossier['DATESDIS_DOSSIER'], Zend_Date::DATES);
                    $this->view->infosDossier['DATESDIS_DOSSIER'] = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                    $this->view->DATESDIS_INPUT = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
                }
                //Conversion de la date prefecture
                if ($this->view->infosDossier['DATEPREF_DOSSIER'] != '') {
                    $date = new Zend_Date($this->view->infosDossier['DATEPREF_DOSSIER'], Zend_Date::DATES);
                    $this->view->infosDossier['DATEPREF_DOSSIER'] = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                    $this->view->DATEPREF_INPUT = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
                }

                //Conversion de la date de visite
                if ($this->view->infosDossier['DATEVISITE_DOSSIER'] != '') {
                    $date = new Zend_Date($this->view->infosDossier['DATEVISITE_DOSSIER'], Zend_Date::DATES);
                    $this->view->infosDossier['DATEVISITE_DOSSIER'] = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                    $this->view->DATEVISITE_INPUT = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
                }
                //Conversion de la date commission
                if ($this->view->infosDossier['DATECOMM_DOSSIER'] != '') {
                    $date = new Zend_Date($this->view->infosDossier['DATECOMM_DOSSIER'], Zend_Date::DATES);
                    $this->view->infosDossier['DATECOMM_DOSSIER'] = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                    $this->view->DATECOMM_INPUT = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
                }

                //Conversion de la date de réponse
                if ($this->view->infosDossier['DATEREP_DOSSIER'] != '') {
                    $date = new Zend_Date($this->view->infosDossier['DATEREP_DOSSIER'], Zend_Date::DATES);
                    $this->view->infosDossier['DATEREP_DOSSIER'] = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                    $this->view->DATEREP_INPUT = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
                }
                //Conversion de la date de réunion
                if ($this->view->infosDossier['DATEREUN_DOSSIER'] != '') {
                    $date = new Zend_Date($this->view->infosDossier['DATEREUN_DOSSIER'], Zend_Date::DATES);
                    $this->view->infosDossier['DATEREUN_DOSSIER'] = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                    $this->view->DATEREUN_INPUT = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
                }
                //Conversion de la date et l'heure d'intervention
                if ($this->view->infosDossier['DATEINTERV_DOSSIER'] != '') {
                    //echo $this->view->infosDossier['DATEINTERV_DOSSIER'];
                    $dateHeure = explode(" ",$this->view->infosDossier['DATEINTERV_DOSSIER']);
                    $date = new Zend_Date($dateHeure[0], Zend_Date::DATES);
                    $this->view->infosDossier['DATEINTERV_DOSSIER'] = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                    $this->view->DATEINTERV_INPUT = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
                    $heure = explode(":",$dateHeure[1]);
                    $this->view->HEUREINTERV_INPUT = $heure[0].":".$heure[1];
                }
                //Conversion de la date signature
                if ($this->view->infosDossier['DATESIGN_DOSSIER'] != '') {
                    $date = new Zend_Date($this->view->infosDossier['DATESIGN_DOSSIER'], Zend_Date::DATES);
                    $this->view->infosDossier['DATESIGN_DOSSIER'] = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                    $this->view->DATESIGN_INPUT = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
                }
				
				//Conversion date incomplet
				if ($this->view->infosDossier['DATEINCOMPLET_DOSSIER'] != '') {
					$date = new Zend_Date($this->view->infosDossier['DATEINCOMPLET_DOSSIER'], Zend_Date::DATES);
					$this->view->infosDossier['DATEINCOMPLET_DOSSIER'] = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
					$this->view->DATEINCOMPLET = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
				}
				
                //Conversion de la durée de l'intervention
                if ($this->view->infosDossier['DUREEINTERV_DOSSIER'] != '') {
                    //echo $this->view->infosDossier['DATEINTERV_DOSSIER'];
                    $heure = explode(":",$this->view->infosDossier['DUREEINTERV_DOSSIER']);
                    $this->view->infosDossier['DUREEINTERV_DOSSIER'] = $heure[0].":".$heure[1];
                }

                if ($this->view->infosDossier["DESCGEN_DOSSIER"] != '') {
                    $this->view->infosDossier["DESCGEN_DOSSIER"] = nl2br($this->view->infosDossier["DESCGEN_DOSSIER"]);
                    $this->view->DESCGEN_INPUT = str_replace("<br />", "" ,$this->view->infosDossier["DESCGEN_DOSSIER"]);
                }
                if ($this->view->infosDossier["DESCRIPTIF_DOSSIER"] != '') {
                    $this->view->infosDossier["DESCRIPTIF_DOSSIER"] = nl2br($this->view->infosDossier["DESCRIPTIF_DOSSIER"]);
                    $this->view->DESCRIPTIF_INPUT = str_replace("<br />", "" ,$this->view->infosDossier["DESCRIPTIF_DOSSIER"]);
                }
				//echo $this->view->infosDossier["AVIS_DOSSIER"];
                if ($this->view->infosDossier["AVIS_DOSSIER"] != '') {
					//Zend_Debug::dump($DBlisteAvis->getAvisLibelle($this->view->infosDossier["AVIS_DOSSIER"]));
                    $this->view->AVIS_VALUE = $DBlisteAvis->getAvisLibelle($this->view->infosDossier["AVIS_DOSSIER"]);
                }
                
				if ($this->view->infosDossier["AVIS_DOSSIER_COMMISSION"] != '') {
					//Zend_Debug::dump($DBlisteAvis->getAvisLibelle($this->view->infosDossier["AVIS_DOSSIER_COMMISSION"]));
                    $this->view->AVIS_COMMISSION_VALUE = $DBlisteAvis->getAvisLibelle($this->view->infosDossier["AVIS_DOSSIER_COMMISSION"]);
                }
				
            //Récupération du libellé du type de dossier
            $libelleType = $DBdossierType->find($this->view->infosDossier['TYPE_DOSSIER'])->current();
            $this->view->libelleType = $libelleType['LIBELLE_DOSSIERTYPE'];
			
            //Récupération tous les libellé des natures du dossier concerné
            $DBdossierNature = new Model_DbTable_DossierNature;
            $this->view->natureConcerne = $DBdossierNature->getDossierNaturesLibelle($idDossier);
            //Zend_Debug::dump($this->view->natureConcerne);

            //Récupération de la liste des natures pour la génération du select
            $DBdossierNatureListe = new Model_DbTable_DossierNatureliste;
            $this->view->dossierNatureListe = $DBdossierNatureListe->getDossierNature($this->view->infosDossier['TYPE_DOSSIER']);

            //Récupération de la liste des documents d'urbanismes
            $DBdossierDocUrba = new Model_DbTable_DossierDocUrba;
            $this->view->dossierDocUrba = $DBdossierDocUrba->getDossierDocUrba($idDossier);

			//On récupére l'ensemble des commissions pour l'affichage du select
			
            //ICI RéCUPERATION DU LIBELLE DE LA COMMISSION !!!!!!!!!!! PUIS AFFICHAGE DANS LE INPUT !!!
            $this->view->commissionInfos = $DBdossierCommission->find($this->view->infosDossier['COMMISSION_DOSSIER'])->current();
					
            //On récupère la liste de tous les champs que l'on doit afficher en fonction des natures
            //Si il y à plusieurs natures on les fait une par une pour savoir tous les champs à afficher
            $premiereNature = 1;
            $afficherChamps = array();
            foreach ($this->view->natureConcerne as $value) {
                if ($premiereNature == 1) {
                    $afficherChamps = $this->listeChamps[$value['ID_NATURE']];
                    $premiereNature = 0;
                } else {
                    $tabTemp = $this->listeChamps[$value['ID_NATURE']];
                    foreach ($tabTemp as $value) {
                        //si la nature contient un champ n'étant pas dans le tableau principal on l'ajoute
                        if (!in_array($value, $afficherChamps)) {
                            array_push($afficherChamps, $value);
                        }
                    }
                }
            }
            $this->view->afficherChamps = $afficherChamps;
            //Zend_Debug::dump($this->view->afficherChamps);

        /*
        GESTION DES DATES DE COMMISSIONS ET DE VISITE / GROUPE DE VISITE
        */
            //On récupere les infos concernant l'affectation à une commission si il y en a eu une
            $dbAffectDossier = new Model_DbTable_DossierAffectation;
            $affectDossier = $dbAffectDossier->find(NULL,$this->_getParam("id"))->current();
            $this->view->affectDossier = $affectDossier;

            $listeDateAffectDossier = $dbAffectDossier->recupDateDossierAffect($this->_getParam("id"));

            //Zend_Debug::dump($listeDateAffectDossier);
            $dbDateComm = new Model_DbTable_DateCommission;
            $dateComm = $dbDateComm->find($affectDossier['ID_DATECOMMISSION_AFFECT'])->current();
			
			//Zend_Debug::dump($dateComm);
			
            //En fonction du type de dossier on traite les dates d'affectation existantes differement
            if ($this->view->infosDossier['TYPE_DOSSIER'] == 1) {
                // CAS D'UNE éTUDE
                //Concernant cette affectation on récupere les infos sur la commission (date aux différents format)
                if ($dateComm['DATE_COMMISSION'] != '') {
                    $date = new Zend_Date($dateComm['DATE_COMMISSION'], Zend_Date::DATES);
                    $this->view->dateCommValue = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                    $this->view->dateCommInput = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
                    $this->view->idDateCommissionAffect = $dateComm['ID_DATECOMMISSION'];
                }
            } elseif ($this->view->infosDossier['TYPE_DOSSIER'] == 2 || $this->view->infosDossier['TYPE_DOSSIER'] == 3) {

                // CAS D'UNE VISITE
                //echo "dossier de type 2".count($listeDateAffectDossier);
                
				foreach($listeDateAffectDossier as $val => $ue){
					if($ue['ID_COMMISSIONTYPEEVENEMENT'] == 1){
						//COMMISSION EN SALLE
						 $date = new Zend_Date($ue['DATE_COMMISSION'], Zend_Date::DATES);
						$this->view->dateCommValue = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
						$this->view->dateCommInput = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
						$this->view->idDateCommissionAffect = $ue['ID_DATECOMMISSION'];
					}else{
						//VISITE OU GROUPE DE VISITE
						$nbDateExist = count($listeDateAffectDossier);		
/*						
						if($nbDateExist == 1){
							//On verifie qu'il ne s'agit pas d'une principale avant de continuer
							$infosDateComm = $dbDateComm->find($affectDossier['ID_DATECOMMISSION_AFFECT'])->current();
							if (!$infosDateComm['DATECOMMISSION_LIEES']) {
								echo "NULL";
							} else {
								echo "PAS NULL";
							}
						}
*/						
						if ($nbDateExist == 1) {
							//Si 1 seule date alors la date de viste et de commission est la même
							$date = new Zend_Date($dateComm['DATE_COMMISSION'], Zend_Date::DATES);
							$this->view->dateVisiteValue = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
							$this->view->dateVisiteInput = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
							$this->view->dateCommValue = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
							$this->view->dateCommInput = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
							$this->view->idDateVisiteAffect = $dateComm['ID_DATECOMMISSION'];
							//$this->view->idDateCommissionAffect = $dateComm['ID_DATECOMMISSION_AFFECT'];
						} elseif ($nbDateExist == 2) {
							//Si 2 dates alors on affiche toutes les dates -1 dans visite et la dernière dans date de passage en comm
							$infosDateComm = $dbDateComm->find($affectDossier['ID_DATECOMMISSION_AFFECT'])->current();
							//Une fois les infos de la date récupérées on peux aller chercher les date liées à cette commission pour les afficher
							if (!$infosDateComm['DATECOMMISSION_LIEES']) {
								$commPrincipale = $affectDossier['ID_DATECOMMISSION_AFFECT'];
							} else {
								$commPrincipale = $infosDateComm['DATECOMMISSION_LIEES'];
							}
							//récupération de l'ensemble des dates liées
							$recupCommLiees = $dbDateComm->getCommissionsDateLieesMaster($commPrincipale);
							$nbDatesTotal = count($recupCommLiees);
							$nbDateDecompte = $nbDatesTotal;
							$listeDateValue = "";
							$listeDateInput = "";
							//echo "nb dates total = ".$nbDatesTotal."<br/>";
							foreach ($recupCommLiees as  $val => $ue) {
								$date = new Zend_Date($ue['DATE_COMMISSION'], Zend_Date::DATES);
								if ($nbDateDecompte == $nbDatesTotal) {
									//premiere date = date visite donc on renseigne l'input hidden correspondant avec l'id de cette date
									$this->view->idDateVisiteAffect = $ue['ID_DATECOMMISSION'];
								}
								if ($nbDateDecompte > 1) {
									$listeDateValue .= $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR).", ";
									$listeDateInput .= $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR).", ";
								} elseif ($nbDateDecompte == 1) {
									$listeDateValue .= $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
									$listeDateInput .= $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
								}

								$this->view->dateVisiteValue = $listeDateValue;
								$this->view->dateVisiteInput = $listeDateInput;

								//echo $nbDateDecompte." --- ".$ue['DATE_COMMISSION']."<br/>";

								$nbDateDecompte--;
							}

							//echo "<br/>";
							//Zend_Debug::dump($recupCommLiees);
						}
						
						
						

					}
				}
			}
				
				
				
				
				/*
                if ($nbDateExist == 1) {
                    //Si 1 seule date alors la date de viste et de commission est la même
                    $date = new Zend_Date($dateComm['DATE_COMMISSION'], Zend_Date::DATES);
                    $this->view->dateVisiteValue = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                    $this->view->dateVisiteInput = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
                    $this->view->dateCommValue = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                    $this->view->dateCommInput = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
                    $this->view->idDateVisiteAffect = $dateComm['ID_DATECOMMISSION'];
                    //$this->view->idDateCommissionAffect = $dateComm['ID_DATECOMMISSION_AFFECT'];
                } elseif ($nbDateExist == 2) {
                    //Si 2 dates alors on affiche toutes les dates -1 dans visite et la dernière dans date de passage en comm
                    $infosDateComm = $dbDateComm->find($affectDossier['ID_DATECOMMISSION_AFFECT'])->current();
                    //Une fois les infos de la date récupérées on peux aller chercher les date liées à cette commission pour les afficher
                    if (!$infosDateComm['DATECOMMISSION_LIEES']) {
                        $commPrincipale = $affectDossier['ID_DATECOMMISSION_AFFECT'];
                    } else {
                        $commPrincipale = $infosDateComm['DATECOMMISSION_LIEES'];
                    }
                    //récupération de l'ensemble des dates liées
                    $recupCommLiees = $dbDateComm->getCommissionsDateLieesMaster($commPrincipale);
                    $nbDatesTotal = count($recupCommLiees);
                    $nbDateDecompte = $nbDatesTotal;
                    $listeDateValue = "";
                    $listeDateInput = "";
                    //echo "nb dates total = ".$nbDatesTotal."<br/>";
                    foreach ($recupCommLiees as  $val => $ue) {
                        $date = new Zend_Date($ue['DATE_COMMISSION'], Zend_Date::DATES);
                        if ($nbDateDecompte == $nbDatesTotal) {
                            //premiere date = date visite donc on renseigne l'input hidden correspondant avec l'id de cette date
                            $this->view->idDateVisiteAffect = $ue['ID_DATECOMMISSION'];
                        }
                        if ($nbDateDecompte > 2) {
                            $listeDateValue .= $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR).", ";
                            $listeDateInput .= $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR).", ";
                        } elseif ($nbDateDecompte == 2) {
                            $listeDateValue .= $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                            $listeDateInput .= $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
                        } elseif ($nbDateDecompte == 1) {
                            //derniere date donc date de commission renseigner l'input hidden avec l'id et le date com value et input
                            $this->view->dateCommValue = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                            $this->view->dateCommInput = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
                            $this->view->idDateCommissionAffect = $ue['ID_DATECOMMISSION'];
                        }

                        $this->view->dateVisiteValue = $listeDateValue;
                        $this->view->dateVisiteInput = $listeDateInput;

                        //echo $nbDateDecompte." --- ".$ue['DATE_COMMISSION']."<br/>";

                        $nbDateDecompte--;
                    }

                    //echo "<br/>";
                    //Zend_Debug::dump($recupCommLiees);
                }
				
            } elseif ($this->view->infosDossier['TYPE_DOSSIER'] == 3) {
                //CAS D'UN GROUPE DE VISITE
                //$listeDateAffectDossier doit contenir deux dates. Une pour la visite et une pour le passage en commission
                //Zend_Debug::dump($listeDateAffectDossier);

                foreach ($listeDateAffectDossier as  $val => $ue) {
                    if ($ue['ID_COMMISSIONTYPEEVENEMENT'] == 1) {
                        //il sagit de la date de commission en salle
                        $date = new Zend_Date($dateComm['DATE_COMMISSION'], Zend_Date::DATES);
                        $this->view->dateCommValue = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                        $this->view->dateCommInput = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
                        $this->view->idDateCommissionAffect = $ue['ID_DATECOMMISSION'];
                    } elseif ($ue['ID_COMMISSIONTYPEEVENEMENT'] == 3) {
                        //il sagit (de la / des) date(s) de visite
                        $infosDateComm = $dbDateComm->find($ue['ID_DATECOMMISSION'])->current();
                        //Zend_Debug::dump($infosDateComm);
                        //echo "!!!!!!!!!! - ".$infosDateComm['DATECOMMISSION_LIEES'];
                        //Une fois les infos de la date récupérées on peux aller chercher les date liées à cette commission pour les afficher
                        if (!$infosDateComm['DATECOMMISSION_LIEES']) {
                            $commPrincipale = $ue['ID_DATECOMMISSION'];
                        } else {
                            $commPrincipale = $infosDateComm['DATECOMMISSION_LIEES'];
                        }
                        //Zend_Debug::dump($commPrincipale);
                        $recupCommLiees = $dbDateComm->getCommissionsDateLieesMaster($commPrincipale);
                        //Zend_Debug::dump($recupCommLiees);
                        $nbDatesTotal = count($recupCommLiees);
                        $nbDateDecompte = $nbDatesTotal;
                        $listeDateValue = "";
                        $listeDateInput = "";
                        //echo "nb dates total = ".$nbDatesTotal."<br/>";
                        foreach ($recupCommLiees as  $val => $ue) {
                            $date = new Zend_Date($ue['DATE_COMMISSION'], Zend_Date::DATES);
                            if ($nbDateDecompte == $nbDatesTotal) {
                                //premiere date = date visite donc on renseigne l'input hidden correspondant avec l'id de cette date
                                $this->view->idDateVisiteAffect = $ue['ID_DATECOMMISSION'];
                            }
                            if ($nbDateDecompte > 1) {
                                $listeDateValue .= $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR).", ";
                                $listeDateInput .= $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR).", ";
                            } elseif ($nbDateDecompte == 1) {
                                $listeDateValue .= $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                                $listeDateInput .= $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
                            }

                            $this->view->dateVisiteValue = $listeDateValue;
                            $this->view->dateVisiteInput = $listeDateInput;

                            //echo $nbDateDecompte." --- ".$ue['DATE_COMMISSION']."<br/>";

                            $nbDateDecompte--;
                        }
                    }
                }
            }
			*/
			
			//Recuperation des documents manquants dans le cas d'un dossier incomplet
			
			$dbDossDocManquant = new Model_DbTable_DossierDocManquant;
			$this->view->listeDocManquant = $dbDossDocManquant->getDocManquantDoss($this->_getParam("id"));
			//Zend_Debug::dump($this->view->listeDocManquant);

            $DBdossierPrev = new Model_DbTable_DossierPreventionniste;
            $this->view->preventionnistes = $DBdossierPrev->getPrevDossier($this->_getParam("id"));

        } else {
            $this->view->do = 'new';
            //echo $this->_getParam("id_etablissement");
            $search = new Model_DbTable_Search;
            $preventionnistes = ( $this->_getParam("id_etablissement") ) ? $search->setItem("utilisateur")->setCriteria("etablissementinformations.ID_ETABLISSEMENT", $this->_getParam("id_etablissement"))->run()->getAdapter()->getItems(0, 99999999999)->toArray() : null;
            $preventionnistes[-1] = array_fill_keys ( array( "LIBELLE_GRADE", "NOM_UTILISATEURINFORMATIONS", "PRENOM_UTILISATEURINFORMATIONS" ) , null );
            unset($preventionnistes[-1]);
            $this->view->preventionnistes = $preventionnistes;
            //Zend_Debug::dump($this->view->preventionnistes);
			$this->view->listeDocManquant = Array();
        }

        //23/10/12 Ajout du service instructeur remplacé par le select des groupements de communes
        // Liste des types de groupement


        if ($this->view->infosDossier['SERVICEINSTRUC_DOSSIER']) {
            $groupements = new Model_DbTable_Groupement();
            $servInstructeur = $this->view->infosDossier['SERVICEINSTRUC_DOSSIER'];
            $groupement = $groupements->find($servInstructeur)->current();
            $this->view->serviceInstructeurLibelle = $groupement["LIBELLE_GROUPEMENT"];
            $this->view->serviceInstructeurId = $groupement["ID_GROUPEMENT"];
        }


        //$this->view->groupement = $groupement->toArray();

    }

    public function fonctionAction()
    {
        $this->view->do = $this->_getParam("do");
        switch ($this->view->do) {
            case "infosEtabs":
                if ($this->_getParam("idEtablissement")) {
                    $DBetab = new Model_DbTable_Etablissement;
                    $etabTab = $DBetab->getInformations($this->_getParam("idEtablissement"));
                    //echo "pour l'établissement ".$etabTab->LIBELLE_ETABLISSEMENTINFORMATIONS;
					$this->view->etablissement = $etabTab->toArray();
					//Zend_Debug::dump($this->view->etablissement);
					$DbAdresse = new Model_DbTable_EtablissementAdresse;
					$this->view->adresseEtab = $DbAdresse->get($this->_getParam("idEtablissement"));
					//Zend_Debug::dump($adresse);
                } elseif ($this->_getParam("idDossier")) {
                    $DBdossier = new Model_DbTable_Dossier;
                    $tabEtablissement = $DBdossier->getEtablissementDossier((int) $this->_getParam("idDossier"));
					$this->view->listeEtablissement = $tabEtablissement;
					//echo count($this->view->listeEtablissement);
					//Zend_Debug::dump($this->view->listeEtablissement);
                }
            break;
            case "showNature":
                $idType = (int) $this->_getParam("idType");

                //Récupération de la liste des natures
                $DBdossiernatureliste = new Model_DbTable_DossierNatureliste;
                $this->view->dossierNatureListe = $DBdossiernatureliste->getDossierNature($idType);
            break;
            case "showChamps":
                $this->_helper->viewRenderer->setNoRender();
                $listeNature = $this->_getParam("listeNature");
				
                //Si une liste de nature est envoyée on peux traiter les différents champs à afficher
                if ($listeNature != '') {
                    $tabListeIdNature = explode("_",$listeNature);
                    $premiereNature = 1;
                    $afficherChamps = array();
                    //$afficherChamps = $this->listeChamps[$idNature];

                    foreach ($tabListeIdNature as $idNature) {
                        if ($premiereNature == 1) {
                            $afficherChamps = $this->listeChamps[$idNature];
                            $premiereNature = 0;
                        } else {
                            $tabTemp = $this->listeChamps[$idNature];
                            foreach ($tabTemp as $value) {
                                //si la nature contient un champ n'étant pas dans le tableau principal on l'ajoute
                                if (!in_array($value, $afficherChamps)) {
                                    array_push($afficherChamps, $value);
                                }
                            }
                        }
                    }

                    echo json_encode($afficherChamps);
                }
            break;
            case "editDossier":
                $idDossier = $this->_getParam("idDossier");
                $DBdossier = new Model_DbTable_Dossier;
                $this->view->infosDossier = $DBdossier->find($idDossier)->current();

                //Récupération tous les libellé des natures du dossier concerné
                $DBdossierNature = new Model_DbTable_DossierNature;
                $this->view->natureConcerne = $DBdossierNature->getDossierNaturesLibelle($idDossier);

                //On récupère la liste de tous les champs que l'on doit afficher en fonction des natures
                //Si il y à plusieurs natures on les fait une par une pour savoir tous les champs à afficher
                $premiereNature = 1;
                $afficherChamps = array();
                foreach ($this->view->natureConcerne as $value) {
                    if ($premiereNature == 1) {
                        $afficherChamps = $this->listeChamps[$value['ID_DOSSIERNATURE']];
                        $premiereNature = 0;
                    } else {
                        $tabTemp = $this->listeChamps[$value['ID_DOSSIERNATURE']];
                        foreach ($tabTemp as $value) {
                            //si la nature contient un champ n'étant pas dans le tableau principal on l'ajoute
                            if (!in_array($value, $afficherChamps)) {
                                array_push($afficherChamps, $value);
                            }
                        }
                    }
                }
                echo json_encode($afficherChamps);
            break;
            case "addNatureDossier":
                //ajoute en BD une nature au dossier
                $DBdossierNature = new Model_DbTable_DossierNature;
                $newNature = $DBdossierNature->createRow();
                $newNature->ID_NATURE = $this->_getParam('idNatureAdd');
                $newNature->ID_DOSSIER = $this->_getParam('idDossier');
                $newNature->save();
                echo $newNature->ID_DOSSIERNATURE;
            break;
            case "deleteNatureDossier":
                //supprime de la BD une nature (mode édition)
                $DBdossierNature = new Model_DbTable_DossierNature;
                $idNatureSupp = $DBdossierNature->find($this->_getParam('idNatureSupp'))->current();
                $idNatureSupp->delete();
            break;
            case "addDocUrba":
                //ajoute dans la base de données un document d'urbanisme au dossier (mode édition seulement)
                if ( $this->_getParam('numDoc') && $this->_getParam('idDossier') ) {
                    $DBdocUrba = new Model_DbTable_DossierDocUrba;
                    $newDocUrba = $DBdocUrba->createRow();
                    $newDocUrba->NUM_DOCURBA = $this->_getParam('numDoc');
                    $newDocUrba->ID_DOSSIER = $this->_getParam('idDossier');
                    $newDocUrba->save();
                }
            break;
            case "deleteDocUrba":
                //supprime un document d'urbanisme dans la base de données (en mode édition seulement)
                $DBdocUrba = new Model_DbTable_DossierDocUrba;
                $numDocSupp = $DBdocUrba->find($this->_getParam('idNumDoc'))->current();
                $numDocSupp->delete();
            break;
            case 'ajoutDocValid':
                $this->ajoutdocAction($this->id_dossier);
            break;
            case 'suppDoc':
                //cas de la suppression d'un document qui avait été renseigné
                $tabInfos = split("_",$this->_getParam('docInfos'));
                //echo $tabInfos[0]." et ".$tabInfos[1];
                $nature = $tabInfos[0];
                $numdoc = $tabInfos[1];
                if (count($tabInfos) == 2) {
                    //cas d'un document existant
                    $dbToUse = new Model_DbTable_DossierDocConsulte;
                    $searchResult = $dbToUse->getGeneral($this->_getParam('idDossier'), $nature, $numdoc);
                    $docDelete = $dbToUse->find($searchResult['ID_DOSSIERDOCCONSULTE'])->current();
                    $docDelete->delete();
                } elseif (count($tabInfos) == 3) {
                    //cas d'un document ajouté
                    $dbToUse = new Model_DbTable_ListeDocAjout;
                    $searchResult = $dbToUse->find($numdoc)->current();
                    $searchResult->delete();
                }
            break;
            case 'showMadContent':
                echo $this->_getParam('numPresc');
                $dbPrescDossier = new Model_DbTable_PrescriptionDossier;
            break;
            case 'showListeDossierEtab':
                //echo $this->_getParam('idListeEtab');
                //On place dans un tableau chacun des idEtablissement liés au dossier
                //$listeEtab = split("-", $this->_getParam('idListeEtab'));
                $listeEtab = explode("-", $this->_getParam('idListeEtab'));

                //Pour chacun des établissement on va récuperer les dossiers concernés
                $dbDossier = new Model_DbTable_Dossier;
                $listeDossierEtab = array();
                foreach ($listeEtab as $lib => $val) {
                    //echo $val."<br/>";
                    $listeDossierEtab[$val] = $dbDossier->getDossierEtab($val,$this->_getParam('idDossier'));
                }
                //Zend_Debug::dump($listeDossierEtab);
                $this->view->idDossier = $this->_getParam('idDossier');
                $this->view->listeEtab = $listeEtab;
                $this->view->listeDossierEtab = $listeDossierEtab;

            break;
            case 'showDossiersLies':
                //On commence par récuperer les dossiers liés à celui dans lequel on est
                $dbDossierLie = new Model_DbTable_DossierLie;
                $this->listeDossierLies = $dbDossierLie->getDossierLie($this->_getParam('idDossier'));
                //Zend_Debug::dump($this->listeDossierLies);

                $dbDossier = new Model_DbTable_Dossier;

                foreach ($this->listeDossierLies as $numrez => $attr) {
                    //on parcour chacun dossiers liers pour en récupérer les informations à afficher
                    if ($this->_getParam('idDossier') == $attr['ID_DOSSIER1']) {
                        $dossierToShow = $attr['ID_DOSSIER2'];
                    } elseif ($this->_getParam('idDossier') == $attr['ID_DOSSIER2']) {
                        $dossierToShow = $attr['ID_DOSSIER1'];
                    }

                    $infosEtabDossier = $dbDossier->getEtablissementDossier($dossierToShow);
                    //Zend_Debug::dump($infosEtabDossier);

                    $infosDossier = $dbDossier->getDossierTypeNature($dossierToShow);
                    //Zend_Debug::dump($infosDossier);

                    $ligneDossierString = "
                        <div class='grid_14 alpha'>
                            <a href='/dossier/index/id/".$infosDossier[0]['ID_DOSSIER']."'>[
                    ";
                    $nbEtabConcerne = count($infosEtabDossier);

                    foreach ($infosEtabDossier as $gne => $val) {
                        $ligneDossierString .= $val["LIBELLE_ETABLISSEMENTINFORMATIONS"];
                        if ($nbEtabConcerne > 1) {
                            $ligneDossierString .= " / ";
                        }
                        $nbEtabConcerne--;
                    }

                    $ligneDossierString .= "
                            ] ".$infosDossier[0]['OBJET_DOSSIER']." (".$infosDossier[0]['LIBELLE_DOSSIERTYPE']." / ".$infosDossier[0]['LIBELLE_DOSSIERNATURE'].") </a>
                    ";
                    $ligneDossierString .= "
                            <input type='hidden' value='".$attr['ID_DOSSIERLIE']."' name='idDossierLie'>
                            <span>
                                <button class='deleteLienDossier' >Supprimer le lien avec ce dossier</button>
                            </span>
                            <span style='display:none;' >
                                <button class='confirmDeleteLienDossier' >Supprimer le lien avec ce dossier</button>
                                <button class='cancelDeleteLienDossier' >Annuler</button>
                            </span>
                        </div>

                    ";
                    echo $ligneDossierString;
                }
                echo "<br/><br/><br/><br/>";

            break;
            case 'liaisonDossier':
                if (count($this->_getParam('idDossierLie')) > 0) {
                    $dbDossier = new Model_DbTable_DossierLie;
                    foreach ($this->_getParam('idDossierLie') as $lib => $id) {
                        $newLien = $dbDossier->createRow();
                        $newLien->ID_DOSSIER1 = $this->_getParam('idDossier');
                        $newLien->ID_DOSSIER2 = $id;
                        $newLien->save();
                    }
                }
            break;
			case "pjPassageCommission":
				//Permet de distinguer les prescriptions qui motivent un avis défavorable sur le dossier
				$dbDossierPj = new Model_DbTable_DossierPj;
				//$dossierPjEdit = $dbDossierPj->getdossierpj($this->_getParam('idDossier'),$this->_getParam('idPjCommission'));
				$dossierPjEdit = $dbDossierPj->find($this->_getParam('idDossier'),$this->_getParam('idPjCommission'))->current();

				if($this->_getParam('checked') == 'true'){
					$dossierPjEdit->PJ_COMMISSION = 1;
				}else if($this->_getParam('checked') == 'false'){
					$dossierPjEdit->PJ_COMMISSION = 0;
				}
				//echo get_class($dossierPjEdit);
				//Zend_debug::dump($dossierPjEdit);
				$dossierPjEdit->save();
			break;
			case "texteApplicable":
				$this->_helper->viewRenderer->setNoRender();
				//echo $this->_getParam('toDo');
				$dbDossierTexteApplicable = new Model_DbTable_DossierTextesAppl;
				
				//Si on est dans une visite on change automatiquement les textes applicables de l'établissement
				//On cherche le type de dossier
				$dbDossier = new Model_DbTable_Dossier;
				$type = $dbDossier->getTypeDossier($this->_getParam('idDossier'));
				
				//Lorsque l'on est dans une visite ou un groupe de visite, on modifie les textes applicables dans l'établissement au fur et à mesure
				if($type['TYPE_DOSSIER'] == 2 || $type['TYPE_DOSSIER'] == 3)
				{
					$dbEtablissementTextAppl = new Model_DbTable_EtsTextesAppl;
					if($this->_getParam('toDo') == 'save')
					{
						$row = $dbEtablissementTextAppl->createRow();
						$row->ID_TEXTESAPPL = $this->_getParam('idTexte');
						$row->ID_ETABLISSEMENT = $this->_getParam('idEtablissement');
						$row->save();
					}else if ($this->_getParam('toDo') == 'delete'){
						$row = $dbEtablissementTextAppl->find($this->_getParam('idTexte'),$this->_getParam('idEtablissement'))->current();
						$row->delete();
					}
				}
				
				
				
				if($this->_getParam('toDo') == 'save'){
					//$row = $dbDossierTexteApplicable->
					$row = $dbDossierTexteApplicable->createRow();
					$row->ID_TEXTESAPPL = $this->_getParam('idTexte');
					$row->ID_DOSSIER = $this->_getParam('idDossier');
					$row->save();
					
				}else if ($this->_getParam('toDo') == 'delete'){
					$row = $dbDossierTexteApplicable->find($this->_getParam('idTexte'),$this->_getParam('idDossier'))->current();
					$row->delete();
				}
			break;
			case "showDocManquant":
				//Si on passe un id dossier en param alors on cherche le dernier champ doc manquant si il existe
				if($this->_getParam('idDossier') && $this->_getParam('idDossier') != '')
				{
					$dbDossDocManquant = new Model_DbTable_DossierDocManquant;
					$lastDocManquantInDb = $dbDossDocManquant->getDocManquantDossLast($this->_getParam('idDossier'));
					//Zend_Debug::dump($lastDocManquantInDb);
					$this->view->oldDocManquant = $lastDocManquantInDb;
					//echo $this->view->oldDocManquant;
				}else{
					//On recupere la liste des documents manquant type
					$dbDocManquant = new Model_DbTable_DocManquant;
					$this->view->listeDoc = $dbDocManquant->getDocManquant();

					//Zend_Debug::dump($listeDocManquant);
					$this->view->numDocManquant = $this->_getParam('numDoc');
				}
			break;
        }
    }

    //Permet de faire les insertions de dossier en base de données et de rediriger vers le dossier/index/id/X => X = id du dossier qui vient d'être crée
    public function saveAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $DBdossier = new Model_DbTable_Dossier;

        if ($this->_getParam('do') == 'new') {
            $nouveauDossier = $DBdossier->createRow();
			$nouveauDossier->CREATEUR_DOSSIER = $this->_getParam('ID_CREATEUR');
        } elseif ($this->_getParam('do') == 'edit') {
            $nouveauDossier = $DBdossier->find($this->_getParam('idDossier'))->current();
			$typeDossier = $nouveauDossier['TYPE_DOSSIER'];
			if($typeDossier != $this->_getParam("TYPE_DOSSIER")){
				//si le type a changé on supprime les documents consultés et les documents d'urbanisme
				$dbDocAjout = new Model_DbTable_ListeDocAjout;
     			$where = $dbDocAjout->getAdapter()->quoteInto('ID_DOSSIER = ?', $this->_getParam('idDossier'));
				$dbDocAjout->delete($where);
				
				$dbDocConsulte = new Model_DbTable_DossierDocConsulte;
     			$where = $dbDocConsulte->getAdapter()->quoteInto('ID_DOSSIER = ?', $this->_getParam('idDossier'));
				$dbDocConsulte->delete($where);				
			}
        }
		
        foreach ($_POST as $libelle => $value) {
            //On exclu la lecture de selectNature => select avec les natures;
            //NUM_DOCURB => input text pour la saisie des doc urba; docUrba & natureId => interpreté après;
            //  !  \\
            //
            if ($libelle != "DATEVISITE_PERIODIQUE" && $libelle != "selectNature" && $libelle != "NUM_DOCURBA" && $libelle != "natureId" && $libelle != "docUrba" && $libelle != 'do' && $libelle != 'idDossier' && $libelle != 'HEUREINTERV_DOSSIER' && $libelle != 'idEtablissement' && $libelle != 'ID_AFFECTATION_DOSSIER_VISITE' && $libelle != 'ID_AFFECTATION_DOSSIER_COMMISSION' && $libelle != "preventionniste" && $libelle != "commissionSelect" && $libelle != "ID_CREATEUR" && $libelle != "HORSDELAI_DOSSIER" && $libelle != "genreInfo" && $libelle != "docManquant" && $libelle != "dateReceptionDocManquant") {
                //Test pour voir s'il sagit d'une date pour la convertir au format ENG et l'inserer dans la base de données
                if ($libelle == "DATEMAIRIE_DOSSIER" || $libelle == "DATESECRETARIAT_DOSSIER" || $libelle == "DATEVISITE_DOSSIER" || $libelle == "DATECOMM_DOSSIER" || $libelle == "DATESDIS_DOSSIER" || $libelle ==  "DATEPREF_DOSSIER" || $libelle ==  "DATEREP_DOSSIER" || $libelle ==  "DATEREUN_DOSSIER" || $libelle == "DATEINTERV_DOSSIER" || $libelle == "DATESIGN_DOSSIER" || $libelle == "DATEINSERT_DOSSIER" || $libelle == "DATEENVTRANSIT_DOSSIER") {
                    if ($value) {
                        $dateTab = explode("/",$value);
                        $value = $dateTab[2]."-".$dateTab[1]."-".$dateTab[0];
                        if ($libelle == "DATEINTERV_DOSSIER") {
                            $value .= " ".$this->_getParam('HEUREINTERV_DOSSIER');
                        }
                    } else {
                        $value = NULL;
                    }
                }
				
                if($libelle == "INCOMPLET_DOSSIER" && $value == 1){
                        //dossier incomplet on enregistre la date du jour dans le champs DATEINCOMPLET
                        $dateIncomplet = Zend_Date::now();
                        $nouveauDossier->DATEINCOMPLET_DOSSIER = $dateIncomplet->get(Zend_Date::YEAR."-".Zend_Date::MONTH_SHORT."-".Zend_Date::DAY_SHORT);
                }

                if ($libelle == 'AVIS_DOSSIER' && $value == 0) {
                    $value = NULL;
                }
                //echo $this->_getParam('HORSDELAI_DOSSIER');
                
                if($value == '')
                    $value = NULL;
                //echo $libelle." - ".$value."<br/>";

                $nouveauDossier->$libelle = $value;
            }

        }
		
		if(!$this->_getParam('HORSDELAI_DOSSIER'))
		{
			 $nouveauDossier->HORSDELAI_DOSSIER = 0;
		}else{
			$nouveauDossier->HORSDELAI_DOSSIER = 1;
		}
		
		if (!$this->_getParam('NPSP_DOSSIER')) {
			$nouveauDossier->NPSP_DOSSIER = 0;                
		}else{
			$nouveauDossier->NPSP_DOSSIER = 1;
		}
		
		if (!$this->_getParam('NPEA_DOSSIER')) {
			$nouveauDossier->NPEA_DOSSIER = 0;                
		}else{
			$nouveauDossier->NPEA_DOSSIER = 1;
		}
		
		if (!$this->_getParam('DIFFEREAVIS_DOSSIER')) {
			$nouveauDossier->DIFFEREAVIS_DOSSIER = 0;                
		}else{
			$nouveauDossier->DIFFEREAVIS_DOSSIER = 1;
		}
		
		if (!$this->_getParam('APPALV_DOSSIER')) {
			$nouveauDossier->APPALV_DOSSIER = 0;                
		}else{
			$nouveauDossier->APPALV_DOSSIER = 1;
		}
		
		if (!$this->_getParam('CNE_DOSSIER')) {
			$nouveauDossier->CNE_DOSSIER = 0;                
		}else{
			$nouveauDossier->CNE_DOSSIER = 1;
		}

        $nouveauDossier->save();
	
		//echo $this->_getParam("selectNature")." - ".$this->_getParam("TYPE_DOSSIER")."<br/>";
		if ($this->_getParam("selectNature") == 21 && $this->_getParam("TYPE_DOSSIER") == 2) {
			//VISITE PERIODIQUE
			//Dans le cas d'une visite périodique on renseigne le champ DATEVISITE_DOSSIER pour pouvoir calculer la périodicité suviante
			if ($this->_getParam('DATEVISITE_PERIODIQUE')) {
				$datePeriodique = explode("/",$_POST['DATEVISITE_PERIODIQUE']);
				$dateToSql = $datePeriodique[2]."-".$datePeriodique[1]."-".$datePeriodique[0];
				$nouveauDossier->DATEVISITE_DOSSIER = $dateToSql;
				$nouveauDossier->save();
			}
		}

        $idDossier = $nouveauDossier->ID_DOSSIER;
		
		
		 $DBetablissementDossier = new Model_DbTable_EtablissementDossier;
        if ($this->_getParam('do') == 'new')
		{
            if ( isset( $_POST['idEtablissement'] ) &&  $_POST['idEtablissement'] != "" ) {
                $saveEtabDossier = $DBetablissementDossier->createRow();
                $saveEtabDossier->ID_ETABLISSEMENT = $this->_getParam('idEtablissement');
                $saveEtabDossier->ID_DOSSIER = $idDossier;
                $saveEtabDossier->save();
            }
            //Sauvegarde des natures du dossier
			
            $DBdossierNature = new Model_DbTable_DossierNature;

            $saveNature = $DBdossierNature->createRow();
            $saveNature->ID_DOSSIER = $idDossier;
            $saveNature->ID_NATURE = $_POST['selectNature'];
            $saveNature->save();


        } else {
            //gestion des natures en mode édition
            $DBdossierNature = new Model_DbTable_DossierNature;
            //$nature = $DBdossierNature->find($idDossier)->current();
            $natureCheck = $DBdossierNature->getDossierNaturesId($idDossier);
            //Zend_Debug::dump($natureCheck);
            $nature = $DBdossierNature->find($natureCheck['ID_DOSSIERNATURE'])->current();

            $nature->ID_NATURE = $this->_getParam("selectNature");
			
            $nature->save();
        }
		
		//On met le champ ID_DOSSIER_DONNANT_AVIS de établissement avec l'ID du dossier que l'on vient d'enregistrer dans les cas suivant 
		if($this->_getParam("AVIS_DOSSIER_COMMISSION") && ($this->_getParam("AVIS_DOSSIER_COMMISSION") == 1 || $this->_getParam("AVIS_DOSSIER_COMMISSION") == 2))
		{
			$MAJEtab = 0;
			if ($this->_getParam("TYPE_DOSSIER") == 1 && $this->_getParam("selectNature") == 19) 
			{
				//Cas d'une étude uniquement dans le cas d'une levée de reserve
				$MAJEtab = 1;
			}
			else if($this->_getParam("TYPE_DOSSIER") == 2 && ($this->_getParam("selectNature") == 21 || $this->_getParam("selectNature") == 24 || $this->_getParam("selectNature") == 47))
			{
				//Cas d'une viste uniquement dans le cas d'une VP, inopinée ou avant ouverture
				$MAJEtab = 1;
			}
			else if($this->_getParam("TYPE_DOSSIER") == 3 && ($this->_getParam("selectNature") == 26 || $this->_getParam("selectNature") == 29 || $this->_getParam("selectNature") == 48))
			{
				//Cas d'un groupe deviste uniquement dans le cas d'une VP, inopinée ou avant ouverture
				$MAJEtab = 1;
			}
			//echo "VAL = ".$MAJEtab."<br/>";
			
			$dbEtab = new Model_DbTable_Etablissement;
            
			if ($MAJEtab == 1 && $this->_getParam('do') == 'new')
			{
				$etabToEdit = $dbEtab->find($this->_getParam('idEtablissement'))->current();
				$etabToEdit->ID_DOSSIER_DONNANT_AVIS = $idDossier;
				$etabToEdit->save();
				
			}
            else if($MAJEtab == 1)
            {
				$listeEtab = $DBetablissementDossier->getEtablissementListe($idDossier);

				foreach($listeEtab as $val => $ue)
				{
                    $etabToEdit = $dbEtab->find($ue['ID_ETABLISSEMENT'])->current();
                    $etabToEdit->ID_DOSSIER_DONNANT_AVIS = $idDossier;
                    $etabToEdit->save();
				}
			}
		}
	
		//GESTION DE LA RECUPERATION DES TEXTES APPLICABLES DANS CERTAINS CAS
		//lorsque je crée un dossier visite ou groupe de visite VP (21-26), VC (22-27), VI (24-29), 
		//il faut que les textes applicables à l’ERP se retrouvent de fait dans le dossier créé
		$idNature = $this->_getParam("selectNature");
		//echo $idNature;
		if( ($idNature == 21 ||  $idNature == 22 || $idNature == 24 || $idNature == 26 || $idNature == 27 || $idNature == 29) &&  $_POST['idEtablissement'] != "")
		{
			$dbEtablissementTextAppl = new Model_DbTable_EtsTextesAppl;
			$listeTexteApplEtab = $dbEtablissementTextAppl->recupTextes($_POST['idEtablissement']);
			//Zend_Debug::dump($listeTexteApplEtab);
			$dbDossierTexteAppl = new Model_DbTable_DossierTextesAppl;
			foreach($listeTexteApplEtab as $val => $ue)
			{
				$saveTexteAppl = $dbDossierTexteAppl->createRow();
				$saveTexteAppl->ID_DOSSIER = $idDossier;
				$saveTexteAppl->ID_TEXTESAPPL = $ue['ID_TEXTESAPPL'];
				$saveTexteAppl->save();
			}
		}
		
		//GESTION DE LA RECUPERATION DES DOCUMENTS CONSULTES DE LA PRECEDENTE VP SI IL EN EXISTE UNE (UNIQUEMENT EN CREATION DE DOSSIER)
		if( ( $idNature == 21 || $idNature == 26 ) &&  $_POST['idEtablissement'] != "" )
		{
			//echo "ici on check les precedentes vp";
			$lastVP = $DBdossier->findLastVp($this->_getParam("idEtablissement"));
			//Zend_Debug::dump($lastVP);
			$idDossierLastVP = $lastVP['ID_DOSSIER'];
			//echo $lastVP['ID_DOSSIER'];
			if($lastVP['ID_DOSSIER'] != '')
			{
				//echo $lastVP['ID_DOSSIER']." IL FAUT RECUP DES DOCUMENTS CONSULTES";
				
		        $dblistedoc = new Model_DbTable_DossierListeDoc;
				$dblistedocAjout = new Model_DbTable_ListeDocAjout;

				//ici on récupère tous les documents qui ont été renseigné dans la base par un utilisateur (avec id du dossier et de la nature)
				$listeDocRenseigne = $dblistedoc->recupDocDossier($idDossierLastVP,$idNature);

				//ici on récupère tous les documents qui ont été ajoutés par l'utilisateur (document non proposé par défaut)
				$listeDocAjout = $dblistedocAjout->getDocAjout($idDossierLastVP,$idNature);
				//Zend_Debug::dump($listeDocAjout);
				
				//on copie les docrenseigne pour la nouvelle visite
				$dbDocConsulte = new Model_DbTable_DossierDocConsulte;
				foreach($listeDocRenseigne as $val => $ue)
				{
					$cpDocConsulte = $dbDocConsulte->createRow();
					$cpDocConsulte->ID_NATURE = $idNature;
					$cpDocConsulte->REF_CONSULTE = $ue['REF_CONSULTE'];
					$cpDocConsulte->DATE_CONSULTE = $ue['DATE_CONSULTE'];
					$cpDocConsulte->DOC_CONSULTE = $ue['DOC_CONSULTE'];
					$cpDocConsulte->ID_DOSSIER = $idDossier;
					$cpDocConsulte->ID_DOC = $ue['ID_DOC'];
					$cpDocConsulte->save();
				}
				
				$dbListeDocAjout = new Model_DbTable_ListeDocAjout;
				foreach($listeDocAjout as $val => $ue)
				{
					$cpDocAjout = $dbListeDocAjout->createRow();
					$cpDocAjout->LIBELLE_DOCAJOUT = $ue['LIBELLE_DOCAJOUT'];
					$cpDocAjout->REF_DOCAJOUT = $ue['REF_DOCAJOUT'];
					$cpDocAjout->DATE_DOCAJOUT = $ue['DATE_DOCAJOUT'];
					$cpDocAjout->ID_NATURE = $idNature;
					$cpDocAjout->ID_DOSSIER = $idDossier;
					$cpDocAjout->save();
				}
			}			
		}
		
		
		if ( isset($_POST['docManquant']) ) 
		{
			$docManquantArray = Array();
			$dateDocManquantArray = Array();
			//array_push($prescriptionArray, $assoc);
			if ( isset($_POST['docManquant']) ) 
			{
				foreach ($_POST['docManquant']  as $libelle => $value) {
					if($value != "")
						array_push($docManquantArray, $value);
				}
			}
			
			if ( isset($_POST['dateReceptionDocManquant']) ) 
			{
				foreach ($_POST['dateReceptionDocManquant']  as $libelle => $value) {
					if($value != "")
						array_push($dateDocManquantArray, $value);
				}
			}
			//Zend_Debug::dump($docManquantArray);
			//Zend_Debug::dump($dateDocManquantArray);
			
			$nbDocParam = count($docManquantArray);
			$nbDateParam = count($dateDocManquantArray);

			$dbDossDocManquant = new Model_DbTable_DossierDocManquant;
			
			$cpt = 0;
			foreach ($docManquantArray  as $libelle => $value)
			{
				$docEnC = $dbDossDocManquant->getDocManquantDossNum($idDossier,$cpt);
				//Zend_Debug::dump($docEnC);
				if($docEnC && $docEnC['DATE_DOCSMANQUANT'] == NULL)
				{
					//echo "On find et on met à jour ".$docEnC['ID_DOCMANQUANT']."<br/>";
					$dossDocManquant = $dbDossDocManquant->find($docEnC['ID_DOCMANQUANT'])->current();
					if($nbDateParam > 0 && $cpt < $nbDateParam)
					{
						$dateTab = explode("/",$dateDocManquantArray[$cpt]);
						$value = $dateTab[2]."-".$dateTab[1]."-".$dateTab[0];
						$dossDocManquant->DATE_DOCSMANQUANT = $value;
					}
					$dossDocManquant->save();
				}else if(!$docEnC){
					//echo "existe pas<br/>";
					$dossDocManquant = $dbDossDocManquant->createRow();
					$dossDocManquant->ID_DOSSIER = $idDossier;
					$dossDocManquant->NUM_DOCSMANQUANT = $cpt;
					$dossDocManquant->DOCMANQUANT = $value;
					if($nbDateParam > 0 && $cpt < $nbDateParam)
					{
						$dateTab = explode("/",$dateDocManquantArray[$cpt]);
						$value = $dateTab[2]."-".$dateTab[1]."-".$dateTab[0];
						$dossDocManquant->DATE_DOCSMANQUANT = $value;
					}
					$dossDocManquant->save();
				}
				
				$cpt++;
			}

		}
		//lorsque je crée un nouveau dossier de VP pour un ERP qui a déjà été visité, il faudrait que les « éléments consultés » de base soient les mêmes
		//Sauvegarde des numéro de document d'urbanisme du dossier
		$DBdossierDocUrba = new Model_DbTable_DossierDocUrba;
		$where = $DBdossierDocUrba->getAdapter()->quoteInto('ID_DOSSIER = ?',  $idDossier);
		//echo $where);
		$DBdossierDocUrba->delete($where);
		
		if (isset($_POST['docUrba'])) 
		{
			foreach ($_POST['docUrba']  as $libelle => $value) {				
				$saveDocUrba = $DBdossierDocUrba->createRow();
				$saveDocUrba->ID_DOSSIER = $idDossier;
				$saveDocUrba->NUM_DOCURBA = $value;
				$saveDocUrba->save();
				//echo $value."<br/>";
			}
		}
		
        //Sauvegarde des préventionnistes
        $DBdossierPrev = new Model_DbTable_DossierPreventionniste;
        $DBdossierPrev->delete("ID_DOSSIER = " .  $idDossier);
        if (isset($_POST['preventionniste'])) 
		{
            foreach ($_POST['preventionniste'] as $prev => $infos) {
                $savePrev = $DBdossierPrev->createRow();
                $savePrev->ID_DOSSIER = $idDossier;
                $savePrev->ID_PREVENTIONNISTE = $infos;
                $savePrev->save();
            }
        }

        //Sauvegarde des informations concernant l'affectation d'un dossier à une commission
        $dbDossierAffectation = new Model_DbTable_DossierAffectation;
		$dbDateComm = new Model_DbTable_DateCommission;
        if ($this->_getParam('COMMISSION_DOSSIER') == '') {
            $dbDossierAffectation->deleteDateDossierAffect($idDossier);
        } else {
			$dbDossierAffectation->deleteDateDossierAffect($idDossier);
            $affectation = $dbDossierAffectation->createRow();
            if ($this->_getParam('ID_AFFECTATION_DOSSIER_VISITE') && $this->_getParam('ID_AFFECTATION_DOSSIER_VISITE') != '') {
                $affectation->ID_DATECOMMISSION_AFFECT = $this->_getParam('ID_AFFECTATION_DOSSIER_VISITE');
                $affectation->ID_DOSSIER_AFFECT = $idDossier;
                $affectation->save();
				
				//On recupere l'affectation à une visite pour renseigner dans dossier car la date sera utilisée dans établissement
				$dateCommDoss = $dbDateComm->find($this->_getParam('ID_AFFECTATION_DOSSIER_VISITE'))->current();
				$nouveauDossier->DATEVISITE_DOSSIER = $dateCommDoss->DATE_COMMISSION;
				$nouveauDossier->save();
            }

            $affectation = $dbDossierAffectation->createRow();
            if ($this->_getParam('ID_AFFECTATION_DOSSIER_COMMISSION') && $this->_getParam('ID_AFFECTATION_DOSSIER_COMMISSION') != '')
			{
                $affectation->ID_DATECOMMISSION_AFFECT = $this->_getParam('ID_AFFECTATION_DOSSIER_COMMISSION');
                $affectation->ID_DOSSIER_AFFECT = $idDossier;
                $affectation->save();
				//On recupere l'affectation à une commission pour renseigner dans dossier car la date sera utilisée dans établissement
				$dateCommDoss = $dbDateComm->find($this->_getParam('ID_AFFECTATION_DOSSIER_COMMISSION'))->current();
				$nouveauDossier->DATECOMM_DOSSIER = $dateCommDoss->DATE_COMMISSION;
				$nouveauDossier->save();
            }
        }

        //on envoi l'id à la vue pour qu'elle puisse rediriger vers la bonne page
        echo trim($idDossier);

    }

//Autocomplétion pour selection TEXTE
    public function selectiontexteAction()
    {
        if (isset($_GET['q'])) {
            $DBprescTexte = new Model_DbTable_PrescriptionTexte;
            //$this->view->selectTexte = $DBprescTexte->selectTexte($_GET['q']);
            $this->view->selectTexte = $DBprescTexte->fetchAll("LIBELLE_TEXTE LIKE '%".$_GET['q']."%'")->toArray();
        }
    }

//Autocomplétion pour selection ARTICLE
    public function selectionarticleAction()
    {
        if (isset($_GET['q'])) {
            $DBprescArticle = new Model_DbTable_PrescriptionArticle;
            //$this->view->selectArticle = $DBprescArticle->selectArticle($_GET['q']);
            $this->view->selectArticle = $DBprescArticle->fetchAll("LIBELLE_ARTICLE LIKE '%".$_GET['q']."%'")->toArray();
        }
    }

//Autocomplétion pour selection ABREVIATION
    public function selectionabreviationAction()
    {
        if (isset($_GET['q'])) {
            $DBprescPrescType = new Model_DbTable_PrescriptionType;
            //$this->view->selectAbreviation = $DBprescArticle->selectArticle($_GET['q']);
            $this->view->selectAbreviation = $DBprescPrescType->fetchAll("ABREVIATION_PRESCRIPTIONTYPE LIKE '%".$_GET['q']."%'")->toArray();
        }
    }

//Autocomplétion pour selection ETABLISSEMENT
    public function selectionetabAction()
    {
        // Création de l'objet recherche
        $search = new Model_DbTable_Search;

        // On set le type de recherche
        $search->setItem("etablissement");
        $search->limit(5);

        if( array_key_exists("ID_GENRE", $_GET) )
            $search->setCriteria("genre.ID_GENRE", $this->_request->ID_GENRE + 1);

        // On recherche avec le libellé
        $search->setCriteria("LIBELLE_ETABLISSEMENTINFORMATIONS", $this->_request->q, false);

        // On balance le résultat sur la vue
        $this->view->resultats = $search->run()->getAdapter()->getItems(0, 99999999999)->toArray();
    }

//Action permettant de lister les établissements et les dossiers liés
    public function lieesAction()
    {
        $DBdossier = new Model_DbTable_Dossier;
        $this->view->listeEtablissement = $DBdossier->getEtablissementDossier((int) $this->_getParam("id"));

        /*
        foreach ($this->view->listeEtablissement as $etablissement) {
            //echo $etablissement['ID_ETABLISSEMENTDOSSIER'];
            $this->view->lastFicheEtab = $DBdossier->getLastInfosEtab($etablissement['ID_ETABLISSEMENTDOSSIER']);
        }
        */
        //Zend_Debug::dump($this->view->listeEtablissement);
    }

    public function contactAction()
    {
        $this->view->idDossier = (int) $this->_getParam("id");
    }

//GESTION DOCUMENTS CONSULTES
    public function docconsulteAction()
    {
        //récupération du type de dossier (etude / visite)
        $dbdossier = new Model_DbTable_Dossier;
        $dossierType = $dbdossier->getTypeDossier((int) $this->_getParam("id"));

        $this->view->idDossier = (int) $this->_getParam("id");

        //récupération de toutes les natures
        $DBdossierNature = new Model_DbTable_DossierNature;
        $this->view->listeNatures = $DBdossierNature->getDossierNaturesLibelle((int) $this->_getParam("id"));

        //suivant le type on récup la liste des docs que l'on met dans un tableau a multi dimension.
        //l'index de chaque liste sera l'id de la nature
        $dblistedoc = new Model_DbTable_DossierListeDoc;
        $dblistedocAjout = new Model_DbTable_ListeDocAjout;

        foreach ($this->view->listeNatures as $index => $nature) {
            if ($dossierType['TYPE_DOSSIER'] == 2 || $dossierType['TYPE_DOSSIER'] == 3) {
                if ($nature["ID_NATURE"] == 20) {
                    //cas d'une visite réception de travaux
                    $listeDocConsulte[$nature["ID_NATURE"]] = $dblistedoc->getDocVisiteRT();
                    //$listeDocConsulte[$nature["ID_NATURE"]] = $dblistedoc->getDocVisite();
                } else {
                    //cas général d'une visite
                    $listeDocConsulte[$nature["ID_NATURE"]] = $dblistedoc->getDocVisite();
                }
            } else if ($dossierType['TYPE_DOSSIER'] == 1 ){
                //cas d'une etude
                $listeDocConsulte[$nature["ID_NATURE"]] = $dblistedoc->getDocEtude();
            } else {
				$listeDocConsulte = 0;
			}

            //ici on récupère tous les documents qui ont été renseigné dans la base par un utilisateur (avec id du dossier et de la nature)
            $listeDocRenseigne[$nature["ID_NATURE"]] = $dblistedoc->recupDocDossier($this->_getParam("id"),$nature["ID_NATURE"]);

            //ici on récupère tous les documents qui ont été ajoutés par l'utilisateur (document non proposé par défaut)
            $listeDocAjout[$nature["ID_NATURE"]] = $dblistedocAjout->getDocAjout((int) $this->_getParam("id"),$nature["ID_NATURE"]);

        }

        //Zend_Debug::dump($listeDocConsulte);
        //On envoie à la vue la liste des documents consultés classés par nature (peux y avoir plusieurs fois la même liste)
        $this->view->listeDocs = $listeDocConsulte;

        //on envoie à la vue tous les documents qui ont été renseignés parmi la liste de ceux récupéré dans la boucle ci-dessus
        $this->view->dossierDocConsutle = $listeDocRenseigne;
        //Zend_Debug::dump($this->view->dossierDocConsutle);

        //on recup les docs ajouté pr le dossiers
        $this->view->listeDocsAjout = $listeDocAjout;
		//Zend_Debug::dump($this->view->listeDocsAjout);
        //$this->view->dossierDocConsutle = $dblistedoc->recupDocDossier((int) $this->_getParam("id"));
    }

    public function ajoutdocAction($idDossier)
    {
        $dblistedocajout = new Model_DbTable_ListeDocAjout;

        //insertion dans la base de données du nouveau type de document
        $newDoc = $dblistedocajout->createRow();
        $newDoc->LIBELLE_DOCAJOUT = $this->_getParam("libelleNewDoc");
        $newDoc->ID_DOSSIER = $this->_getParam("idDossier");
        $newDoc->ID_NATURE = $this->_getParam("natureDocAjout");
        $newDoc->save();

        $this->view->idNatureNewDoc = $this->_getParam("natureDocAjout");
        $this->view->idNewDoc = $newDoc->ID_DOCAJOUT;
        $this->view->libelleNewDoc = $newDoc->LIBELLE_DOCAJOUT;

        $this->render('ajoutdoc');
    }

    public function validdocAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $id_dossier = (int) $this->_getParam("idDossier");
        $idValid = $this->_getParam("id");
        //echo $idValid."<br/>";
        $datePost = $this->_getParam("date_".$idValid);

        if($id_dossier == '' || $idValid == '')

            return false;

        //echo $datePost;
        $dateTab = explode("/",$datePost);
        $date = $dateTab[2]."-".$dateTab[1]."-".$dateTab[0];

        $ref = str_replace("\"","''",$_POST['ref_'.$idValid]);

        //on définit s'il sagid d'un doc ajouté ou nom
        $tabNom = explode("_",$idValid);

        //echo $idValid; 22_8 => idNature_idDoc
        if (count($tabNom) == 2) {
            //echo "cas d'une liste base";
            $dblistedoc = new Model_DbTable_DossierDocConsulte;
            echo "vala les params ".$id_dossier." - ".$tabNom[0]." - ".$tabNom[1];
            $listevalid = $dblistedoc->getGeneral($id_dossier,$tabNom[0],$tabNom[1]);
            //Zend_Debug::dump($listevalid);

            $liste = $dblistedoc->find($listevalid['ID_DOSSIERDOCCONSULTE'])->current();
            //echo "liste valide resultat ".count($listevalid);

            if ($listevalid) {
                //si UN enregistrement existe
                $liste = $dblistedoc->find($listevalid['ID_DOSSIERDOCCONSULTE'])->current();
                $liste->REF_CONSULTE = $ref;
                $liste->DATE_CONSULTE = $date;
            } else {
                //si AUCUN enregistrement existe
                 $liste = $dblistedoc->createRow();
                $liste->ID_DOC = $tabNom[1];
                $liste->ID_DOSSIER = $id_dossier;
                $liste->ID_NATURE = $tabNom[0];
                $liste->REF_CONSULTE = $ref;
                $liste->DATE_CONSULTE = $date;
                $liste->DOC_CONSULTE = 1;
            }
            $liste->save();

        } else {
            //echo "cas d'une liste ajoutée Doc ajout";
            //On commence par isoler l'id de "_aj"
            $idDocAjout = explode("_",$this->_getParam("id"));
            $dblistedocajout = new Model_DbTable_ListeDocAjout;

            $docAjout = $dblistedocajout->find($idDocAjout[1])->current();
            //$nbRez = $dbdocsearch->count();

            $docAjout->REF_DOCAJOUT = $ref;
            $docAjout->DATE_DOCAJOUT = $date;
            $docAjout->ID_DOSSIER = $id_dossier;

            $docAjout->save();
        }
    }

//GESTION LIAISON ETABLISSMENTS
    public function addetablissementAction()
    {
        $DBetablissementDossier = new Model_DbTable_EtablissementDossier;
        $newEtabDossier = $DBetablissementDossier->createRow();
        $newEtabDossier->ID_ETABLISSEMENT = $this->_getParam("idSelect");
        $newEtabDossier->ID_DOSSIER = $this->_getParam("idDossier");
        $newEtabDossier->save();

        $this->view->libelleEtab = $this->_getParam("libelleSelect");
        $this->view->infosEtab = $newEtabDossier;
    }

    public function deleteetablissementAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $DBetablissementDossier = new Model_DbTable_EtablissementDossier;
        //$deleteEtabDossier = $DBetablissementDossier->delete("ID_ETABLISSEMENTDOSSIER = " . $this->_getParam("idEtabDossier"));
        $deleteEtabDossier = $DBetablissementDossier->find($this->_getParam("idEtabDossier"))->current();
        $deleteEtabDossier->delete();
    }

    public function deleteliendossierAction()
    {
        //action appelée lorsque l'on supprime un lien avec un autre dossier
        $this->_helper->viewRenderer->setNoRender();

        $DBetablissementDossier = new Model_DbTable_DossierLie;
        $deleteEtabDossier = $DBetablissementDossier->find($this->_getParam("idLienDossier"))->current();
        $deleteEtabDossier->delete();
    }

    public function dialogcommshowAction()
    {
        $dbDateComm = new Model_DbTable_DateCommission;
        $infosDateComm = $dbDateComm->find($this->_getParam("idDateComm"))->current();
        $this->view->infosDateComm = $infosDateComm;
        //echo "INFOS => ".Zend_Debug::dump($infosDateComm);

        $date = new Zend_Date($infosDateComm['DATE_COMMISSION'], Zend_Date::DATES);
        $this->view->dateSelect = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME." ".Zend_Date::YEAR);

        $dbCommission = new Model_DbTable_Commission;
        $infosComm = $dbCommission->find($infosDateComm['COMMISSION_CONCERNE'])->current();
        $this->view->infosComm = $infosComm;
        //Zend_Debug::dump($infosComm);

    }

    public function affectationodjAction()
    {
        $this->_helper->viewRenderer->setNoRender();

    }

    public function descriptifsAction()
    {
        //$this->_helper->viewRenderer->setNoRender();
        $idDossier = (int) $this->_getParam("id");
        $DBdossier = new Model_DbTable_Dossier;
        $this->view->infosDossier = $DBdossier->find($idDossier)->current();

        //Zend_Debug::dump($this->view->infosDossier);
    }

//GENERATION DOCUMENTS
    public function dialoggenrapportAction()
    {
        //Permet de charger la liste des établissements liés au dossier pour la selection des rapports à generer
        $DBdossier = new Model_DbTable_Dossier;
        $this->view->listeEtablissement = $DBdossier->getEtablissementDossier((int) $this->_getParam("idDossier"));
		//Zend_Debug::dump($this->view->listeEtablissement);
    }

    public function generationrapportAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $listeEtab = $this->_getParam("idEtab");
        $idDossier = $this->_getParam("idDossier");

        foreach ($this->_getParam("idEtab") as $etablissementId) {
            $this->creationdocAction($idDossier,$etablissementId);
            //echo $idDossier." - ".$etablissementId."<br/>";
        }

    }

    public function creationdocAction($idDossier, $idEtab)
    {
        $this->view->idDossier = $idDossier;

        $this->view->fichierSelect = $this->_getParam("fichierSelect");

        //RECUPERATIONS DES INFOS CONCERNANT L'ETABLISSEMENT
        $model_typeactivite = new Model_DbTable_TypeActivite;

        //Récupération des documents d'urbanisme
        $DBdossierDocUrba = new Model_DbTable_DossierDocUrba;
        $dossierDocUrba = $DBdossierDocUrba->getDossierDocUrba($idDossier);
        $listeDocUrba = "";
        foreach ($dossierDocUrba as $var) {
            $listeDocUrba .= $var['NUM_DOCURBA'].", ";
        }

        $this->view->listeDocUrba = substr($listeDocUrba, 0, -2);

        $model_etablissement = new Model_DbTable_Etablissement;
        $etablissement = $model_etablissement->find($idEtab)->current();
		//Zend_Debug::dump($etablissement);
		
        $this->view->numWinPrev = $etablissement['NUMEROID_ETABLISSEMENT'];
		$this->view->numTelEtab = $etablissement['TELEPHONE_ETABLISSEMENT'];
		$this->view->numFaxEtab = $etablissement['FAX_ETABLISSEMENT'];

        //Informations de l'établissement (catégorie, effectifs, activité / type principal)
        $object_informations = $model_etablissement->getInformations($idEtab);
		//Zend_Debug::dump($object_informations);
		$this->view->entite = $object_informations;
		
        $this->view->numPublic = $object_informations["EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"];
        $this->view->numPersonnel = $object_informations["EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS"];

        $dbCategorie = new Model_DbTable_Categorie;
		if($object_informations["ID_CATEGORIE"]){
			$categorie = $dbCategorie->getCategories($object_informations["ID_CATEGORIE"]);
			//Zend_Debug::dump($categorie);
			$categorie = explode(" ",$categorie['LIBELLE_CATEGORIE']);
			$this->view->categorieEtab = $categorie[0];
		}
	

        $this->view->etablissementLibelle = $object_informations['LIBELLE_ETABLISSEMENTINFORMATIONS'];
		
		$dbType = new Model_DbTable_Type;
		$lettreType = $dbType->find($object_informations['ID_TYPE'])->current();
        $this->view->typeLettreP = $lettreType['LIBELLE_TYPE'];

        $activitePrincipale = $model_typeactivite->find($object_informations["ID_TYPEACTIVITE"])->current();
        $this->view->libelleActiviteP = $activitePrincipale["LIBELLE_ACTIVITE"];
		
        //echo "ID ETAB INFO ".$object_informations->ID_ETABLISSEMENTINFORMATIONS;
        // Types / activités secondaires
        $model_typesactivitessecondaire = new Model_DbTable_EtablissementInformationsTypesActivitesSecondaires;
        $array_types_activites_secondaires = $model_typesactivitessecondaire->fetchAll("ID_ETABLISSEMENTINFORMATIONS = " . $object_informations->ID_ETABLISSEMENTINFORMATIONS)->toArray();
		
		//Zend_Debug::dump($array_types_activites_secondaires);
		
        $typeS = "";
        $actS = "";

        foreach ($array_types_activites_secondaires as $var) {
			$lettreTypeS = $dbType->find($var['ID_TYPE_SECONDAIRE'])->current();
            $typeS .= $lettreTypeS['LIBELLE_TYPE'].", ";
            $activiteSearchLibelle = $model_typeactivite->find($var['ID_TYPEACTIVITE_SECONDAIRE'])->current();
            $actS .= $activiteSearchLibelle["LIBELLE_ACTIVITE"].", ";
        }

        $this->view->activiteSecondaire = substr($actS, 0, -2);
        $this->view->typeSecondaire = substr($typeS, 0, -2);
		
		
		//En fonction du genre on récupère les informations de l'établissement ou du site
		if($object_informations['ID_GENRE'] == 2){
			//cas d'un établissement
			$this->view->GN = 2;
		}else if($object_informations['ID_GENRE'] == 3){
			//cas d'une céllule
			$this->view->GN = 3;
		}
		
		$dbEtabLie = new Model_DbTable_EtablissementLie;
		$etabLie = $dbEtabLie->recupEtabCellule($object_informations['ID_ETABLISSEMENT']);
		//Zend_Debug::dump($etabLie);
		if($etabLie != null){
			$idPere = $etabLie[0]['ID_ETABLISSEMENT'];
			$this->view->infoPere = $model_etablissement->getInformations($idPere);	
			//Zend_Debug::dump($this->view->infoPere);
			$lettreType = $dbType->find($this->view->infoPere['ID_TYPE'])->current();
			$this->view->typeLettrePPere = $lettreType['LIBELLE_TYPE'];
			$activitePrincipale = $model_typeactivite->find($this->view->infoPere["ID_TYPEACTIVITE"])->current();
			$this->view->libelleActivitePPere = $activitePrincipale["LIBELLE_ACTIVITE"];
			$this->view->categorieEtabPere = $this->view->infoPere['ID_CATEGORIE'];
		}	
		
		
        // Adresses
        $model_adresse = new Model_DbTable_EtablissementAdresse;
        $array_adresses = $model_adresse->get($idEtab);

		//Zend_Debug::dump($array_adresses);		
        if (count($array_adresses) > 0) {
            $this->view->communeEtab = $array_adresses[0]["LIBELLE_COMMUNE"];
			$adresse = "";
			if($array_adresses[0]['NUMERO_ADRESSE'] != 0)
				$adresse = $array_adresses[0]['NUMERO_ADRESSE']." ";
			if($array_adresses[0]["LIBELLE_RUE"] != '')
				$adresse .= $array_adresses[0]["LIBELLE_RUE"]." ";
			if($array_adresses[0]["CODEPOSTAL_COMMUNE"] != '')
				$adresse .= $array_adresses[0]["CODEPOSTAL_COMMUNE"]." ";
			if($array_adresses[0]["LIBELLE_COMMUNE"] != '')
				$adresse .= $array_adresses[0]["LIBELLE_COMMUNE"]." ";
            $this->view->etablissementAdresse = $adresse;
        }

        //Zend_Debug::dump($etablissement->toArray());

        //Récupération de tous les champs de la table dossier
        $DBdossier = new Model_DbTable_Dossier;
        $this->view->infosDossier = $DBdossier->find($idDossier)->current();
		//Zend_Debug::dump($this->view->infosDossier);
		
		//SERVICEINSTRUC_DOSSIER   servInstructeur
		$dbGroupement = new Model_DbTable_Groupement;
		$groupement = $dbGroupement->find($this->view->infosDossier["SERVICEINSTRUC_DOSSIER"])->current();
		//Zend_Debug::dump($groupement);
		$this->view->servInstructeur = $groupement['LIBELLE_GROUPEMENT'];
		
		//On recherche si un directeur unique de sécurité existe
		$dbDossierContact = new Model_DbTable_DossierContact;
		$dusInfos = $dbDossierContact->recupDUS($idDossier);
		if(count($dusInfos) == 1)
			$this->view->dusDossier = $dusInfos[0];
		
		//Zend_Debug::dump($dusInfos);
		
		//Affichage dossier incomplet pour generation dossier incomplet
		//Recuperation des documents manquants dans le cas d'un dossier incomplet
		$dbDossDocManquant = new Model_DbTable_DossierDocManquant;
		$this->view->listeDocManquant = $dbDossDocManquant->getDocManquantDossLast($idDossier);
		//Zend_Debug::dump($this->view->listeDocManquant);
		
        //$avisDossier = $this->view->infosDossier["AVIS_DOSSIER"];
        $DBavisDossier = new Model_DbTable_Avis;
        $libelleAvis = $DBavisDossier->find($this->view->infosDossier["AVIS_DOSSIER"])->current();
        $this->view->avisDossier = $libelleAvis["LIBELLE_AVIS"];

        $DBdossierCommission = new Model_DbTable_Commission;
        if ($this->view->infosDossier['COMMISSION_DOSSIER']) {
            $this->view->commissionInfos = $DBdossierCommission->find($this->view->infosDossier['COMMISSION_DOSSIER'])->current();
        } else {
            $this->view->commissionInfos = "Aucune commission";
        }
		
		if($this->view->infosDossier['INCOMPLET_DOSSIER'] == 1){
			$this->view->etatDossier = "Incomplet";
		}else{
			$this->view->etatDossier = "Complet";
		}
        //$this->view->commissionLibelle = $this->view->commissionInfos['LIBELLE_COMMISSION'];

        //récup de l'id de la piece jointe qu'aura le rapport
        $DBpieceJointe = new Model_DbTable_PieceJointe;
        $this->view->idRapportPj = $DBpieceJointe->maxPieceJointe();

        if (!isset($this->view->idRapportPj['MAX(ID_PIECEJOINTE)'])) {
            $this->view->idPieceJointe = 1;
        } else {
            $this->view->idPieceJointe = $this->view->idRapportPj['MAX(ID_PIECEJOINTE)'] + 1;
        }

        //récuperation de la date de passage en commission
        $dbAffectDossier = new Model_DbTable_DossierAffectation;
        $affectDossier = $dbAffectDossier->find(NULL,$idDossier)->current();
        $this->view->affectDossier = $affectDossier;

        //Concernant cette affectation on récupere les infos sur la commission (date aux différents format)
        $dbDateComm = new Model_DbTable_DateCommission;
        $dateComm = $dbDateComm->find($affectDossier['ID_DATECOMMISSION_AFFECT'])->current();

        if ($dateComm['DATE_COMMISSION'] != '') {
            $date = new Zend_Date($dateComm['DATE_COMMISSION'], Zend_Date::DATES);
            //$this->view->dateCommValue = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
            $this->view->dateCommEntete = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
        }

        //PARTIE DOC CONSULTE

            //récupération du type de dossier (etude / visite)
            $dbdossier = new Model_DbTable_Dossier;
            $dossierType = $dbdossier->getTypeDossier((int) $idDossier);
            $dossierNature = $dbdossier->getNatureDossier((int) $idDossier);

            //Zend_Debug::dump($dossierNature);

            //suivant le type on récup la liste des docs
            $dblistedoc = new Model_DbTable_DossierListeDoc;
            
            if ($dossierType['TYPE_DOSSIER'] == 2 || $dossierType['TYPE_DOSSIER'] == 3) {
                if ($dossierNature['ID_NATURE'] == 20) {
                    //cas d'une visite réception de travaux
                    $listeDocConsulte = $dblistedoc->getDocVisiteRT();
                } else {
                    $listeDocConsulte = $dblistedoc->getDocVisite();
                }

            } else {
                $listeDocConsulte = $dblistedoc->getDocEtude();
            }

            //on envoi la liste de base à la vue
            $this->view->listeDocs = $listeDocConsulte;

            //Zend_Debug::dump($this->view->listeDocs);
            //on recup les docs ajouté pr le dossiers
            $dblistedocAjout = new Model_DbTable_ListeDocAjout;
            $listeDocAjout = $dblistedocAjout->getDocAjout((int) $idDossier,$dossierNature['ID_NATURE']);
            $this->view->listeDocsAjout = $listeDocAjout;

            //Zend_Debug::dump($this->view->listeDocsAjout);
            $this->view->dossierDocConsutle = $dblistedoc->recupDocDossier((int) $idDossier,$dossierNature['ID_NATURE']);

        /*
        PARTIE PRESCRIPTION
        */
		
			//on affiche les prescriptions du dossier
			$dbPrescDossier = new Model_DbTable_PrescriptionDossier;
			$listePrescDossier = $dbPrescDossier->recupPrescDossier($this->_getParam('idDossier'));
			//Zend_Debug::dump($listePrescDossier);
			$dbPrescDossierAssoc = new Model_DbTable_PrescriptionDossierAssoc;
			
			$prescriptionArray = array();
			foreach($listePrescDossier as $val => $ue)
			{
				if($ue['ID_PRESCRIPTION_TYPE'])
				{
					//cas d'une prescription type
					//echo "Prescription type : ".$ue['ID_PRESCRIPTION_TYPE'];
					$assoc = $dbPrescDossierAssoc->getPrescriptionTypeAssoc($ue['ID_PRESCRIPTION_TYPE'],$ue['ID_PRESCRIPTION_DOSSIER']);
					array_push($prescriptionArray, $assoc);
					//Zend_Debug::dump($assoc);
				}else{
					//cas d'une prescription particulière
					//echo "Prescription pas type : ".$ue['ID_PRESCRIPTION_DOSSIER'];
					$assoc = $dbPrescDossierAssoc->getPrescriptionDossierAssoc($ue['ID_PRESCRIPTION_DOSSIER']);
					array_push($prescriptionArray, $assoc);
					//Zend_Debug::dump($assoc);				
				}
			}
			$this->view->prescriptionDossier = $prescriptionArray;
			//Zend_Debug::dump($this->view->prescriptionDossier);
					
			
        // GESTION DES DATES
            //Conversion de la date de dépot en mairie pour l'afficher
            if ($this->view->infosDossier['DATEMAIRIE_DOSSIER'] != '') {
                $date = new Zend_Date($this->view->infosDossier['DATEMAIRIE_DOSSIER'], Zend_Date::DATES);
                $this->view->DATEMAIRIE = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
            }
            //Conversion de la date de dépot en secrétariat pour l'afficher
            if ($this->view->infosDossier['DATESECRETARIAT_DOSSIER'] != '') {
                $date = new Zend_Date($this->view->infosDossier['DATESECRETARIAT_DOSSIER'], Zend_Date::DATES);
                $this->view->DATESECRETARIAT = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
            }
            //Conversion de la date de réception SDIS
            if ($this->view->infosDossier['DATEINSERT_DOSSIER'] != '') {
                $date = new Zend_Date($this->view->infosDossier['DATESDIS_DOSSIER'], Zend_Date::DATES);
                $this->view->DATEINSERTDOSSIER = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
            }
			
			//Conversion de la date de création du dossier
            if ($this->view->infosDossier['DATESDIS_DOSSIER'] != '') {
                $date = new Zend_Date($this->view->infosDossier['DATESDIS_DOSSIER'], Zend_Date::DATES);
                $this->view->DATESDIS = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
            }

            $dateDuJour = new Zend_Date();
            $DBpieceJointe = new Model_DbTable_PieceJointe;
            $nouvellePJ = $DBpieceJointe->createRow();
            $nouvellePJ->ID_PIECEJOINTE = $this->view->idPieceJointe;
            $nouvellePJ->NOM_PIECEJOINTE = "Rapport";
            $nouvellePJ->EXTENSION_PIECEJOINTE = ".odt";
            $nouvellePJ->DESCRIPTION_PIECEJOINTE = "Rapport de l'établissement ".$object_informations['LIBELLE_ETABLISSEMENTINFORMATIONS']." généré le ".$dateDuJour->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
            $nouvellePJ->DATE_PIECEJOINTE = $dateDuJour->get(Zend_Date::YEAR."-".Zend_Date::MONTH."-".Zend_Date::DAY." ".Zend_Date::HOUR.":".Zend_Date::MINUTE.":".Zend_Date::SECOND);
            $nouvellePJ->save();

            echo "<a href='/data/uploads/pieces-jointes/".$nouvellePJ->ID_PIECEJOINTE.".odt'>Ouvrir le rapport de l'établissement : ".$object_informations['LIBELLE_ETABLISSEMENTINFORMATIONS']."<a/><br/><br/>";

            $DBsave = new Model_DbTable_DossierPj;
            $linkPj = $DBsave->createRow();
            $linkPj->ID_DOSSIER = $idDossier;
            $linkPj->ID_PIECEJOINTE = $nouvellePJ->ID_PIECEJOINTE;
            $linkPj->save();

					
		/*
        PARTIE TEXTES APPLICABLES
        */

			//on recupere tout les textes applicables qui ont été cochés dans le dossier
			$dbDossierTextesAppl = new Model_DbTable_DossierTextesAppl;
			$this->view->listeTextesAppl = $dbDossierTextesAppl->recupTextesDossierGenDoc($this->_getParam('idDossier'));
			
			//Zend_Debug::dump($this->view->listeTextesAppl);
			
			
            $this->render('creationdoc');

        //Zend_Debug::dump($this->view->infosDossier);

    }

    public function generationconvocAction()
    {
        //$this->_helper->viewRenderer->setNoRender();
        $dateCommId = $this->_getParam("dateCommId");
        $this->view->idComm = $dateCommId;
		
		//on recupere le type de commission (salle / visite / groupe de visite)
		$dbDateComm = new Model_DbTable_DateCommission;
		$commissionInfo = $dbDateComm->find($dateCommId)->current()->toArray();
		//1 = salle . 2 = visite . 3 = groupe de visite
		$this->view->typeCommission = $commissionInfo['ID_COMMISSIONTYPEEVENEMENT'];
		//Zend_Debug::dump($commissionInfo);
		
        //On récupère la liste des dossiers
        $dbDateCommPj = new Model_DbTable_DateCommissionPj;
        $listeDossiers = $dbDateCommPj->getDossiersInfos($dateCommId);
        //Zend_Debug::dump($listeDossiers);

        //Récupération des membres de la commission
        $model_membres = new Model_DbTable_CommissionMembre;

        //$this->view->membresComm = $model_membres->get($listeDossiers[0]["COMMISSION_DOSSIER"]);
        $this->view->membresFiles = $model_membres->fetchAll("ID_COMMISSION = " . $listeDossiers[0]["COMMISSION_DOSSIER"]);
        //Zend_Debug::dump($this->view->membresFiles );

        //On récupère le nom de la commission
        $model_commission = new Model_DbTable_Commission;
        $this->view->commissionInfos = $model_commission->find($listeDossiers[0]["COMMISSION_DOSSIER"])->toArray();
        //Zend_Debug::dump($this->view->commissionInfos);

        //afin de récuperer les informations des communes (adresse des mairies etc)
        $model_adresseCommune = new Model_DbTable_AdresseCommune;
        $model_utilisateurInfo = new Model_DbTable_UtilisateurInformations;

        $libelleCommune = "";
        $tabCommune[] = array();
        $numCommune = 0;
        foreach ($dbDateCommPj->getDossiersInfos($dateCommId) as $doss => $infos) {
            //Zend_Debug::dump($infos);
            //echo "passage num :".$numCommune." commune name = ".$libelleCommune." alors que celui passé est ".$infos["LIBELLE_COMMUNE"]."<br/>";
            if ($libelleCommune != $infos["LIBELLE_COMMUNE"]) {
                $libelleCommune = $infos["LIBELLE_COMMUNE"];
                //echo $infos["LIBELLE_COMMUNE"];

                $adresseCommune = $model_adresseCommune->find($infos["NUMINSEE_COMMUNE"])->toArray();

                $communeInfo = $model_utilisateurInfo->find($adresseCommune[0]["ID_UTILISATEURINFORMATIONS"])->toArray();

                $tabCommune[$numCommune] = array($libelleCommune,$communeInfo);
                $numCommune++;
            }
        }

        $this->view->listeCommunes = $tabCommune;
        $this->view->dossierComm = $listeDossiers;

        //Zend_Debug::dump($this->view->dossierComm);

        //récuperation du nom de la commission
        $this->view->nomComm = $listeDossiers[0]["LIBELLE_DATECOMMISSION"];
        $this->view->dateComm = $listeDossiers[0]["DATE_COMMISSION"];
        $this->view->heureDeb = $listeDossiers[0]["HEUREDEB_COMMISSION"];

    }

    public function generationodjAction()
    {
        $dateCommId = $this->_getParam("dateCommId");
        $this->view->idComm = $dateCommId;

        //On récupère la liste des dossiers
        //Suivant si l'on prend en compte les heures ou non on choisi la requete à effectuer
        $dbDateComm = new Model_DbTable_DateCommission;
        $commSelect = $dbDateComm->find($dateCommId)->current();
        //echo $commSelect['GESTION_HEURES'];
        $dbDateCommPj = new Model_DbTable_DateCommissionPj;

        if ($commSelect['GESTION_HEURES'] == 1) {
            //prise en compte heures
            $listeDossiers = $dbDateCommPj->getDossiersInfosByHour($dateCommId);
        } elseif ($commSelect['GESTION_HEURES'] == 0) {
            //prise en compte ordre
            $listeDossiers = $dbDateCommPj->getDossiersInfosByOrder($dateCommId);
        }
		
        //Zend_Debug::dump($listeDossiers);
	
        //Récupération des membres de la commission
        $model_membres = new Model_DbTable_CommissionMembre;
		
		//echo "<br/> ! ! ! ".$listeDossiers[0]["COMMISSION_DOSSIER"];

        //$this->view->membresComm = $model_membres->get($listeDossiers[0]["COMMISSION_DOSSIER"]);
        $this->view->membresFiles = $model_membres->fetchAll("ID_COMMISSION = " . $listeDossiers[0]["COMMISSION_DOSSIER"]);
        //Zend_Debug::dump($this->view->membresFiles );

        //On récupère le nom de la commission
        $model_commission = new Model_DbTable_Commission;
        $this->view->commissionInfos = $model_commission->find($listeDossiers[0]["COMMISSION_DOSSIER"])->toArray();
        //Zend_Debug::dump($this->view->commissionInfos);

        //afin de récuperer les informations des communes (adresse des mairies etc)
        $model_adresseCommune = new Model_DbTable_AdresseCommune;
        $model_utilisateurInfo = new Model_DbTable_UtilisateurInformations;

        $libelleCommune = "";
        $tabCommune[] = array();
        $numCommune = 0;
        foreach ($dbDateCommPj->getDossiersInfos($dateCommId) as $doss => $infos) {
            //Zend_Debug::dump($infos);
            //echo "passage num :".$numCommune." commune name = ".$libelleCommune." alors que celui passé est ".$infos["LIBELLE_COMMUNE"]."<br/>";
            if ($libelleCommune != $infos["LIBELLE_COMMUNE"]) {
                $libelleCommune = $infos["LIBELLE_COMMUNE"];
                //echo $infos["LIBELLE_COMMUNE"];

                $adresseCommune = $model_adresseCommune->find($infos["NUMINSEE_COMMUNE"])->toArray();

                $communeInfo = $model_utilisateurInfo->find($adresseCommune[0]["ID_UTILISATEURINFORMATIONS"])->toArray();

                $tabCommune[$numCommune] = array($libelleCommune,$communeInfo);
                $numCommune++;
            }
        }

        $this->view->listeCommunes = $tabCommune;
        //Zend_Debug::dump($listeDossiers);
        $this->view->dossierComm = $listeDossiers;

    }
    
    public function descriptifAction()
    {
        if ((int) $this->_getParam("id")) {
            //Cas d'affichage des infos d'un dossier existant
            $this->view->do = 'edit';
            //On récupère l'id du dossier
            $idDossier = (int) $this->_getParam("id");
            $this->view->idDossier = $idDossier;
            //Récupération de tous les champs de la table dossier
            $DBdossier = new Model_DbTable_Dossier;
            $this->view->infosDossier = $DBdossier->find($idDossier)->current();
        }
        
        if ($this->_request->DESCRIPTIF_DOSSIER)
        {
            $DBdossier = new Model_DbTable_Dossier;
            $dossier = $DBdossier->find($this->_request->id)->current();
            $dossier->DESCRIPTIF_DOSSIER = $this->_request->DESCRIPTIF_DOSSIER;
            $dossier->save();
            
            $this->_helper->_redirector("descriptif", $this->_request->getControllerName(), null, array("id" => $this->_request->id));
        }
        
    }
	
	public function textesapplicablesAction()
    {
		//on commence par afficher tous les texte applicables qui sont visible regroupés par leurs type
		$dbTextesAppl = new Model_DbTable_TextesAppl;
		$this->view->listeTextesAppl = $dbTextesAppl->recupTextesApplVisible();
		
		//on recupere tout les textes applicables qui ont été cochés dans le dossier
		$dbDossierTextesAppl = new Model_DbTable_DossierTextesAppl;
		$liste = $dbDossierTextesAppl->recupTextesDossier($this->_getParam("id"));
		//Zend_Debug::dump($liste);
		$listeId = array();
		foreach($liste as $val => $ue){
			array_push($listeId,$ue['ID_TEXTESAPPL']);
		}
		
		$this->view->listeIdTexte = $listeId;
		//Zend_Debug::dump($this->view->listeIdTexte);
		/***************
			RECUPERATIONS INFOS ETABLISSEMENT (cellule ou etab pour generation des avis)
		******************/
		if ($this->_getParam("id_etablissement")) {
			$idEtablissement = $this->_getParam("id_etablissement");
		} elseif ((int) $this->_getParam("id")) {
			//echo "ICI ON EST DANS UN DOSS EXISTANT (consultation/edit dossier)";
			$DBdossier = new Model_DbTable_Dossier;
			$tabEtablissement = $DBdossier->getEtablissementDossier((int) $this->_getParam("id"));
			$this->view->listeEtablissement = $tabEtablissement;
			$idEtablissement = $tabEtablissement[0]['ID_ETABLISSEMENT'];
		}
		$this->view->idEtablissement = $idEtablissement;
	}

//GESTION DE LA PARTIE PRESCRIPTION
    public function prescriptionAction()
    {
		//on affiche les prescriptions du dossier
		$dbPrescDossier = new Model_DbTable_PrescriptionDossier;
		//echo $this->_getParam('id');
		$listePrescDossier = $dbPrescDossier->recupPrescDossier($this->_getParam('id'));
		//Zend_Debug::dump($listePrescDossier);

		$dbPrescDossierAssoc = new Model_DbTable_PrescriptionDossierAssoc;
		
		$prescriptionArray = array();
		foreach($listePrescDossier as $val => $ue)
		{
			if($ue['ID_PRESCRIPTION_TYPE'])
			{
				//cas d'une prescription type
				//echo "Prescription type : ".$ue['ID_PRESCRIPTION_TYPE'];
				$assoc = $dbPrescDossierAssoc->getPrescriptionTypeAssoc($ue['ID_PRESCRIPTION_TYPE'],$ue['ID_PRESCRIPTION_DOSSIER']);
				array_push($prescriptionArray, $assoc);
				//Zend_Debug::dump($assoc);
			}else{
				//cas d'une prescription particulière
				//echo "Prescription pas type : ".$ue['ID_PRESCRIPTION_DOSSIER'];
				$assoc = $dbPrescDossierAssoc->getPrescriptionDossierAssoc($ue['ID_PRESCRIPTION_DOSSIER']);
				array_push($prescriptionArray, $assoc);
				//Zend_Debug::dump($assoc);				
			}
		}
		$this->view->prescriptionDossier = $prescriptionArray;
		//Zend_Debug::dump($prescriptionArray);
    }
	
	public function prescriptionwordsearchAction()
	{
		if($this->_getParam('motsCles')){
			$tabMotCles = explode(" ", $this->_getParam('motsCles'));
			$dbPrescType = new Model_DbTable_PrescriptionType;
			$listePrescType = $dbPrescType->getPrescriptionTypeByWords($tabMotCles);
			
			$dbPrescAssoc = new Model_DbTable_PrescriptionTypeAssoc;	
			$prescriptionArray = array();			
			foreach($listePrescType as $val => $ue)
			{
				//echo $ue['ID_PRESCRIPTIONTYPE'];
				$assoc = $dbPrescAssoc->getPrescriptionAssoc($ue['ID_PRESCRIPTIONTYPE']);
				//Zend_Debug::dump($assoc);
				//echo "<br/>";
				array_push($prescriptionArray, $assoc);
			}
			//Zend_Debug::dump($prescriptionArray);
			$this->view->prescriptionType = $prescriptionArray;
		}
	}
	
	public function prescriptiontypeformAction()
	{
		$this->showprescriptionTypeAction(0,0,0);
	}
	
	public function prescriptionshowemplacementAction()
	{
		$this->view->categorie = $this->_getParam('PRESCRIPTIONTYPE_CATEGORIE');
		$this->view->texte = $this->_getParam('PRESCRIPTIONTYPE_TEXTE');
		$this->view->article = $this->_getParam('PRESCRIPTIONTYPE_ARTICLE');
		if(!$this->view->categorie && !$this->view->texte && !$this->view->article){
			$this->showprescriptionTypeAction(0,0,0);
		}
		else if(!$this->view->texte && !$this->view->article)
		{
			$this->showprescriptionTypeAction($this->view->categorie,0,0);
		}
		else if(!$this->view->article)
		{
			$this->showprescriptionTypeAction($this->view->categorie,$this->view->texte,0);
		}
		else
		{
			$this->showprescriptionTypeAction($this->view->categorie,$this->view->texte,$this->view->article);
		}
	}
	
	public function showprescriptionTypeAction($categorie,$texte,$article)
	{
		//echo $categorie." ".$texte." ".$article."<br/>";
		$dbPrescType = new Model_DbTable_PrescriptionType;
		$listePrescType = $dbPrescType->getPrescriptionType($categorie,$texte,$article);
		//Zend_Debug::dump($listePrescType);
		
		$dbPrescAssoc = new Model_DbTable_PrescriptionTypeAssoc;		
		$prescriptionArray = array();
		
		foreach($listePrescType as $val => $ue)
		{
			//echo $ue['ID_PRESCRIPTIONTYPE'];
			$assoc = $dbPrescAssoc->getPrescriptionAssoc($ue['ID_PRESCRIPTIONTYPE']);
			//Zend_Debug::dump($assoc);
			//echo "<br/>";
			array_push($prescriptionArray, $assoc);
		}
		
		$this->view->prescriptionType = $prescriptionArray;
		//Zend_Debug::dump($this->view->prescriptionType);
	}
	
	public function prescriptionaddtypeAction()
	{
		$idPrescType = $this->_getParam('idPrescType');
		$idDossier = $this->_getParam('idDossier');
		//on recup le num max de prescription du dossier
		$dbPrescDossier = new Model_DbTable_PrescriptionDossier;
		$numMax = $dbPrescDossier->recupMaxNumPrescDossier($idDossier);
		//Zend_Debug::dump($numMax);
		$num = $numMax['maxnum'];
		if($numMax['maxnum'] == NULL)
		{
			//premiere prescription que l'on ajoute
			$num = 1;
		}else{
			$num++;
		}
		$newPrescDossier = $dbPrescDossier->createRow();
		$newPrescDossier->ID_DOSSIER = $idDossier;
		$newPrescDossier->NUM_PRESCRIPTION_DOSSIER = $num;
		$newPrescDossier->ID_PRESCRIPTION_TYPE = $idPrescType;
		$newPrescDossier->save();
		
		$this->view->idPrescriptionDossier = $newPrescDossier->ID_PRESCRIPTION_DOSSIER;
		
		//On recupere les informations de la prescription type pour l'afficher dans la liste
		$dbPrescTypeAssoc = new Model_DbTable_PrescriptionTypeAssoc;
		$prescType = $dbPrescTypeAssoc->getPrescriptionAssoc($idPrescType);
		//Zend_Debug::dump($prescType);
		$texteArray = array();
		$articleArray = array();
		
		foreach($prescType as $libelle => $value) {
			array_push($articleArray, $value['LIBELLE_ARTICLE']);
			array_push($texteArray, $value['LIBELLE_TEXTE']);
			$this->view->libelle = $value['PRESCRIPTIONTYPE_LIBELLE'];
		}
		
		$this->view->numPresc = $num;
		$this->view->textes = $texteArray;
		$this->view->articles = $articleArray;
		
	}
	
	public function prescriptioneditsaveAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$this->view->edit = 'edit';
		$dbPrescDossier = new Model_DbTable_PrescriptionDossier;
		$dbPrescDossierAssoc = new Model_DbTable_PrescriptionDossierAssoc;
		$dbTexte = new Model_DbTable_PrescriptionTexteListe;
		$dbArticle = new Model_DbTable_PrescriptionArticleListe;
		if( $this->_getParam('do') == 'prescType' )
		{
			//On edite une prescription type: edition de la prescriptionDossier existante et création des prescriptionDossierAssoc
			$prescDossier = $dbPrescDossier->find($this->_getParam('idPrescDossier'))->current();
			$prescDossier->ID_PRESCRIPTION_TYPE = NULL;
			$prescDossier->LIBELLE_PRESCRIPTION_DOSSIER = $this->_getParam('PRESCRIPTIONTYPE_LIBELLE');
			$prescDossier->save();
			
			$idPrescDossier = $prescDossier->ID_PRESCRIPTION_DOSSIER;
			
			//on s'occupe de verifier les textes et articles pour les inserer ou récuperer l'id si besoin puis on insert dans assoc
			$texteArray = array();
			$articleArray = array();
			
			foreach($_POST['article'] as $libelle => $value) {
				array_push($articleArray, $value);
			}
			
			foreach($_POST['texte'] as $libelle => $value) {
				array_push($texteArray, $value);
			}
			
			$numAssoc = 1;
			
			for($i = 0; $i < count($articleArray); $i++)
			{
				//pour chacun des articles et des textes on verifie leurs existance ou non
				if($articleArray[$i] != '')
				{
					$article = $dbArticle->fetchAll("LIBELLE_ARTICLE LIKE '".$articleArray[$i]."'")->toArray();
					//echo count($article);
					//Zend_Debug::dump($article);
					if(count($article) == 0){
						//l'article n'existe pas donc on l'enregistre
						$article = $dbArticle->createRow();
						$article->LIBELLE_ARTICLE = $articleArray[$i];
						$article->save();
						$idArticle = $article->ID_ARTICLE;
					}else if(count($article) == 1){
						//l'article existe donc on récupere son ID
						$idArticle = $article[0]['ID_ARTICLE'];
					}
				}else{
					$idArticle = 1;
				}
				
				if($texteArray[$i] != '')
				{
					$texte = $dbTexte->fetchAll("LIBELLE_TEXTE LIKE '".$texteArray[$i]."'")->toArray();
					if(count($texte) == 0){
						//le texte n'existe pas donc on l'enregistre
						$texte = $dbTexte->createRow();
						$texte->LIBELLE_TEXTE = $texteArray[$i];
						$texte->save();
						$idTexte = $texte->ID_TEXTE;
					}else if(count($texte) == 1){
						//le texte existe donc on récupere son ID
						$idTexte = $texte[0]['ID_TEXTE'];
					}
				}else{
					$idTexte = 1;
				}
				//echo $idTexte." ".$idArticle."<br/>";
				$prescDossierAssoc = $dbPrescDossierAssoc->createRow();
				$prescDossierAssoc->ID_PRESCRIPTION_DOSSIER = $idPrescDossier;
				$prescDossierAssoc->NUM_PRESCRIPTION_DOSSIERASSOC = $numAssoc;
				$prescDossierAssoc->ID_TEXTE = $idTexte;
				$prescDossierAssoc->ID_ARTICLE = $idArticle;
				$prescDossierAssoc->save();
				$idArticle = NULL;
				$idTexte = NULL;
				$numAssoc++;
			}
			$this->view->textes = $texteArray;
			$this->view->articles = $articleArray;
			$this->view->libelle = $this->_getParam('PRESCRIPTIONTYPE_LIBELLE');
			$this->view->numPresc = $this->_getParam('numPresc');
			$this->view->idPrescriptionDossier = $idPrescDossier;
			
			$this->render('prescriptionaddtype');
		}else{
			//On edite une prescriptionDossier suppression des associations et creation des nouvelles
			$idPrescDossier = $this->_getParam('idPrescDossier');
			
			$prescDossier = $dbPrescDossier->find($idPrescDossier)->current();
			$prescDossier->ID_PRESCRIPTION_TYPE = NULL;
			$prescDossier->LIBELLE_PRESCRIPTION_DOSSIER = $this->_getParam('PRESCRIPTIONTYPE_LIBELLE');
			$prescDossier->save();
			//on s'occupe de verifier les textes et articles pour les inserer ou récuperer l'id si besoin puis on insert dans assoc
			$listeAssoc = $dbPrescDossierAssoc->fetchAll("ID_PRESCRIPTION_DOSSIER = " . $this->_getParam('idPrescDossier'))->toArray();
			//on supprime les associations de la prescription en question
			foreach($listeAssoc as $val => $ue)
			{
				$assocToDelete = $dbPrescDossierAssoc->find($idPrescDossier,$ue['NUM_PRESCRIPTION_DOSSIERASSOC'])->current();
				$assocToDelete->delete();
			}
			
			$texteArray = array();
			$articleArray = array();
			
			foreach($_POST['article'] as $libelle => $value) {
				array_push($articleArray, $value);
			}
			
			foreach($_POST['texte'] as $libelle => $value) {
				array_push($texteArray, $value);
			}
			
			$numAssoc = 1;
					
			for($i = 0; $i < count($articleArray); $i++)
			{
				//pour chacun des articles et des textes on verifie leurs existance ou non
				if($articleArray[$i] != '')
				{
					$article = $dbArticle->fetchAll("LIBELLE_ARTICLE LIKE '".$articleArray[$i]."'")->toArray();
					//echo count($article);
					//Zend_Debug::dump($article);
					if(count($article) == 0){
						//l'article n'existe pas donc on l'enregistre
						$article = $dbArticle->createRow();
						$article->LIBELLE_ARTICLE = $articleArray[$i];
						$article->save();
						$idArticle = $article->ID_ARTICLE;
					}else if(count($article) == 1){
						//l'article existe donc on récupere son ID
						$idArticle = $article[0]['ID_ARTICLE'];
					}
				}else{
					$idArticle = 1;
				}
				
				if($texteArray[$i] != '')
				{
					$texte = $dbTexte->fetchAll("LIBELLE_TEXTE LIKE '".$texteArray[$i]."'")->toArray();
					if(count($texte) == 0){
						//le texte n'existe pas donc on l'enregistre
						$texte = $dbTexte->createRow();
						$texte->LIBELLE_TEXTE = $texteArray[$i];
						$texte->save();
						$idTexte = $texte->ID_TEXTE;
					}else if(count($texte) == 1){
						//le texte existe donc on récupere son ID
						$idTexte = $texte[0]['ID_TEXTE'];
					}
				}else{
					$idTexte = 1;
				}
				//echo $idTexte." ".$idArticle."<br/>";
				$prescDossierAssoc = $dbPrescDossierAssoc->createRow();
				$prescDossierAssoc->ID_PRESCRIPTION_DOSSIER = $idPrescDossier;
				$prescDossierAssoc->NUM_PRESCRIPTION_DOSSIERASSOC = $numAssoc;
				$prescDossierAssoc->ID_TEXTE = $idTexte;
				$prescDossierAssoc->ID_ARTICLE = $idArticle;
				$prescDossierAssoc->save();
				$idArticle = NULL;
				$idTexte = NULL;
				$numAssoc++;
			}
			$this->view->textes = $texteArray;
			$this->view->articles = $articleArray;
			$this->view->libelle = $this->_getParam('PRESCRIPTIONTYPE_LIBELLE');
			$this->view->numPresc = $this->_getParam('numPresc');
			$this->view->idPrescriptionDossier = $idPrescDossier;
		
			$this->render('prescriptionaddtype');
		}
	}
	
	public function prescriptionaddAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$dbTexte = new Model_DbTable_PrescriptionTexteListe;
		$dbArticle = new Model_DbTable_PrescriptionArticleListe;
		$dbPrescDossier = new Model_DbTable_PrescriptionDossier;
		$dbPrescDossierAssoc = new Model_DbTable_PrescriptionDossierAssoc;

		$idDossier = $this->_getParam('idDossier');
		//Lorsque l'on crée une prescription spécifique à un dossier	
		$prescDossier = $dbPrescDossier->createRow();
		$prescDossier->ID_DOSSIER = $idDossier;

		//On récupère le num max de prescription du dossier
		$numMax = $dbPrescDossier->recupMaxNumPrescDossier($idDossier);
		$num = $numMax['maxnum'];
		if($numMax['maxnum'] == NULL)
		{
			//premiere prescription que l'on ajoute
			$num = 1;
		}else{
			$num++;
		}		

		$prescDossier->NUM_PRESCRIPTION_DOSSIER = $num;
		$prescDossier->LIBELLE_PRESCRIPTION_DOSSIER = $this->_getParam('PRESCRIPTIONTYPE_LIBELLE');
		$prescDossier->save();

		//on recupere l'id de la prescription que l'on vient d'enregistrer
		$idPrescDossier = $prescDossier->ID_PRESCRIPTION_DOSSIER;
		
		//on s'occupe de verifier les textes et articles pour les inserer ou récuperer l'id si besoin puis on insert dans assoc
		$texteArray = array();
		$articleArray = array();
		
		foreach($_POST['article'] as $libelle => $value) {
			array_push($articleArray, $value);
		}
		
		foreach($_POST['texte'] as $libelle => $value) {
			array_push($texteArray, $value);
		}
		
		$numAssoc = 1;
		
		for($i = 0; $i < count($articleArray); $i++)
		{
			//pour chacun des articles et des textes on verifie leurs existance ou non
			if($articleArray[$i] != '')
			{
				$article = $dbArticle->fetchAll("LIBELLE_ARTICLE LIKE '".$articleArray[$i]."'")->toArray();
				//echo count($article);
				//Zend_Debug::dump($article);
				if(count($article) == 0){
					//l'article n'existe pas donc on l'enregistre
					$article = $dbArticle->createRow();
					$article->LIBELLE_ARTICLE = $articleArray[$i];
					$article->save();
					$idArticle = $article->ID_ARTICLE;
				}else if(count($article) == 1){
					//l'article existe donc on récupere son ID
					$idArticle = $article[0]['ID_ARTICLE'];
				}
			}else{
				$idArticle = 1;
			}
			
			if($texteArray[$i] != '')
			{
				$texte = $dbTexte->fetchAll("LIBELLE_TEXTE LIKE '".$texteArray[$i]."'")->toArray();
				if(count($texte) == 0){
					//le texte n'existe pas donc on l'enregistre
					$texte = $dbTexte->createRow();
					$texte->LIBELLE_TEXTE = $texteArray[$i];
					$texte->save();
					$idTexte = $texte->ID_TEXTE;
				}else if(count($texte) == 1){
					//le texte existe donc on récupere son ID
					$idTexte = $texte[0]['ID_TEXTE'];
				}
			}else{
				$idTexte = 1;
			}
			//echo $idTexte." ".$idArticle."<br/>";
			$prescDossierAssoc = $dbPrescDossierAssoc->createRow();
			$prescDossierAssoc->ID_PRESCRIPTION_DOSSIER = $idPrescDossier;
			$prescDossierAssoc->NUM_PRESCRIPTION_DOSSIERASSOC = $numAssoc;
			$prescDossierAssoc->ID_TEXTE = $idTexte;
			$prescDossierAssoc->ID_ARTICLE = $idArticle;
			$prescDossierAssoc->save();
			$idArticle = NULL;
			$idTexte = NULL;
			$numAssoc++;
		}
		
		//$this->view->idPrescriptionType = $prescType['ID_PRESCRIPTIONTYPE'];
		$this->view->textes = $texteArray;
		$this->view->articles = $articleArray;
		$this->view->libelle = $this->_getParam('PRESCRIPTIONTYPE_LIBELLE');
		$this->view->numPresc = $num;
		$this->view->idPrescriptionDossier = $idPrescDossier;
		
		$this->render('prescriptionaddtype');
	}

	public function prescriptioneditAction()
	{
		//$this->_helper->viewRenderer->setNoRender();
		$idDossier = $this->_getParam('idDossier');
		$dbPrescDossier = new Model_DbTable_PrescriptionDossier;
		$prescDossierEdit = $dbPrescDossier->find($this->_getParam('idPrescDossier'));
		//Zend_Debug::dump($prescDossierEdit->toArray());
		$dbPrescDossierAssoc = new Model_DbTable_PrescriptionDossierAssoc;
		if($prescDossierEdit[0]['ID_PRESCRIPTION_TYPE'] == NULL)
		{
			//echo "Il s'agit d'une prescription ordinaire de dossier on recup l'assoc et on remplit les champs";
			$assoc = $dbPrescDossierAssoc->getPrescriptionDossierAssoc($prescDossierEdit[0]['ID_PRESCRIPTION_DOSSIER']);					
			$this->view->do = 'prescDossier';
		}else{
			//echo "Il s'agit d'un prescription type donc on recup l'assoc et on remplit les champs";
			$assoc = $dbPrescDossierAssoc->getPrescriptionTypeAssoc($prescDossierEdit[0]['ID_PRESCRIPTION_TYPE'],$prescDossierEdit[0]['ID_PRESCRIPTION_DOSSIER']);			
			$this->view->do = 'prescType';
		}
		$this->view->assoc = $assoc;
		$this->view->idDossier = $idDossier;
		$this->view->numPresc = $this->_getParam('numPresc');
		$this->view->idPrescDossier = $this->_getParam('idPrescDossier');
		//Zend_Debug::dump($assoc);
	}

	public function prescriptiondeleteAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		
		$dbPrescDossier = new Model_DbTable_PrescriptionDossier;
		$prescToDelete = $dbPrescDossier->find($this->_getParam('idPrescDossier'))->current();
		$prescToDelete->delete();
		
		$prescriptionDossier = $dbPrescDossier->recupPrescDossier($this->_getParam('idDossier'));
		$num = 1;
		//Zend_Debug::dump($prescriptionDossier);
		foreach($prescriptionDossier as $val => $ue)
		{
			//echo $ue['NUM_PRESCRIPTION_DOSSIER']."<br/>";
			$prescChangePlace = $dbPrescDossier->find($ue['ID_PRESCRIPTION_DOSSIER'])->current();
			$prescChangePlace->NUM_PRESCRIPTION_DOSSIER = $num;
			$prescChangePlace->save();
			$num++;
		}
	}
		
	public function prescriptionchangeposAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$stringUpdate = $this->_getParam('tableUpdate');

		$tabId = explode(",",$stringUpdate);

		$DBprescDossier = new Model_DbTable_PrescriptionDossier;

		$numPresc = 1;
		foreach ($tabId as $idPrescDoss) {
			$updatePrescDossier = $DBprescDossier->find($idPrescDoss)->current();
			$updatePrescDossier->NUM_PRESCRIPTION_DOSSIER = $numPresc;
			$updatePrescDossier->save();
			$numPresc++;
		}
	}
	
}
