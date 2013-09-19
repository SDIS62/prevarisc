<?php

class DossierController extends Zend_Controller_Action
{
    private $id_dossier;

    //liste des champs à afficher en fonction de la nature

    private $listeChamps = array(
    //ETUDES
        //PC - OK
        "1" => array("DATEINSERT","OBJET","NUMDOCURBA","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","SERVICEINSTRUC","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","COORDSSI","DATESDIS","PREVENTIONNISTE","DEMANDEUR"),
        //AT - OK
        "2" => array("DATEINSERT","OBJET","NUMDOCURBA","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","SERVICEINSTRUC","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","COORDSSI","DATESDIS","PREVENTIONNISTE","DEMANDEUR"),
        //Dérogation - OK
        "3" => array("DATEINSERT","OBJET","NUMDOCURBA","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","SERVICEINSTRUC","COMMISSION","DESCGEN","JUSTIFDEROG","MESURESCOMPENS","MESURESCOMPLE","DESCEFF","DATECOMM","AVIS","COORDSSI","DATESDIS","PREVENTIONNISTE","DEMANDEUR","REGLEDEROG"),
        //Cahier des charges fonctionnel du SSI - OK
        "4" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DATECOMM","AVIS","COORDSSI","DATESDIS","PREVENTIONNISTE","DEMANDEUR"),
        //Cahier des charges de type T - OK
        "5" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR"),
        //Salon type T - OK
        "6" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","CHARGESEC","PREVENTIONNISTE","DEMANDEUR"),
        //RVRMD (diag sécu) - OK
        "7" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR"),
        //Documents divers - OK
        "8" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","DATEPREF","DATEREP","PREVENTIONNISTE","DEMANDEUR"),
        //Changement de DUS - OK
        "9" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR"),
        //Suivi organisme formation SSIAP - OK
        "10" => array("DATEINSERT","OBJET","NUMCHRONO","AVIS","DATESDIS","DATEPREF","DATEREP","PREVENTIONNISTE","DEMANDEUR"),
        //Demande de registre de sécurité CTS - OK
        "11" => array("DATEINSERT","OBJET","NUMCHRONO","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","DATEPREF","PREVENTIONNISTE","DEMANDEUR"),
        //Demande d'implantation CTS <6mois - OK
        "12" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR"),
        //Déclaration préalable - OK
        "13" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","DESCGEN","DESCEFF","DATESDIS","PREVENTIONNISTE","DEMANDEUR"),
        //Permis d'aménager - OK
        "14" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","DESCGEN","DESCEFF","DATESDIS","PREVENTIONNISTE","DEMANDEUR"),
        //Permis de démolir - OK
        "15" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR"),
        //CR de visite des organismes d'ins.... - OK
        "16" => array("DATEINSERT","OBJET","NUMCHRONO","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","DATEPREF","PREVENTIONNISTE","DEMANDEUR"),
        //Etude suite a un avis ne se prononce pas - OK MAIS VOIR POUR PARTICULARITé TABLEAU
        "17" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR"),
        //Utilisation exceptionnelle de locaux - OK
        "18" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR"),
        //Levée de réserves - OK
        "19" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR"),
        //Echéncier de travaux - OK
        "46" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR"),
    //VISITE DE COMMISSION
        //Réception de travaux - OK
        "20" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATEVISITE","DATECOMM","AVIS","COORDSSI","PREVENTIONNISTE","DEMANDEUR"),
		//Avant ouverture - OK
        "47" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATEVISITE","DATECOMM","AVIS","COORDSSI","PREVENTIONNISTE","DEMANDEUR"),
        //Périodique - OK
        "21" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATEVISITE","DATECOMM","AVIS","PREVENTIONNISTE","DEMANDEUR"),
        //Chantier - OK
        "22" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATEVISITE","DATECOMM","AVIS","COORDSSI","PREVENTIONNISTE","DEMANDEUR"),
        //Controle - OK
        "23" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATEVISITE","DATECOMM","AVIS","COORDSSI","PREVENTIONNISTE","DEMANDEUR"),
        //Inopinéee - OK
        "24" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATEVISITE","DATECOMM","AVIS","PREVENTIONNISTE","DEMANDEUR"),
    //GROUPE DE VISITE
        //Réception de travaux - OK
        "25" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATECOMM","DATEVISITE","AVIS","COORDSSI","PREVENTIONNISTE","DEMANDEUR"),
		//Avant ouverture - OK
        "48" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATECOMM","DATEVISITE","AVIS","COORDSSI","PREVENTIONNISTE","DEMANDEUR"),
        //Périodique - OK
        "26" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATECOMM","DATEVISITE","AVIS","PREVENTIONNISTE","DEMANDEUR"),
        //Chantier - OK
        "27" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATECOMM","DATEVISITE","AVIS","COORDSSI","PREVENTIONNISTE","DEMANDEUR"),
        //Controle - OK
        "28" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATECOMM","DATEVISITE","AVIS","COORDSSI","PREVENTIONNISTE","DEMANDEUR"),
        //Inopinéee - OK
        "29" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATECOMM","DATEVISITE","AVIS","PREVENTIONNISTE","DEMANDEUR"),
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
        "37" => array("DATEINSERT","OBJET","OPERSDIS","RCCI","REX","NUMINTERV","DATEINTERV","DUREEINTERV","PREVENTIONNISTE","DEMANDEUR"),
        //SAP - OK
        "38" => array("DATEINSERT","OBJET","OPERSDIS","RCCI","REX","NUMINTERV","DATEINTERV","DUREEINTERV","PREVENTIONNISTE","DEMANDEUR"),
        //Intervention div - OK
        "39" => array("DATEINSERT","OBJET","OPERSDIS","RCCI","REX","NUMINTERV","DATEINTERV","DUREEINTERV","PREVENTIONNISTE","DEMANDEUR"),
    //ARRETE
        //Ouverture - OK
        "40" => array("DATEINSERT","OBJET","DATESIGN","PREVENTIONNISTE","DEMANDEUR"),
        //Fermeture - OK
        "41" => array("DATEINSERT","OBJET","DATESIGN","PREVENTIONNISTE","DEMANDEUR"),
        //Mise en demeure - OK
        "42" => array("DATEINSERT","OBJET","DATESIGN","PREVENTIONNISTE","DEMANDEUR"),
        //Mise en demeure de l'exploitant - OK
        "43" => array("DATEINSERT","OBJET","DATESIGN","PREVENTIONNISTE","DEMANDEUR"),
        //GN6 - OK
        "44" => array("DATEINSERT","OBJET","DATESIGN","PREVENTIONNISTE","DEMANDEUR"),
        //Courrier - OK
        "45" => array("DATEINSERT","OBJET","DATESIGN","PREVENTIONNISTE","DEMANDEUR"),
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

            if($this->_helper->Droits()->checkDossier($id_dossier))
                $this->_helper->Droits()->redirect();

            //Si on à l'id d'un dossier, on récupére tous les établissements liés à ce dossier
            $DBdossier = new Model_DbTable_Dossier;
            $dossier = $DBdossier->find($id_dossier)->current();

            $DBdossierType = new Model_DbTable_DossierType;
            $libelleType = $DBdossierType->find($dossier->TYPE_DOSSIER)->current();

            //Zend_Debug::dump($libelleType);
            $this->view->objetDossier = $dossier->OBJET_DOSSIER;
            $this->view->libelleType = $libelleType['LIBELLE_DOSSIERTYPE'];

            $this->view->idDossier = ($this->_getParam("id"));
        } else {

            if ($this->_helper->Droits()->get()->DROITDOSSCREATION_GROUPE != 1) {

                $this->_helper->Droits()->redirect();
            }
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
        //On récupère tous les types de dossier
        $DBdossierType = new Model_DbTable_DossierType;
        $this->view->dossierType = $DBdossierType ->fetchAll();
        //Récupération de la liste des avis pour la génération du select
        $DBlisteAvis = new Model_DbTable_Avis;
        $this->view->listeAvis = $DBlisteAvis->getAvis();
        //Zend_Debug::dump($this->view->listeAvis);
        if ($this->_getParam("idEtablissement")) {
            $this->view->idEtablissement = $this->_getParam("idEtablissement");
        }
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
		
        if ((int) $this->_getParam("id")) {
            //Cas d'affichage des infos d'un dossier existant
            $this->view->do = 'edit';
            //On récupère l'id du dossier
            $idDossier = (int) $this->_getParam("id");
            $this->view->idDossier = $idDossier;
            //Récupération de tous les champs de la table dossier
            $DBdossier = new Model_DbTable_Dossier;
            $this->view->infosDossier = $DBdossier->find($idDossier)->current();
            //Zend_Debug::dump($this->view->infosDossier->toArray());

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
                if ($this->view->infosDossier["AVIS_DOSSIER"] != '') {
                    $this->view->AVIS_VALUE = $DBlisteAvis->getAvisLibelle($this->view->infosDossier["AVIS_DOSSIER"]);
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
            } elseif ($this->view->infosDossier['TYPE_DOSSIER'] == 2) {
                // CAS D'UNE VISITE
                //echo "dossier de type 2".count($listeDateAffectDossier);
                $nbDateExist = count($listeDateAffectDossier);
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
                    //Zend_Debug::dump($this->view->etablissement);
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
            case "addPrescription":
                //ici insertion des valeurs dans la bd puis affichage au dessus du menu d'ajout des prescriptions
                $DBprescAssoc = new Model_DbTable_PrescriptionAssoc;
                $newPrescription = $DBprescAssoc->createRow();

                $listeTexte = "";
                $listeArticle = "";

                $texteArray = array();
                $articleArray = array();

                $DBtexte = new Model_DbTable_PrescriptionTexte;
                $DBarticle = new Model_DbTable_PrescriptionArticle;

                //On boucle sur les hidden POST -> text et on vérifie aussi les articles
                foreach ($_POST['texte'] as $libelle => $valueTexte) {
                    if ($valueTexte == "") {
                        //si vide
                        $listeTexte .= "_";
                        $valueTexte = '';
                    }else	if (is_numeric($valueTexte)) {
                        //si il s'agit d'un id
                        $listeTexte .= $valueTexte."_";
                        $libelleTexte = $DBtexte->find($valueTexte)->current();
                        $valueTexte = $libelleTexte->LIBELLE_TEXTE;
                    } else {
                        //Si c'est un nouveau -> inserer texte et récup id
                        $idExistant = $DBtexte->verifTexteExiste($valueTexte);
                        $idExistant = $idExistant['ID_TEXTE'];
                        if ($idExistant == false) {
                            $newTexte = $DBtexte->createRow();
                            $newTexte->LIBELLE_TEXTE = $valueTexte;
                            $newTexte->save();
                            $listeTexte .= $newTexte->ID_TEXTE."_";
                        } else {
                            $listeTexte .= $idExistant."_";
                        }
                    }

                    if ($_POST['article'][$libelle] == "") {
                        //si vide
                        $listeArticle .= "_";
                        $valueArticle = '';
                    } elseif (is_numeric($_POST['article'][$libelle])) {
                        //si il s'agit d'un id
                        $listeArticle .= $_POST['article'][$libelle]."_";
                        $libelleArticle = $DBarticle->find($_POST['article'][$libelle])->current();
                        $valueArticle = $libelleArticle->LIBELLE_ARTICLE;
                    } else {
                        //Si c'est un nouveau -> inserer texte et récup id
                        $idExistant = $DBarticle->verifArticleExiste($_POST['article'][$libelle]);
                        $idExistant = $idExistant['ID_ARTICLE'];
                        if ($idExistant == false) {
                            $newArticle = $DBarticle->createRow();
                            $newArticle->LIBELLE_ARTICLE = $_POST['article'][$libelle];
                            $valueArticle = $_POST['article'][$libelle];
                            $newArticle->save();
                            $listeArticle .= $newArticle->ID_ARTICLE."_";
                        } else {
                            $valueArticle = $_POST['article'][$libelle];
                            $listeArticle .= $idExistant."_";
                        }
                    }

                    array_push($texteArray, $valueTexte);
                    array_push($articleArray, $valueArticle);
                }
                //on envoi à la vue l'affichage des texte et des articles pour affichage
                //$this->view->listeTexte = $texteArray;
                //$this->view->listeArticle = $articleArray;

                $DBprescLibelle = new Model_DbTable_PrescriptionLibelle;
                $newPrescLibelle = $DBprescLibelle->createRow();
                $newPrescLibelle->LIBELLE_PRESCRIPTIONLIBELLE = $this->_getParam('PRESCRIPTIONLIBELLE');
                $newPrescLibelle->save();

                //on envoie à la vue la le libelle de la prescription pour affichage
                $prescriptionLibelle = $this->_getParam('PRESCRIPTIONLIBELLE');

                if ( !$this->_getParam('prescType') ) {
                    //dans le cas ou il ne sagit pas d'une prescription type
                    $DBprescAssoc = new Model_DbTable_PrescriptionAssoc;
                    $newPrescAssoc = $DBprescAssoc->createRow();
                    $newPrescAssoc->TEXTE_PRESCRIPTIONASSOC = $listeTexte;
                    $newPrescAssoc->ARTICLE_PRESCRIPTIONASSOC = $listeArticle;
                    $newPrescAssoc->LIBELLE_PRESCRIPTIONASSOC = $newPrescLibelle->ID_PRESCRIPTIONLIBELLE;
                    $newPrescAssoc->save();
                    $idPrescAssoc = $newPrescAssoc->ID_PRESCRIPTIONASSOC;
                } else {
                    //dans le cas ou il sagit d'une prescription type
                    $DBprescType = new Model_DbTable_PrescriptionType;
                    $newPrescType = $DBprescType->createRow();
                    $newPrescType->TEXTE_PRESCRIPTIONTYPE = $listeTexte;
                    $newPrescType->ARTICLE_PRESCRIPTIONTYPE = $listeArticle;
                    $newPrescType->LIBELLE_PRESCRIPTIONTYPE = $newPrescLibelle->ID_PRESCRIPTIONLIBELLE;
                    $newPrescType->ABREVIATION_PRESCRIPTIONTYPE = $this->_getParam('abreviation');
                    $newPrescType->save();
                    $idPrescAssoc = $newPrescType->ID_PRESCRIPTIONTYPE;
                }

                $DBprescDossier = new Model_DbTable_PrescriptionDossier;
                $newPrescDossier = $DBprescDossier->createRow();
                $newPrescDossier->IDDOSSIER_PRESCRIPTIONDOSSIER = $this->_getParam('idDossier');
                $newPrescDossier->NUM_PRESCRIPTIONDOSSIER = $this->_getParam('numPresc');
                $newPrescDossier->PRESCRIPTIONASSOC_PRESCRIPTIONDOSSIER = $idPrescAssoc;
                if ( $this->_getParam('prescType') ) {
                    $newPrescDossier->TYPE_PRESCRIPTIONDOSSIER = 1;
                }
                $newPrescDossier->save();
                $idPrescriptionDossier = $newPrescDossier->ID_PRESCRIPTIONDOSSIER;
                $this->view->numPresc = $this->_getParam('numPresc');
                $this->showprescriptionAction($texteArray,$articleArray,$prescriptionLibelle,$idPrescriptionDossier,"addPrescAssoc");
            break;
			case "motiveAvisDefPresc":
				//Permet de distinguer les prescriptions qui motivent un avis défavorable sur le dossier
				$dbPrecDoss = new Model_DbTable_PrescriptionDossier;
				$prescDossEdit = $dbPrecDoss->find($this->_getParam('idPrescMAD'))->current();
				if($this->_getParam('checked') == 'true'){
					$prescDossEdit->MAD_PRESCRIPTIONDOSSIER = 1;
				}else if($this->_getParam('checked') == 'false'){
					$prescDossEdit->MAD_PRESCRIPTIONDOSSIER = 0;
				}
				
				//On passe l'avis du dossier sur défavorable (idAvis = 3)
				$dbDossier = new Model_DbTable_Dossier;
				$dossier = $dbDossier->find($this->_getParam('idDossier'))->current();
				$avisActuel = $dossier->AVIS_DOSSIER;
				$dossier->AVIS_DOSSIER = 3;
								
				if($avisActuel != 3)
					$dossier->save();
				
				$prescDossEdit->save();				
            break;
            case "verifAbreviation":
                $DBprescType = new Model_DbTable_PrescriptionType;
                $abreviationExist = $DBprescType->searchIfAbreviationExist($this->_getParam('abreviation'));
                echo $abreviationExist['COUNT(*)'];
            break;
            case "affichePrescType":
                //Permet d'afficher une prescription type que l'on à recherché via son abréviation
                $DBprescType = new Model_DbTable_PrescriptionType;
                $infosPrescriptionType = $DBprescType->find($this->_getParam('idPresc'))->current();

                $listeTextes = array();
                $listeArticles = array();

                $tabTexte = explode("_",$infosPrescriptionType->TEXTE_PRESCRIPTIONTYPE);
                $dbtexte = new Model_DbTable_PrescriptionTexte;
                foreach ($tabTexte as $indText => $valText) {
                    $texte = $dbtexte->find($valText)->current();
                    array_push($listeTextes, $texte['LIBELLE_TEXTE']);
                }

                $tabArticle = explode("_",$infosPrescriptionType->ARTICLE_PRESCRIPTIONTYPE);
                $dbarticle = new Model_DbTable_PrescriptionArticle;
                foreach ($tabArticle as $indArticle => $valArticle) {
                    $article = $dbarticle->find($valArticle)->current();
                    array_push($listeArticles, $article['LIBELLE_ARTICLE']);
                }

                $idPrescriptionLibelle = $infosPrescriptionType->LIBELLE_PRESCRIPTIONTYPE;

                $DBprescLibelle = new Model_DbTable_PrescriptionLibelle;
                $prescriptionLibelle = $DBprescLibelle->find($idPrescriptionLibelle)->current();

                $this->showprescriptionAction($listeTextes,$listeArticles,$prescriptionLibelle['LIBELLE_PRESCRIPTIONLIBELLE'],"","addPrescType");
            break;
            case "addPrescType":
                $DBprescDossier = new Model_DbTable_PrescriptionDossier;
                $newPrescDossier = $DBprescDossier->createRow();
                $newPrescDossier->IDDOSSIER_PRESCRIPTIONDOSSIER = $this->_getParam('idDossier');
                $newPrescDossier->NUM_PRESCRIPTIONDOSSIER = $this->_getParam('numPresc');
                $newPrescDossier->PRESCRIPTIONASSOC_PRESCRIPTIONDOSSIER = $this->_getParam('presAssoc');
                $newPrescDossier->TYPE_PRESCRIPTIONDOSSIER = 1;
                $newPrescDossier->save();
                $this->view->numPresc = $this->_getParam('numPresc');
                echo $newPrescDossier->ID_PRESCRIPTIONDOSSIER;
            break;
            case "changePrescOrder":
                $stringUpdate = $this->_getParam('tableUpdate');

                $tabId = explode(",",$stringUpdate);

                $DBprescDossier = new Model_DbTable_PrescriptionDossier;

                $numPresc = 1;
                foreach ($tabId as $idPrescDoss) {
                    $updatePrescDossier = $DBprescDossier->find($idPrescDoss)->current();
                    $updatePrescDossier->NUM_PRESCRIPTIONDOSSIER = $numPresc;
                    $updatePrescDossier->save();
                    $numPresc++;
                }
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
                $listeEtab = split("-", $this->_getParam('idListeEtab'));

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
        }
    }

    //Permet de faire les insertions de dossier en base de données et de rediriger vers le dossier/index/id/X => X = id du dossier qui vient d'être crée
    public function saveAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $DBdossier = new Model_DbTable_Dossier;

        if ($this->_getParam('do') == 'new') {
            $nouveauDossier = $DBdossier->createRow();
        } elseif ($this->_getParam('do') == 'edit') {
            $nouveauDossier = $DBdossier->find($this->_getParam('idDossier'))->current();
        }

        foreach ($_POST as $libelle => $value) {
            //On exclu la lecture de selectNature => select avec les natures;
            //NUM_DOCURB => input text pour la saisie des doc urba; docUrba & natureId => interpreté après;
            //  !  \\
            //Il  faudrait idéalement: Récuperer le tableau concernant les champs à faire afficher et si : le champ passé n'y apparait pas le mettre à null
            //
            if ($libelle != "DATEVISITE_PERIODIQUE" && $libelle != "selectNature" && $libelle != "NUM_DOCURBA" && $libelle != "natureId" && $libelle != "docUrba" && $libelle != 'do' && $libelle != 'idDossier' && $libelle != 'HEUREINTERV_DOSSIER' && $libelle != 'idEtablissement' && $libelle != 'ID_AFFECTATION_DOSSIER_VISITE' && $libelle != 'ID_AFFECTATION_DOSSIER_COMMISSION' && $libelle != "preventionniste" && $libelle != "commissionSelect") {
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

                if ($libelle == 'AVIS_DOSSIER' && $value == 0) {
                    $value = NULL;
                }


                if($value == '')
                    $value = NULL;
                //echo $libelle." - ".$value."<br/>";

                $nouveauDossier->$libelle = $value;

            }
        }




        $nouveauDossier->save();

        $idDossier = $nouveauDossier->ID_DOSSIER;

        if ($this->_getParam('do') == 'new') {
            if ( isset( $_POST['idEtablissement'] ) &&  $_POST['idEtablissement'] != 0 ) {
                $DBetablissementDossier = new Model_DbTable_EtablissementDossier;
                $saveEtabDossier = $DBetablissementDossier->createRow();
                $saveEtabDossier->ID_ETABLISSEMENT = $this->_getParam('idEtablissement');
                $saveEtabDossier->ID_DOSSIER = $idDossier;
                $saveEtabDossier->save();
            }

            //Sauvegarde des natures du dossier
            /*
            foreach ($_POST['natureId'] as $libelle => $value) {
                    $saveNature = $DBdossierNature->createRow();
                    $saveNature->ID_DOSSIER = $idDossier;
                    $saveNature->ID_NATURE = $value;
                    $saveNature->save();
                    if ($value == 21) {
                        //VISITE PERIODIQUE
                        //Dans le cas d'une visite périodique on renseigne le champ DATEVISITE_DOSSIER pour pouvoir calculer la périodicité suviante
                        if ($_POST['DATEVISITE_PERIODIQUE']) {
                            $datePeriodique = explode("/",$_POST['DATEVISITE_PERIODIQUE']);
                            $dateToSql = $datePeriodique[2]."-".$datePeriodique[1]."-".$datePeriodique[0];
                            $nouveauDossier->DATEVISITE_DOSSIER = $dateToSql;
                            $nouveauDossier->save();
                        }
                    }
            }
            */
            $DBdossierNature = new Model_DbTable_DossierNature;

            $saveNature = $DBdossierNature->createRow();
            $saveNature->ID_DOSSIER = $idDossier;
            $saveNature->ID_NATURE = $_POST['selectNature'];
            $saveNature->save();


            if ($this->_getParam("selectNature") == 21 && $this->_getParam("TYPE_DOSSIER") == 2) {
                //VISITE PERIODIQUE
                //Dans le cas d'une visite périodique on renseigne le champ DATEVISITE_DOSSIER pour pouvoir calculer la périodicité suviante
                if ($_POST['DATEVISITE_PERIODIQUE']) {
                    $datePeriodique = explode("/",$_POST['DATEVISITE_PERIODIQUE']);
                    $dateToSql = $datePeriodique[2]."-".$datePeriodique[1]."-".$datePeriodique[0];
                    $nouveauDossier->DATEVISITE_DOSSIER = $dateToSql;
                    $nouveauDossier->save();
                }
            }



            //Sauvegarde des numéro de document d'urbanisme du dossier
            $DBdossierDocUrba = new Model_DbTable_DossierDocUrba;
            if (isset($_POST['docUrba'])) {
                foreach ($_POST['docUrba']  as $libelle => $value) {
                    $saveDocUrba = $DBdossierDocUrba->createRow();
                    $saveDocUrba->ID_DOSSIER = $idDossier;
                    $saveDocUrba->NUM_DOCURBA = $value;
                    $saveDocUrba->save();
                }
            }

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


        //Sauvegarde des préventionnistes
        $DBdossierPrev = new Model_DbTable_DossierPreventionniste;
        $DBdossierPrev->delete("ID_DOSSIER = " .  $idDossier);
        if (isset($_POST['preventionniste'])) {
            foreach ($_POST['preventionniste'] as $prev => $infos) {
                $savePrev = $DBdossierPrev->createRow();
                $savePrev->ID_DOSSIER = $idDossier;
                $savePrev->ID_PREVENTIONNISTE = $infos;
                $savePrev->save();
            }
        }
	/*
		else if ($this->_getParam('do') == 'edit') {

            $dbDossierAffectation = new Model_DbTable_DossierAffectation;
            $affectation = $dbDossierAffectation->find(NULL,$nouveauDossier->ID_DOSSIER)->current();
            $affectation->ID_DATECOMMISSION_AFFECT = $this->_getParam("ID_AFFECTATION_DOSSIER");
            $affectation->save();

        }
	*/
	
        //echo $idDossier;
        //Sauvegarde des informations concernant l'affectation d'un dossier à une commission

        //$affectation = $dbDossierAffectation->find(NULL,$idDossier)->current();

        //if(count($affectation) == 0)
		//echo $this->_getParam('ID_AFFECTATION_DOSSIER_VISITE')+" - "+$this->_getParam('ID_AFFECTATION_DOSSIER_COMMISSION');

        $dbDossierAffectation = new Model_DbTable_DossierAffectation;
        if ($this->_getParam('COMMISSION_DOSSIER') == '') {
            $dbDossierAffectation->deleteDateDossierAffect($idDossier);
			
        } else {
            
			$dbDossierAffectation->deleteDateDossierAffect($idDossier);
            $affectation = $dbDossierAffectation->createRow();
            if ($this->_getParam('ID_AFFECTATION_DOSSIER_VISITE') && $this->_getParam('ID_AFFECTATION_DOSSIER_VISITE') != '') {
                $affectation->ID_DATECOMMISSION_AFFECT = $this->_getParam('ID_AFFECTATION_DOSSIER_VISITE');
                $affectation->ID_DOSSIER_AFFECT = $idDossier;
                $affectation->save();
            }

            $affectation = $dbDossierAffectation->createRow();
            if ($this->_getParam('ID_AFFECTATION_DOSSIER_COMMISSION') && $this->_getParam('ID_AFFECTATION_DOSSIER_COMMISSION') != '') {
                $affectation->ID_DATECOMMISSION_AFFECT = $this->_getParam('ID_AFFECTATION_DOSSIER_COMMISSION');
                $affectation->ID_DOSSIER_AFFECT = $idDossier;
                $affectation->save();
            }
        }

	/*
        if ($this->_getParam('ID_AFFECTATION_DOSSIER') && $this->_getParam('ID_AFFECTATION_DOSSIER') != '') {
            $affectation->ID_DATECOMMISSION_AFFECT = $this->_getParam('ID_AFFECTATION_DOSSIER');
            $affectation->ID_DOSSIER_AFFECT = $idDossier;
            $affectation->save();
        } else {
            $affectation->delete();
        }

	*/
        //on envoi l'id à la vue pour qu'elle puisse rediriger vers la bonne page
        echo $idDossier;
    }

    public function prescriptionAction()
    {
    }

    public function prescriptionloadajaxAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $dblistepresc = new Model_DbTable_PrescriptionDossier;
        $dbtexte = new Model_DbTable_PrescriptionTexte;
        $dbarticle = new Model_DbTable_PrescriptionArticle;

        $this->view->listePrescription = $dblistepresc->getListePrescription($this->_getParam('idDossier'));

        $compteur = 1;

        //Arrays contenant toutes les infos sur les différentes prescriptions qui seront envoyées à la vue
        $Textes = array();
        $Articles = array();
        $PrescriptionsLibelle = array();

        //Array temporaire d'une prescription qui récolte les infos de chaque description 1 par 1 puis vient s'inclure aux arrays décrits au dessus
        //On initialise pour le premier
        $listeTextePresc = array();
        $listeArticlePresc = array();

        foreach ($this->view->listePrescription as $indPresc => $val) {
            //On liste les prescriptions
            $numPrescription = $val['NUM_PRESCRIPTIONDOSSIER'];

            if ($val['TYPE_PRESCRIPTIONDOSSIER'] == 0) {
                //la prescription se trouve dans prescription ASSOC
                $infosPrescription = $dblistepresc->getPrescriptionAssoc($val['PRESCRIPTIONASSOC_PRESCRIPTIONDOSSIER']);
            } elseif ($val['TYPE_PRESCRIPTIONDOSSIER'] == 1) {
                //la prescription se trouve dans prescription TYPE
                $infosPrescription = $dblistepresc->getPrescriptionType($val['PRESCRIPTIONASSOC_PRESCRIPTIONDOSSIER']);
            }
            //Zend_Debug::dump($infosPrescription);

            if ($compteur != $numPrescription) {
                //On réinitialise les tableau après les avoir ajoutés au tableau contenant toute les infos
                array_push($Textes, $listeTextePresc);
                array_push($Articles, $listeArticlePresc);
                $compteur ++;
                $listeTextePresc = array();
                $listeArticlePresc = array();
            }

            if ($val['TYPE_PRESCRIPTIONDOSSIER'] == 0) {
                $tabTexte = explode("_",$infosPrescription['TEXTE_PRESCRIPTIONASSOC']);
            } else {
                $tabTexte = explode("_",$infosPrescription['TEXTE_PRESCRIPTIONTYPE']);
            }
            foreach ($tabTexte as $indText => $valText) {
                //echo $valText."<br/>";
                $texte = $dbtexte->find($valText)->current();
                //echo $texte['LIBELLE_TEXTE'];
                array_push($listeTextePresc, $texte['LIBELLE_TEXTE'] );
            }

            if ($val['TYPE_PRESCRIPTIONDOSSIER'] == 0) {
                $tabArticle = explode("_",$infosPrescription['ARTICLE_PRESCRIPTIONASSOC']);
            } else {
                $tabArticle = explode("_",$infosPrescription['ARTICLE_PRESCRIPTIONTYPE']);
            }
            foreach ($tabArticle as $indArticle => $valArticle) {
                //echo $valArticle."<br/>";
                $article = $dbarticle->find($valArticle)->current();
                //echo $article['LIBELLE_ARTICLE'];
                array_push($listeArticlePresc, $article['LIBELLE_ARTICLE'] );
            }
            array_push($PrescriptionsLibelle, $infosPrescription['LIBELLE_PRESCRIPTIONLIBELLE']);
        }
        array_push($Textes, $listeTextePresc);
        array_push($Articles, $listeArticlePresc);

        $this->view->nbPrescription = count($this->view->listePrescription);
        $this->view->listeTextes = $Textes;
        //Zend_Debug::dump($this->view->listeTextes);
        $this->view->listeArticles = $Articles;
        //Zend_Debug::dump($this->view->listeArticles);
        $this->view->ListePrescriptionsLibelle = $PrescriptionsLibelle;
        //Zend_Debug::dump($this->view->ListePrescriptionsLibelle);

        $this->showprescriptionAction($Textes,$Articles,$PrescriptionsLibelle," ","loadPrescriptionAjax");
    }

    public function showprescriptionAction($tabTexte,$tabArticle,$tabLibelle,$idPrescDossier,$type)
    {
        $this->view->listeTextes = $tabTexte;
        $this->view->listeArticles = $tabArticle;
        $this->view->listeLibelles = $tabLibelle;
        $this->view->idPrescDossier = $idPrescDossier;
        $this->view->type = $type;

        $this->render('showprescription');
    }

    public function editprescriptionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        //echo $this->_getParam('idDossier')." - ".$this->_getParam('idPrescription');
        $idPrescription = (int) $this->_getParam('idPrescription');

        $dbPrescDossier = new Model_DbTable_PrescriptionDossier;
        //On récupere les infos de la prescriptions permettant de savoir s'il sagit d'une prescription type ou non
        $prescription = $dbPrescDossier->find($idPrescription)->current()->toArray();

        // Zend_Debug::dump($prescription);
        //Récupération des infos concernant l'association texte/article/libelle
        if ($prescription["TYPE_PRESCRIPTIONDOSSIER"] == 0) {
            //prescription ordinaire
            //echo "il s'agit d'une prescription ordinaire";
            $infosPrescription = $dbPrescDossier->getPrescriptionAssoc($prescription["PRESCRIPTIONASSOC_PRESCRIPTIONDOSSIER"]);
        } elseif ($prescription["TYPE_PRESCRIPTIONDOSSIER"] == 1) {
            //prescription type
            //echo "il s'agit d'une prescription type";
            $infosPrescription = $dbPrescDossier->getPrescriptionType($prescription["PRESCRIPTIONASSOC_PRESCRIPTIONDOSSIER"]);
        }
        //Zend_Debug::dump($infosPrescription);

        echo json_encode($infosPrescription);

    }

    public function editprescriptiontexteAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        //echo $this->_getParam('prescriptionTexte')."<br/>";
        //On récupere le srting contenant tous les id des textes
        $texteIdListe = $this->_getParam('prescriptionTexte');
        //On place chacun des id dans un tableau
        $texteTab = explode("_", $texteIdListe);
        //supprimer le dernier élément du tableau car il est inutile
        array_pop($texteTab);
        //Pour chacun des tableaux on récupère le texte concerné
        //Zend_Debug::dump($texteTab);
        $listeTextes = array();
        $dbPrescTexte = new Model_DbTable_PrescriptionTexte;
        $cpt = 0;
        foreach ($texteTab as $index => $idTexte) {
            if ($idTexte) {
                $texte = $dbPrescTexte->find($idTexte)->current();
                array_push($listeTextes,array($texte->ID_TEXTE,$texte->LIBELLE_TEXTE));
            } else {
                array_push($listeTextes,array("",""));
            }
            $cpt++;
        }
        echo json_encode($listeTextes);
    }

    public function editprescriptionarticleAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        //echo $this->_getParam('prescriptionArticle')."<br/>";
        $articleIdListe = $this->_getParam('prescriptionArticle');
        //On place chacun des id dans un tableau
        $articleTab = explode("_", $articleIdListe);
        //supprimer le dernier élément du tableau car il est inutile
        array_pop($articleTab);
        //Pour chacun des tableaux on récupère le texte concerné
        //Zend_Debug::dump($texteTab);
        $listeArticles = array();
        //On récupere le srting contenant tous les id des textes
        $dbPrescArticle = new Model_DbTable_PrescriptionArticle;
        $cpt = 0;
        foreach ($articleTab as $index => $idArticle) {
            if ($idArticle) {
                $article = $dbPrescArticle->find($idArticle)->current();
                array_push($listeArticles,array($article->ID_ARTICLE,$article->LIBELLE_ARTICLE));
            } else {
                array_push($listeArticles,array("",""));
            }
            $cpt++;
        }
        echo json_encode($listeArticles);

    }

    public function editprescriptionvalidationAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $idPrescription = (int) $this->_getParam('prescriptionEdit');

        $dbPrescDossier = new Model_DbTable_PrescriptionDossier;
        //On récupere les infos de la prescriptions permettant de savoir s'il sagit d'une prescription type ou non
        $prescription = $dbPrescDossier->find($idPrescription)->current()->toArray();

        //Récupération des infos concernant l'association texte/article/libelle
        if ($prescription["TYPE_PRESCRIPTIONDOSSIER"] == 0) {
            //prescription ordinaire
            //On change dans precriptionassoc les textes et articles ainsi que le libelle (mais on supprime le précédent)
            echo "il s'agit d'une prescription ordinaire";
            //On récupere la prescription associée (article texte libelle)
            $dbPrescAssoc = new Model_DbTable_PrescriptionAssoc;
            $assocPresc = $dbPrescAssoc->find($prescription["PRESCRIPTIONASSOC_PRESCRIPTIONDOSSIER"])->current();
            //On suprime le libelle associé
            $dbPrescLibelle = new Model_DbTable_PrescriptionLibelle;
            $libelleToDelete = $dbPrescLibelle->find($assocPresc['LIBELLE_PRESCRIPTIONASSOC'])->current();
            $libelleToDelete->delete();
            //Puis on supprime l'association pour la créer à nouveau
            $assocPresc->delete();
            $prescription = $dbPrescDossier->find($idPrescription)->current();
            $prescription->delete();

        } elseif ($prescription["TYPE_PRESCRIPTIONDOSSIER"] == 1) {
            //prescription type
            //On change dans prescriptiondossier on supprime la ligne et on la recrée en gardant juste le numéro de la prescription
            echo "il s'agit d'une prescription type";

            $prescription = $dbPrescDossier->find($idPrescription)->current();
            $prescription->delete();


        }
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
            } else {
                //cas d'une etude
                $listeDocConsulte[$nature["ID_NATURE"]] = $dblistedoc->getDocEtude();
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

    //gestion des documents consultés
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

    public function dialoggenrapportAction()
    {
        //Permet de charger la liste des établissements liés au dossier pour la selection des rapports à generer
        $DBdossier = new Model_DbTable_Dossier;
        $this->view->listeEtablissement = $DBdossier->getEtablissementDossier((int) $this->_getParam("idDossier"));

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

        $this->view->numWinPrev = $etablissement['NUMEROID_ETABLISSEMENT'];

        //Informations de l'établissement (catégorie, effectifs, activité / type principal)
        $object_informations = $model_etablissement->getInformations($idEtab);

        $this->view->numPublic = $object_informations["EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"];
        $this->view->numPersonnel = $object_informations["EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS"];

        $this->view->categorieEtab = $object_informations["ID_CATEGORIE"];

        $this->view->etablissementLibelle = $object_informations['LIBELLE_ETABLISSEMENTINFORMATIONS'];
        $this->view->typeLettreP = $object_informations['ID_TYPE'];

        $activitePrincipale = $model_typeactivite->find($object_informations["ID_TYPEACTIVITE"])->current();
        $this->view->libelleActiviteP = $activitePrincipale["LIBELLE_ACTIVITE"];

        //Zend_Debug::dump($object_informations);

        //echo "ID ETAB INFO ".$object_informations->ID_ETABLISSEMENTINFORMATIONS;
        // Types / activités secondaires
        $model_typesactivitessecondaire = new Model_DbTable_EtablissementInformationsTypesActivitesSecondaires;
        $array_types_activites_secondaires = $model_typesactivitessecondaire->fetchAll("ID_ETABLISSEMENTINFORMATIONS = " . $object_informations->ID_ETABLISSEMENTINFORMATIONS)->toArray();


        $typeS = "";
        $actS = "";

        foreach ($array_types_activites_secondaires as $var) {
            $typeS .= $var['ID_TYPE_SECONDAIRE'].", ";
            $activiteSearchLibelle = $model_typeactivite->find($var['ID_TYPEACTIVITE_SECONDAIRE'])->current();
            $actS .= $activiteSearchLibelle["LIBELLE_ACTIVITE"].", ";
        }

        $this->view->activiteSecondaire = substr($actS, 0, -2);
        $this->view->typeSecondaire = substr($typeS, 0, -2);

        // Adresses
        $model_adresse = new Model_DbTable_EtablissementAdresse;
        $array_adresses = $model_adresse->get($idEtab);

        if (count($array_adresses) > 0) {
            $this->view->communeEtab = $array_adresses[0]["LIBELLE_COMMUNE"];
            $this->view->etablissementAdresse = $array_adresses[0]["LIBELLE_RUE"];
        }

        //Zend_Debug::dump($etablissement->toArray());

        //Récupération de tous les champs de la table dossier
        $DBdossier = new Model_DbTable_Dossier;
        $this->view->infosDossier = $DBdossier->find($idDossier)->current();


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
            /*
            if ($dossierType['TYPE_DOSSIER'] == 1) {
                //Cas d'une viste
                $listeDocConsulte = $dblistedoc->getDocVisite();
            } else {
                //Cas d'une etude
                $listeDocConsulte = $dblistedoc->getDocEtude();
            }
            */
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
            $dblistepresc = new Model_DbTable_PrescriptionDossier;
            $dbtexte = new Model_DbTable_PrescriptionTexte;
            $dbarticle = new Model_DbTable_PrescriptionArticle;

            $this->view->listePrescription = $dblistepresc->getListePrescription($this->_getParam('idDossier'));

            //Zend_Debug::dump($this->view->listePrescription);

            $compteur = 1;

            //Arrays contenant toutes les infos sur les différentes prescriptions qui seront envoyées à la vue
            $Textes = array();
            $Articles = array();
            $PrescriptionsLibelle = array();

            //Array temporaire d'une prescription qui récolte les infos de chaque description 1 par 1 puis vient s'inclure aux arrays décrits au dessus
            //On initialise pour le premier
            $listeTextePresc = array();
            $listeArticlePresc = array();

            foreach ($this->view->listePrescription as $indPresc => $val) {
                //On liste les prescriptions
                $numPrescription = $val['NUM_PRESCRIPTIONDOSSIER'];

                if ($val['TYPE_PRESCRIPTIONDOSSIER'] == 0) {
                    //la prescription se trouve dans prescription ASSOC
                    $infosPrescription = $dblistepresc->getPrescriptionAssoc($val['PRESCRIPTIONASSOC_PRESCRIPTIONDOSSIER']);
                } elseif ($val['TYPE_PRESCRIPTIONDOSSIER'] == 1) {
                    //la prescription se trouve dans prescription TYPE
                    $infosPrescription = $dblistepresc->getPrescriptionType($val['PRESCRIPTIONASSOC_PRESCRIPTIONDOSSIER']);
                }
                //Zend_Debug::dump($infosPrescription);

                if ($compteur != $numPrescription) {
                    //On réinitialise les tableau après les avoir ajoutés au tableau contenant toute les infos
                    array_push($Textes, $listeTextePresc);
                    array_push($Articles, $listeArticlePresc);
                    $compteur ++;
                    $listeTextePresc = array();
                    $listeArticlePresc = array();
                }

                if ($val['TYPE_PRESCRIPTIONDOSSIER'] == 0) {
                    $tabTexte = explode("_",$infosPrescription['TEXTE_PRESCRIPTIONASSOC']);
                } else {
                    $tabTexte = explode("_",$infosPrescription['TEXTE_PRESCRIPTIONTYPE']);
                }
                foreach ($tabTexte as $indText => $valText) {
                    //echo $valText."<br/>";
                    $texte = $dbtexte->find($valText)->current();
                    //echo $texte['LIBELLE_TEXTE'];
                    array_push($listeTextePresc, $texte['LIBELLE_TEXTE'] );
                }

                if ($val['TYPE_PRESCRIPTIONDOSSIER'] == 0) {
                    $tabArticle = explode("_",$infosPrescription['ARTICLE_PRESCRIPTIONASSOC']);
                } else {
                    $tabArticle = explode("_",$infosPrescription['ARTICLE_PRESCRIPTIONTYPE']);
                }
                foreach ($tabArticle as $indArticle => $valArticle) {
                    //echo $valArticle."<br/>";
                    $article = $dbarticle->find($valArticle)->current();
                    //echo $article['LIBELLE_ARTICLE'];
                    array_push($listeArticlePresc, $article['LIBELLE_ARTICLE'] );
                }
                array_push($PrescriptionsLibelle, $infosPrescription['LIBELLE_PRESCRIPTIONLIBELLE']);
            }
            array_push($Textes, $listeTextePresc);
            array_push($Articles, $listeArticlePresc);

            $this->view->nbPrescription = count($this->view->listePrescription);
            $this->view->listeTextes = $Textes;
            //Zend_Debug::dump($this->view->listeTextes);
            $this->view->listeArticles = $Articles;
            //Zend_Debug::dump($this->view->listeArticles);
            $this->view->ListePrescriptionsLibelle = $PrescriptionsLibelle;

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

            $this->render('creationdoc');

        //Zend_Debug::dump($this->view->infosDossier);

    }

    public function generationconvocAction()
    {
        //$this->_helper->viewRenderer->setNoRender();
        $dateCommId = $this->_getParam("dateCommId");
        $this->view->idComm = $dateCommId;

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

}
