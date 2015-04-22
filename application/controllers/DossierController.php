<?php

class DossierController extends Zend_Controller_Action
{
    private $id_dossier;

    //liste des champs à afficher en fonction de la nature
    private $listeChamps = array(
    //ETUDES
        //PC - OK
        "1" => array("type","DATEINSERT","OBJET","NUMDOCURBA","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","SERVICEINSTRUC","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","COORDSSI","DATESDIS","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //AT - OK
        "2" => array("type","DATEINSERT","OBJET","NUMDOCURBA","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","SERVICEINSTRUC","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","COORDSSI","DATESDIS","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //Dérogation - OK
        "3" => array("type","DATEINSERT","OBJET","NUMDOCURBA","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","SERVICEINSTRUC","COMMISSION","DESCGEN","JUSTIFDEROG","MESURESCOMPENS","MESURESCOMPLE","DESCEFF","DATECOMM","AVIS","COORDSSI","DATESDIS","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","REGLEDEROG","INCOMPLET","HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //Cahier des charges fonctionnel du SSI - OK
        "4" => array("type","DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DATECOMM","AVIS","COORDSSI","DATESDIS","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //Cahier des charges de type T - OK
        "5" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //Salon type T - OK
        "6" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","CHARGESEC","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","INCOMPLET", "HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //RVRMD (diag sécu) => Levée de prescriptions - OK
        "7" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //Documents divers - OK
        "8" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","DATEPREF","DATEREP","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //Changement de DUS - OK
        "9" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //Suivi organisme formation SSIAP - OK
        "10" => array("DATEINSERT","OBJET","NUMCHRONO","AVIS","DATESDIS","DATEPREF","DATEREP","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //Demande de registre de sécurité CTS - OK
        "11" => array("DATEINSERT","OBJET","NUMCHRONO","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","DATEPREF","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","INCOMPLET", "HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //Demande d'implantation CTS < 6mois - OK
        "12" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","INCOMPLET", "HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //Demande d'implantation CTS > 6mois - OK
        "13" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","INCOMPLET", "HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //Permis d'aménager - OK
        "14" => array("DATEINSERT","OBJET","NUMDOCURBA","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","DESCGEN","DESCEFF","DATESDIS","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //Permis de démolir - OK
        "15" => array("DATEINSERT","OBJET","NUMDOCURBA","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //CR de visite des organismes d'ins.... - OK
        "16" => array("DATEINSERT","OBJET","NUMCHRONO","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","DATEPREF","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //Etude suite a un avis ne se prononce pas - OK MAIS VOIR POUR PARTICULARITé TABLEAU
        "17" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //Utilisation exceptionnelle de locaux - OK
        "18" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","INCOMPLET", "HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //Levée de réserves - OK
        "19" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","AVIS_COMMISSION","OBSERVATION"),
        //Echéncier de travaux - OK
        "46" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //Déclaration préalable
        "30" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //RVRMD diag sécu
        "33" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //Autorisation d'une ICPE - OK
        "61" => array("type","DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","SERVICEINSTRUC","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","COORDSSI","DATESDIS","PREVENTIONNISTE","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        //Certificats d'urbanisme (CU) - OK
        "62" => array("type","DATEINSERT","OBJET","NUMDOCURBA","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","SERVICEINSTRUC","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","COORDSSI","DATESDIS","PREVENTIONNISTE","DEMANDEUR","INCOMPLET","HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
        // Demande d'organisation de manifestation temporaire - OK
        "63" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","DATESECRETARIAT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","AVIS","DATESDIS","PREVENTIONNISTE","ABSQUORUM","DEMANDEUR","INCOMPLET", "HORSDELAI","AVIS_COMMISSION","OBSERVATION"),
    //VISITE DE COMMISSION
        //Réception de travaux - OK
        "20" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATEVISITE","COORDSSI","PREVENTIONNISTE","ABSQUORUM","NPSP","AVIS_COMMISSION","OBSERVATION"),
        //Avant ouverture - OK
        "47" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATEVISITE","COORDSSI","PREVENTIONNISTE","ABSQUORUM","NPSP","AVIS_COMMISSION","OBSERVATION"),
        //Périodique - OK
        "21" => array("DATEINSERT","COMMISSION","DESCGEN","DESCEFF","DATEVISITE","PREVENTIONNISTE","DIFFEREAVIS","ABSQUORUM","AVIS","AVIS_COMMISSION","OBSERVATION"),
        //Chantier - OK
        "22" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATEVISITE","COORDSSI","PREVENTIONNISTE","OBSERVATION"),
        //Controle - OK
        "23" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATEVISITE","COORDSSI","PREVENTIONNISTE","DIFFEREAVIS","ABSQUORUM","AVIS_COMMISSION","OBSERVATION"),
        //Inopinéee - OK
        "24" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATEVISITE","PREVENTIONNISTE","DIFFEREAVIS","ABSQUORUM","AVIS_COMMISSION","OBSERVATION"),
    //GROUPE DE VISITE
        //Réception de travaux - OK
        "25" => array("type","DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATECOMM","DATEVISITE","AVIS","COORDSSI","PREVENTIONNISTE","NPSP","ABSQUORUM","AVIS_COMMISSION","OBSERVATION"),
        //Avant ouverture - OK
        "48" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATECOMM","DATEVISITE","AVIS","COORDSSI","PREVENTIONNISTE","NPSP","ABSQUORUM","AVIS_COMMISSION","OBSERVATION"),
        //Périodique - OK
        "26" => array("DATEINSERT","COMMISSION","DESCGEN","DESCEFF","DATECOMM","DATEVISITE","AVIS","PREVENTIONNISTE","DIFFEREAVIS","ABSQUORUM","AVIS_COMMISSION","OBSERVATION"),
        //Chantier - OK
        "27" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATEVISITE","COORDSSI","PREVENTIONNISTE","OBSERVATION"),
        //Controle - OK
        "28" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATECOMM","DATEVISITE","AVIS","COORDSSI","PREVENTIONNISTE","DIFFEREAVIS","ABSQUORUM","AVIS_COMMISSION","OBSERVATION"),
        //Inopinéee - OK
        "29" => array("DATEINSERT","OBJET","COMMISSION","DESCGEN","DESCEFF","DATECOMM","DATEVISITE","AVIS","PREVENTIONNISTE","DIFFEREAVIS","ABSQUORUM","AVIS_COMMISSION","OBSERVATION"),
    //REUNION
        //Locaux SDIS - OK
        "31" => array("DATEINSERT","OBJET","DATEREUN","PREVENTIONNISTE","DEMANDEUR","OBSERVATION"),
        //Exterieur SDIS - OK
        "32" => array("DATEINSERT","OBJET","LIEUREUNION","DATEREUN","PREVENTIONNISTE","DEMANDEUR","OBSERVATION"),
        //Téléphonique - OK
        "43" => array("DATEINSERT","OBJET","DATEREUN","PREVENTIONNISTE","DEMANDEUR","OBSERVATION"),
    //COURRIER/COURRIEL
        //Lettre - OK
        "52" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","PREVENTIONNISTE","DATEREP","DATEENVTRANSIT","PREVENTIONNISTE","DATESDIS","DEMANDEUR","DATETRANSFERTCOMM","DATERECEPTIONCOMM","OBSERVATION"),
        //Mise en demeure - OK
        "55" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","PREVENTIONNISTE","DATEREP","DATEENVTRANSIT","PREVENTIONNISTE","DATESDIS","DEMANDEUR","DATETRANSFERTCOMM","DATERECEPTIONCOMM","OBSERVATION"),
        //Avis écrit motivé - OK
        "51" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","PREVENTIONNISTE","DATEREP","DATEENVTRANSIT","PREVENTIONNISTE","DATESDIS","DEMANDEUR","DATETRANSFERTCOMM","DATERECEPTIONCOMM","OBSERVATION"),
        //Consultation PLU - OK
        "53" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","PREVENTIONNISTE","DATEREP","DATEENVTRANSIT","PREVENTIONNISTE","DATESDIS","DEMANDEUR","DATETRANSFERTCOMM","DATERECEPTIONCOMM","OBSERVATION"),
        //Rapport d'organisme agréé - OK
        "49" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","PREVENTIONNISTE","DATEREP","DATEENVTRANSIT","PREVENTIONNISTE","DATESDIS","DEMANDEUR","DATETRANSFERTCOMM","DATERECEPTIONCOMM","OBSERVATION"),
        //Demande de renseignements
        "54" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","PREVENTIONNISTE","DATEREP","DATEENVTRANSIT","PREVENTIONNISTE","DATESDIS","DEMANDEUR","DATETRANSFERTCOMM","DATERECEPTIONCOMM","OBSERVATION"),
        //Demande de visite périodique
        "59" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","PREVENTIONNISTE","DATEREP","DATEENVTRANSIT","PREVENTIONNISTE","DATESDIS","DEMANDEUR","DATETRANSFERTCOMM","DATERECEPTIONCOMM","OBSERVATION"),
        //Demande de visite technique
        "57" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","PREVENTIONNISTE","DATEREP","DATEENVTRANSIT","PREVENTIONNISTE","DATESDIS","DEMANDEUR","DATETRANSFERTCOMM","DATERECEPTIONCOMM","OBSERVATION"),
        //Demande de visite inopinée
        "58" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","PREVENTIONNISTE","DATEREP","DATEENVTRANSIT","PREVENTIONNISTE","DATESDIS","DEMANDEUR","DATETRANSFERTCOMM","DATERECEPTIONCOMM","OBSERVATION"),
        //Demande de visite hors programme
        "50" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","PREVENTIONNISTE","DATEREP","DATEENVTRANSIT","PREVENTIONNISTE","DATESDIS","DEMANDEUR","DATETRANSFERTCOMM","DATERECEPTIONCOMM","OBSERVATION"),
        //Demande de visite de réception
        "60" => array("DATEINSERT","OBJET","NUMCHRONO","DATEMAIRIE","PREVENTIONNISTE","DATEREP","DATEENVTRANSIT","PREVENTIONNISTE","DATESDIS","DEMANDEUR","DATETRANSFERTCOMM","DATERECEPTIONCOMM","OBSERVATION"),
    //INTERVENTION
        //Incendie - OK
        "37" => array("DATEINSERT","OBJET","OPERSDIS","RCCI","REX","NUMINTERV","DATEINTERV","DUREEINTERV","PREVENTIONNISTE","OBSERVATION"),
        //SAP - OK
        "38" => array("DATEINSERT","OBJET","OPERSDIS","REX","NUMINTERV","DATEINTERV","DUREEINTERV","PREVENTIONNISTE","OBSERVATION"),
        //Intervention div - OK
        "39" => array("DATEINSERT","OBJET","OPERSDIS","REX","NUMINTERV","DATEINTERV","DUREEINTERV","PREVENTIONNISTE","OBSERVATION"),
    //ARRETE
        //Ouverture - OK
        "40" => array("DATEINSERT","DATESIGN","PREVENTIONNISTE","OBSERVATION"),
        //Fermeture - OK
        "41" => array("DATEINSERT","OBJET","DATESIGN","PREVENTIONNISTE","OBSERVATION"),
        //Mise en demeure - OK
        "42" => array("DATEINSERT","OBJET","DATESIGN","PREVENTIONNISTE","OBSERVATION"),
        //Utilisation exceptionnelle de locaux - OK
        "44" => array("DATEINSERT","OBJET","DATESIGN","PREVENTIONNISTE","OBSERVATION"),
        //Courrier - OK
        "45" => array("DATEINSERT","OBJET","DATESIGN","PREVENTIONNISTE","OBSERVATION"),
    );

    public function init()
    {
        $this->_helper->layout->setLayout('dossier');
        $this->view->inlineScript()->appendFile('/js/dossier/dossierGeneral.js','text/javascript');

        // Actions à effectuées en AJAX
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('selectionabreviation', 'json')
            ->addActionContext('selectionetab', 'json')
            ->initContext();

        if (!isset($this->view->action)) {
            $this->view->action = $this->_request->getActionName();
        }

        $this->view->idDossier = ($this->_getParam("id"));

        $id_dossier = null;
        $id_dossier = $this->_getParam("id");
        if (null == $id_dossier) {
            $id_dossier = $this->_getParam("idDossier");
        }
        $this->view->idUser = Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR'];

        if ($id_dossier != null) {
            //Si on à l'id d'un dossier, on récupére tous les établissements liés à ce dossier
            $DBdossier = new Model_DbTable_Dossier();
            $dossier = $DBdossier->find($id_dossier)->current();

            $DBdossierType = new Model_DbTable_DossierType();
            $libelleType = $DBdossierType->find($dossier->TYPE_DOSSIER)->current();

            $this->view->objetDossier = $dossier->OBJET_DOSSIER;
            $this->view->idTypeDossier = $dossier->TYPE_DOSSIER;
            $this->view->libelleType = $libelleType['LIBELLE_DOSSIERTYPE'];

            $natureDossier = $DBdossier->getDossierTypeNature($id_dossier);
            $this->view->natureDossier = $natureDossier[0]['ID_NATURE'];
            $this->view->verrouDossier = $dossier['VERROU_DOSSIER'];
            $this->view->idDossier = ($this->_getParam("id"));

            $this->view->verrou = $dossier->VERROU_DOSSIER;
        }
    }

    public function pieceJointeAction()
    {
        $DBdossier = new Model_DbTable_Dossier();
        $service_dossier = new Service_Dossier;
        if($this->_getParam("id")){
            $this->view->enteteEtab = $service_dossier->getEtabInfos($this->_getParam("id"));
        }
        $this->infosDossier = $DBdossier->find((int) $this->_getParam("id"))->current();
        $this->_forward("index", "piece-jointe", null, array(
            "type" => "dossier",
            "id" => $this->_request->id,
            "verrou" => $this->infosDossier['VERROU_DOSSIER'],
        ));
    }

    public function addAction()
    {
        $this->view->action = "add";
        $this->_forward('index');
    }

    public function indexAction()
    {
        $this->view->do = "new";
        if ($this->_getParam("id")) {
            $this->view->do = "edit";
            $this->view->idDossier = ($this->_getParam("id"));
        }

        $service_dossier = new Service_Dossier;
        if($this->_getParam("id")){
            $this->view->enteteEtab = $service_dossier->getEtabInfos($this->_getParam("id"));
        }else if($this->_getParam("id_etablissement")){
            $this->view->enteteEtab = $service_dossier->getEtabInfos(null,$this->_getParam("id_etablissement"));
        }else{
        }

        $this->view->idEtablissement = $this->_getParam("id_etablissement");
        if (isset($this->view->idEtablissement)) {
            $DBetablissement = new Model_DbTable_Etablissement();
            $this->view->etablissementLibelle = $DBetablissement->getLibelle($this->_getParam("id_etablissement"));
        }

        //$this->view->entete = $this->getetabsAction($this->_getParam("id_etablissement"),$this->_getParam("id"));
        $this->view->idUser = Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR'];
        $this->view->userInfos = Zend_Auth::getInstance()->getIdentity();

        //On récupère tous les types de dossier
        $DBdossierType = new Model_DbTable_DossierType();
        $DBdossier = new Model_DbTable_Dossier();
        $this->view->dossierType = $DBdossierType->fetchAll();

        //Récupération de la liste des avis pour la génération du select
        $DBlisteAvis = new Model_DbTable_Avis();
        $this->view->listeAvis = $DBlisteAvis->getAvis();
        $this->view->afficherChamps = array();

        $listeMois = array("Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");
        $this->view->mois = $listeMois;

        // AUTORISATIONS CHANGEMENT AVIS DE LA COMMISSION
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');

        $this->view->is_allowed_change_avis = unserialize($cache->load('acl'))->isAllowed(Zend_Auth::getInstance()->getIdentity()['group']['LIBELLE_GROUPE'], "avis_commission", "edit_avis_com");

        $service_etablissement = new Service_Etablissement();

        if ($this->_getParam("idEtablissement")) {
            $this->view->idEtablissement = $this->_getParam("idEtablissement");
        } else {
        }

        /******
            RECUPERATIONS INFOS ETABLISSEMENT (cellule ou etab pour generation des avis)
        ******/
        if ($this->_getParam("id_etablissement")) {
            $DBetab = new Model_DbTable_Etablissement();
            $etabTab = $DBetab->getInformations($this->_getParam("id_etablissement"));
            $etablissement = $etabTab->toArray();
            $this->view->genre = $etablissement['ID_GENRE'];
            $commissionEtab = $etablissement['ID_COMMISSION'];
            $idEtablissement = $this->_getParam("id_etablissement");

            $etablissementInfos = $service_etablissement->get($this->view->idEtablissement);
            $ID_DOSSIER_DONNANT_AVIS = $etablissementInfos['general']['ID_DOSSIER_DONNANT_AVIS'];
            if ($ID_DOSSIER_DONNANT_AVIS != null) {
                $avisExploitationEtab = $DBdossier->getAvisDossier($ID_DOSSIER_DONNANT_AVIS);
                $this->view->avisExploitationEtab = $avisExploitationEtab['AVIS_DOSSIER'];
            } else {
                $this->view->avisExploitationEtab = 3;
            }
        } elseif ((int) $this->_getParam("id")) {
            $tabEtablissement = $DBdossier->getEtablissementDossier((int) $this->_getParam("id"));
            $this->view->listeEtablissement = $tabEtablissement;
            if (count($tabEtablissement) > 0) {
                $DBetab = new Model_DbTable_Etablissement();
                $etablissement = $DBetab->getInformations($tabEtablissement[0]['ID_ETABLISSEMENT'])->toArray();
                $this->view->genre = $etablissement['ID_GENRE'];
                $commissionEtab = $etablissement['ID_COMMISSION'];
                $idEtablissement = $tabEtablissement[0]['ID_ETABLISSEMENT'];

                $etablissementInfos = $service_etablissement->get($idEtablissement);
                $ID_DOSSIER_DONNANT_AVIS = $etablissementInfos['general']['ID_DOSSIER_DONNANT_AVIS'];

                if ($ID_DOSSIER_DONNANT_AVIS != null) {
                    $avisExploitationEtab = $DBdossier->getAvisDossier($ID_DOSSIER_DONNANT_AVIS);
                    $this->view->avisExploitationEtab = $avisExploitationEtab['AVIS_DOSSIER'];
                } else {
                    $this->view->avisExploitationEtab = 3;
                }
            }
        }

        if (isset($commissionEtab)) {
            $this->view->commissionEtab = $commissionEtab;
        }
        $genreInfo = $this->view->genre;
        if (isset($idEtablissement)) {
            $this->view->idEtablissement = $idEtablissement;
        }

        $today = new Zend_Date();
        $this->view->dateToday = $today->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);

        $DBdossierCommission = new Model_DbTable_Commission();

        // Modèle de données
        $model_typesDesCommissions = new Model_DbTable_CommissionType();
        $model_commission = new Model_DbTable_Commission();

        // On cherche tous les types de commissions
        $rowset_typesDesCommissions = $model_typesDesCommissions->fetchAll();

        // Tableau de résultats
        $array_commissions = array();

        // Pour tous les types, on cherche leur commission
        foreach ($rowset_typesDesCommissions as $row_typeDeCommission) {
            $array_commissions[$row_typeDeCommission->ID_COMMISSIONTYPE] = array(
                "LIBELLE" => $row_typeDeCommission->LIBELLE_COMMISSIONTYPE,
                "ARRAY" => $model_commission->fetchAll("ID_COMMISSIONTYPE = ".$row_typeDeCommission->ID_COMMISSIONTYPE)->toArray(),
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
            $this->view->infosDossier = $DBdossier->find($idDossier)->current();

            //On verifie les éléments masquant l'avis et la date de commission/visite pour les afficher ou non
            //document manquant - absence de quorum - hors delai - ne peut se prononcer - differe l'avis
            $absQuorum = $this->view->infosDossier['ABSQUORUM_DOSSIER'];
            $horsDelai = $this->view->infosDossier['HORSDELAI_DOSSIER'];
            $npsp = $this->view->infosDossier['NPSP_DOSSIER'];
            $differeAvis = $this->view->infosDossier['DIFFEREAVIS_DOSSIER'];

            //$this->view->typeDossierLibelle = $this->view->dossierType[$this->view->infosDossier['TYPE_DOSSIER']];
            //Debut mise en place avec service (voir pour récup le type )

            $afficheAvis = 1;
            if (!isset($absQuorum) || $absQuorum != 0) {
                $afficheAvis = 0;
            } elseif (!isset($horsDelai) || $horsDelai != 0) {
                $afficheAvis = 0;
            //} elseif (!isset($npsp) || $npsp != 0) {
            } elseif (!isset($npsp)) {
                $afficheAvis = 0;
            } elseif (!isset($differeAvis) || $differeAvis != 0) {
                $afficheAvis = 0;
            } elseif ($this->view->infosDossier['INCOMPLET_DOSSIER'] != 0) {
                $afficheAvis = 0;
            }

            $this->view->afficheAvis = $afficheAvis;

            //récuperation des informations sur le créateur du dossier
            $DB_user = new Model_DbTable_Utilisateur();
            $DB_informations = new Model_DbTable_UtilisateurInformations();
            if ($this->view->infosDossier['CREATEUR_DOSSIER']) {
                $user = $DB_user->find($this->view->infosDossier['CREATEUR_DOSSIER'])->current();
                $this->view->user_info = $DB_informations->find($user->ID_UTILISATEURINFORMATIONS)->current();
            } else {
                $this->view->user_info = "";
            }

            if ($this->view->infosDossier['VERROU_USER_DOSSIER']) {
                $user = $DB_user->find($this->view->infosDossier['VERROU_USER_DOSSIER'])->current();
                $this->view->user_infoVerrou = $DB_informations->find($user->ID_UTILISATEURINFORMATIONS)->current();
            }

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

            //Conversion date echeancier de travaux
            if ($this->view->infosDossier['ECHEANCIERTRAV_DOSSIER'] != '') {
                $date = new Zend_Date($this->view->infosDossier['ECHEANCIERTRAV_DOSSIER'], Zend_Date::DATES);
                $this->view->infosDossier['ECHEANCIERTRAV_DOSSIER'] = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                $this->view->ECHEANCIERTRAV = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
            }

            //Conversion date incomplet
            if ($this->view->infosDossier['DATEINCOMPLET_DOSSIER'] != '') {
                $date = new Zend_Date($this->view->infosDossier['DATEINCOMPLET_DOSSIER'], Zend_Date::DATES);
                $this->view->infosDossier['DATEINCOMPLET_DOSSIER'] = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                $this->view->DATEINCOMPLET = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
            }

            //Conversion de transfert à la commission compétente
            if ($this->view->infosDossier['DATETRANSFERTCOMM_DOSSIER'] != '') {
                $date = new Zend_Date($this->view->infosDossier['DATETRANSFERTCOMM_DOSSIER'], Zend_Date::DATES);
                $this->view->infosDossier['DATETRANSFERTCOMM_DOSSIER'] = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                $this->view->DATETRANSFERTCOMM = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
            }

            //Conversion de reception à la commission compétente
            if ($this->view->infosDossier['DATERECEPTIONCOMM_DOSSIER'] != '') {
                $date = new Zend_Date($this->view->infosDossier['DATERECEPTIONCOMM_DOSSIER'], Zend_Date::DATES);
                $this->view->infosDossier['DATERECEPTIONCOMM_DOSSIER'] = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                $this->view->DATERECEPTIONCOMM = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
            }

            //Conversion de la durée de l'intervention
            if ($this->view->infosDossier['DUREEINTERV_DOSSIER'] != '') {
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

            if ($this->view->infosDossier["AVIS_DOSSIER_COMMISSION"] != '') {
                $this->view->AVIS_COMMISSION_VALUE = $DBlisteAvis->getAvisLibelle($this->view->infosDossier["AVIS_DOSSIER_COMMISSION"]);
            }

            //Récupération du libellé du type de dossier
            $libelleType = $DBdossierType->find($this->view->infosDossier['TYPE_DOSSIER'])->current();
            $this->view->libelleType = $libelleType['LIBELLE_DOSSIERTYPE'];

            //Récupération tous les libellé des natures du dossier concerné
            $DBdossierNature = new Model_DbTable_DossierNature();
            $this->view->natureConcerne = $DBdossierNature->getDossierNaturesLibelle($idDossier);

            //Récupération de la liste des natures pour la génération du select
            $DBdossierNatureListe = new Model_DbTable_DossierNatureliste();
            $this->view->dossierNatureListe = $DBdossierNatureListe->getDossierNature($this->view->infosDossier['TYPE_DOSSIER']);

            //Récupération de la liste des documents d'urbanismes
            $DBdossierDocUrba = new Model_DbTable_DossierDocUrba();
            $this->view->dossierDocUrba = $DBdossierDocUrba->getDossierDocUrba($idDossier);

            //On récupére l'ensemble des commissions pour l'affichage du select

            //ICI RéCUPERATION DU LIBELLE DE LA COMMISSION !!!!!!!!!!! PUIS AFFICHAGE DANS LE INPUT !!!
            $this->view->commissionInfos = $DBdossierCommission->find($this->view->infosDossier['COMMISSION_DOSSIER'])->current();
            $this->view->commissionInfosCommissionType = $model_typesDesCommissions->find($this->view->commissionInfos['ID_COMMISSIONTYPE'])->current();

            //On récupère la liste de tous les champs que l'on doit afficher en fonction des natures
            //Si il y à plusieurs natures on les fait une par une pour savoir tous les champs à afficher
            $premiereNature = 1;
            $afficherChamps = array();
            foreach ($this->view->natureConcerne as $value) {
                if (1 == $premiereNature) {
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

            //On verifie les éléments masquant l'avis et la date de commission/visite pour les afficher ou non


        /*
        GESTION DES DATES DE COMMISSIONS ET DE VISITE / GROUPE DE VISITE
        */
            //On récupere les infos concernant l'affectation à une commission si il y en a eu une
            $dbAffectDossier = new Model_DbTable_DossierAffectation();
            $affectDossier = $dbAffectDossier->find(null,$this->_getParam("id"))->current();
            $this->view->affectDossier = $affectDossier;

            $listeDateAffectDossier = $dbAffectDossier->recupDateDossierAffect($this->_getParam("id"));

            $dbDateComm = new Model_DbTable_DateCommission();
            $dateComm = $dbDateComm->find($affectDossier['ID_DATECOMMISSION_AFFECT'])->current();

            //En fonction du type de dossier on traite les dates d'affectation existantes differement
            if (1 == $this->view->infosDossier['TYPE_DOSSIER']) {
                // CAS D'UNE éTUDE
                //Concernant cette affectation on récupere les infos sur la commission (date aux différents format)
                if ($dateComm['DATE_COMMISSION'] != '') {
                    $date = new Zend_Date($dateComm['DATE_COMMISSION'], Zend_Date::DATES);
                    $this->view->dateCommValue = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                    $this->view->dateCommInput = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
                    $this->view->idDateCommissionAffect = $dateComm['ID_DATECOMMISSION'];
                }
            } elseif (2 == $this->view->infosDossier['TYPE_DOSSIER'] || 3 == $this->view->infosDossier['TYPE_DOSSIER']) {
                // CAS D'UNE VISITE
                foreach ($listeDateAffectDossier as $val => $ue) {
                    if (1 == $ue['ID_COMMISSIONTYPEEVENEMENT']) {
                        //COMMISSION EN SALLE
                        $date = new Zend_Date($ue['DATE_COMMISSION'], Zend_Date::DATES);
                        $this->view->dateCommValue = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                        $this->view->dateCommInput = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
                        $this->view->idDateCommissionAffect = $ue['ID_DATECOMMISSION'];
                    } else {
                        //VISITE OU GROUPE DE VISITE
                        $dateVisite = $dbDateComm->getInfosVisite($this->_getParam("id"));

                        $dateLiees = $dbDateComm->getDateLieesv2($dateVisite['ID_DATECOMMISSION_AFFECT']);
                        $this->view->dateVisite = $this->view->infosDossier['DATEVISITE_DOSSIER'];

                        $nbDates = count($dateLiees);

                        $listeDateValue = "";
                        $listeDateInput = "";
                        foreach ($dateLiees as  $val => $ue) {
                            $date = new Zend_Date($ue['DATE_COMMISSION'], Zend_Date::DATES);
                            $this->view->idDateVisiteAffect = $ue['ID_DATECOMMISSION'];
                            $listeDateValue .= $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME_SHORT." ".Zend_Date::YEAR);
                            $listeDateInput .= $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
                            if ($nbDates > 1) {
                                $listeDateValue .= ", ";
                                $listeDateInput .= ", ";
                            }
                            $nbDates--;
                        }

                        $this->view->idDateVisiteAffect = $dateVisite['ID_DATECOMMISSION_AFFECT'];
                        $this->view->dateVisiteValue = $listeDateValue;
                        $this->view->dateVisiteInput = $listeDateInput;
                    }
                }
            }

            //Recuperation des documents manquants dans le cas d'un dossier incomplet
            $dbDossDocManquant = new Model_DbTable_DossierDocManquant();
            $this->view->listeDocManquant = $dbDossDocManquant->getDocManquantDoss($this->_getParam("id"));

            $DBdossierPrev = new Model_DbTable_DossierPreventionniste();
            $this->view->preventionnistes = $DBdossierPrev->getPrevDossier($this->_getParam("id"));
        } else {
            $this->view->do = 'new';
            $search = new Model_DbTable_Search();
            $preventionnistes = ($this->_getParam("id_etablissement")) ? $search->setItem("utilisateur")->setCriteria("etablissementinformations.ID_ETABLISSEMENT", $this->_getParam("id_etablissement"))->run()->getAdapter()->getItems(0, 99999999999)->toArray() : null;
            $preventionnistes[-1] = array_fill_keys (array( "LIBELLE_GRADE", "NOM_UTILISATEURINFORMATIONS", "PRENOM_UTILISATEURINFORMATIONS" ) , null);
            unset($preventionnistes[-1]);
            $this->view->preventionnistes = $preventionnistes;
            $this->view->listeDocManquant = array();
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
    }

    public function fonctionAction()
    {
        $this->view->do = $this->_getParam("do");
        switch ($this->view->do) {
            case "showNature":
                $idType = (int) $this->_getParam("idType");

                //Récupération de la liste des natures
                $DBdossiernatureliste = new Model_DbTable_DossierNatureliste();
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
                        if (1 == $premiereNature) {
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
                $DBdossier = new Model_DbTable_Dossier();
                $this->view->infosDossier = $DBdossier->find($idDossier)->current();

                //Récupération tous les libellé des natures du dossier concerné
                $DBdossierNature = new Model_DbTable_DossierNature();
                $this->view->natureConcerne = $DBdossierNature->getDossierNaturesLibelle($idDossier);

                //On récupère la liste de tous les champs que l'on doit afficher en fonction des natures
                //Si il y à plusieurs natures on les fait une par une pour savoir tous les champs à afficher
                $premiereNature = 1;
                $afficherChamps = array();
                foreach ($this->view->natureConcerne as $value) {
                    if (1 == $premiereNature) {
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
            case "addDocUrba":
                //ajoute dans la base de données un document d'urbanisme au dossier (mode édition seulement)
                if ($this->_getParam('numDoc') && $this->_getParam('idDossier')) {
                    $DBdocUrba = new Model_DbTable_DossierDocUrba();
                    $newDocUrba = $DBdocUrba->createRow();
                    $newDocUrba->NUM_DOCURBA = $this->_getParam('numDoc');
                    $newDocUrba->ID_DOSSIER = $this->_getParam('idDossier');
                    $newDocUrba->save();
                }
            break;
            case "deleteDocUrba":
                //supprime un document d'urbanisme dans la base de données (en mode édition seulement)
                $DBdocUrba = new Model_DbTable_DossierDocUrba();
                $numDocSupp = $DBdocUrba->find($this->_getParam('idNumDoc'))->current();
                $numDocSupp->delete();
            break;
            case 'ajoutDocValid':
                $this->ajoutdocAction($this->id_dossier);
            break;
            case 'suppDoc':
                //cas de la suppression d'un document qui avait été renseigné
                $tabInfos = split("_",$this->_getParam('docInfos'));
                $nature = $tabInfos[0];
                $numdoc = $tabInfos[1];
                if (count($tabInfos) == 2) {
                    //cas d'un document existant
                    $dbToUse = new Model_DbTable_DossierDocConsulte();
                    $searchResult = $dbToUse->getGeneral($this->_getParam('idDossier'), $numdoc);
                    $docDelete = $dbToUse->find($searchResult['ID_DOSSIERDOCCONSULTE'])->current();
                    $docDelete->delete();
                } elseif (count($tabInfos) == 3) {
                    //cas d'un document ajouté
                    $dbToUse = new Model_DbTable_ListeDocAjout();
                    $searchResult = $dbToUse->find($numdoc)->current();
                    $searchResult->delete();
                }
            break;
            case 'showMadContent':
                echo $this->_getParam('numPresc');
                $dbPrescDossier = new Model_DbTable_PrescriptionDossier();
            break;
            case 'showListeDossierEtab':
                //On place dans un tableau chacun des idEtablissement liés au dossier
                $listeEtab = explode("-", $this->_getParam('idListeEtab'));

                //Pour chacun des établissement on va récuperer les dossiers concernés
                $dbDossier = new Model_DbTable_Dossier();
                $listeDossierEtab = array();
                foreach ($listeEtab as $lib => $val) {
                    $listeDossierEtab[$val] = $dbDossier->getDossierEtab($val,$this->_getParam('idDossier'));
                }
                $this->view->idDossier = $this->_getParam('idDossier');
                $this->view->listeEtab = $listeEtab;
                $this->view->listeDossierEtab = $listeDossierEtab;

            break;
            case 'showDossiersLies':
                //On commence par récuperer les dossiers liés à celui dans lequel on est
                $dbDossierLie = new Model_DbTable_DossierLie();
                $this->listeDossierLies = $dbDossierLie->getDossierLie($this->_getParam('idDossier'));

                $dbDossier = new Model_DbTable_Dossier();

                foreach ($this->listeDossierLies as $numrez => $attr) {
                    //on parcour chacun dossiers liers pour en récupérer les informations à afficher
                    if ($this->_getParam('idDossier') == $attr['ID_DOSSIER1']) {
                        $dossierToShow = $attr['ID_DOSSIER2'];
                    } elseif ($this->_getParam('idDossier') == $attr['ID_DOSSIER2']) {
                        $dossierToShow = $attr['ID_DOSSIER1'];
                    }

                    $infosEtabDossier = $dbDossier->getEtablissementDossier($dossierToShow);

                    $infosDossier = $dbDossier->getDossierTypeNature($dossierToShow);

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
                    $dbDossier = new Model_DbTable_DossierLie();
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
                $dbDossierPj = new Model_DbTable_DossierPj();
                $dossierPjEdit = $dbDossierPj->find($this->_getParam('idDossier'),$this->_getParam('idPjCommission'))->current();

                if ($this->_getParam('checked') == 'true') {
                    $dossierPjEdit->PJ_COMMISSION = 1;
                } elseif ($this->_getParam('checked') == 'false') {
                    $dossierPjEdit->PJ_COMMISSION = 0;
                }
                $dossierPjEdit->save();
            break;
        }
    }

	public function formdocmanquantAction()
	{
		$dbDocManquant = new Model_DbTable_DocManquant();
		//Si on passe un id dossier en param alors on cherche le dernier champ doc manquant si il existe
		//On recupere la liste des documents manquant type
		$this->view->listeDoc = $dbDocManquant->getDocManquant();
		$this->view->numDocManquant = $this->_getParam('numDoc');

        $date = Zend_Date::now();
        $this->view->dateDay = $date->get(Zend_Date::DAY_SHORT."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
	}

    public function savenewAction()
    {
        $this->_forward("save");
    }

    //Permet de faire les insertions de dossier en base de données et de rediriger vers le dossier/index/id/X => X = id du dossier qui vient d'être crée
    public function saveAction()
    {
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');

        try {
            $this->_helper->viewRenderer->setNoRender();
            $DBdossier = new Model_DbTable_Dossier();
            $DBdossierNature = new Model_DbTable_DossierNature();
            if ($this->_getParam('do') == 'new') {
                $nouveauDossier = $DBdossier->createRow();
                $nouveauDossier->CREATEUR_DOSSIER = $this->_getParam('ID_CREATEUR');
            } elseif ($this->_getParam('do') == 'edit') {
                $nouveauDossier = $DBdossier->find($this->_getParam('idDossier'))->current();

                $oldType = $nouveauDossier['TYPE_DOSSIER'];
                $newType = $this->_getParam("TYPE_DOSSIER");
                $oldNature = $DBdossier->getNatureDossier($this->_getParam('idDossier'));
                $oldNature = $oldNature['ID_NATURE'];

                $newNature = $this->_getParam("selectNature");

                $arrayT1 = array(24,21,23,29,26,28);
                $arrayT2 = array(20,47,25,48);

                $dbDocConsulte = new Model_DbTable_DossierDocConsulte();
                $dbDocAjout = new Model_DbTable_ListeDocAjout();

                if( in_array($oldNature, $arrayT1) && in_array($newNature, $arrayT1)){
                    //On conserve les documents consultés
                }else if( in_array($oldNature, $arrayT2) && in_array($newNature, $arrayT2) ){
                    //On conserve les documents consultés en faisant une copie dans la table docajout
                    $docRestant = $dbDocConsulte->getDocOtheNature($this->_getParam('idDossier'),$oldNature);
                    foreach($docRestant as $doc){
                        $newDocAjout = $dbDocAjout->createRow();
                        $newDocAjout->LIBELLE_DOCAJOUT = $doc['LIBELLE_DOC'];
                        $newDocAjout->REF_DOCAJOUT = $doc['REF_CONSULTE'];
                        $newDocAjout->DATE_DOCAJOUT = $doc['DATE_CONSULTE'];
                        $newDocAjout->ID_DOSSIER = $doc['ID_DOSSIER'];
                        $newDocAjout->ID_NATURE = $newNature;
                        $newDocAjout->save();

                        $where = $dbDocConsulte->getAdapter()->quoteInto('ID_DOSSIERDOCCONSULTE = ?', $doc['ID_DOSSIERDOCCONSULTE']);
                        $dbDocConsulte->delete($where);
                    }
                }else if( $oldNature != $newNature ){
                    //On supprime les documents consultés
                    $where = $dbDocAjout->getAdapter()->quoteInto('ID_DOSSIER = ?', $this->_getParam('idDossier'));
                    $dbDocAjout->delete($where);

                    $where = $dbDocConsulte->getAdapter()->quoteInto('ID_DOSSIER = ?', $this->_getParam('idDossier'));
                    $dbDocConsulte->delete($where);
                }
            }

            foreach ($_POST as $libelle => $value) {
                //On exclu la lecture de selectNature => select avec les natures;
                //NUM_DOCURB => input text pour la saisie des doc urba; docUrba & natureId => interpreté après;
                if ($libelle != "DATEVISITE_PERIODIQUE" && $libelle != "selectNature" && $libelle != "NUM_DOCURBA" && $libelle != "natureId" && $libelle != "docUrba" && $libelle != 'do' && $libelle != 'idDossier' && $libelle != 'HEUREINTERV_DOSSIER' && $libelle != 'idEtablissement' && $libelle != 'ID_AFFECTATION_DOSSIER_VISITE' && $libelle != 'ID_AFFECTATION_DOSSIER_COMMISSION' && $libelle != "preventionniste" && $libelle != "commissionSelect" && $libelle != "ID_CREATEUR" && $libelle != "HORSDELAI_DOSSIER" && $libelle != "genreInfo" && $libelle != "docManquant" && $libelle != "dateReceptionDocManquant" && $libelle != "dateDocManquant" && $libelle != "ABSQUORUM_DOSSIER" && $libelle != "servInst" && $libelle != "servInstVille" && $libelle != "servInstGrp" && $libelle != "repercuterAvis" && $libelle != "INCOMPLET_DOSSIER") {
                    //Test pour voir s'il sagit d'une date pour la convertir au format ENG et l'inserer dans la base de données
                    if ("DATEMAIRIE_DOSSIER" == $libelle || "DATESECRETARIAT_DOSSIER" == $libelle || "DATEVISITE_DOSSIER" == $libelle || "DATECOMM_DOSSIER" == $libelle || "DATESDIS_DOSSIER" == $libelle || "DATEPREF_DOSSIER" ==  $libelle || "DATEREP_DOSSIER" ==  $libelle || "DATEREUN_DOSSIER" ==  $libelle || "DATEINTERV_DOSSIER" == $libelle || "DATESIGN_DOSSIER" == $libelle || "DATEINSERT_DOSSIER" == $libelle || "DATEENVTRANSIT_DOSSIER" == $libelle || "ECHEANCIERTRAV_DOSSIER" == $libelle || "DATETRANSFERTCOMM_DOSSIER" == $libelle || "DATERECEPTIONCOMM_DOSSIER" == $libelle) {
                        if ($value) {
                            $dateTab = explode("/",$value);
                            $value = $dateTab[2]."-".$dateTab[1]."-".$dateTab[0];
                            if ("DATEINTERV_DOSSIER" == $libelle) {
                                $value .= " ".$this->_getParam('HEUREINTERV_DOSSIER');
                            }
                        } else {
                            $value = null;
                        }
                    }

                    if ('AVIS_DOSSIER' == $libelle && 0 == $value) {
                        $value = null;
                    }

                    if ('' == $value) {
                        $value = null;
                    }

                    $nouveauDossier->$libelle = $value;
                }
            }

            if (!$this->_getParam('HORSDELAI_DOSSIER')) {
                $nouveauDossier->HORSDELAI_DOSSIER = 0;
            } else {
                $nouveauDossier->HORSDELAI_DOSSIER = 1;
            }

            if (!$this->_getParam('ABSQUORUM_DOSSIER')) {
                $nouveauDossier->ABSQUORUM_DOSSIER = 0;
            } else {
                $nouveauDossier->ABSQUORUM_DOSSIER = 1;
            }

            if (!$this->_getParam('NPSP_DOSSIER')) {
                $nouveauDossier->NPSP_DOSSIER = 0;
            } else {
                $nouveauDossier->NPSP_DOSSIER = 1;
            }

            if (!$this->_getParam('DIFFEREAVIS_DOSSIER')) {
                $nouveauDossier->DIFFEREAVIS_DOSSIER = 0;
            } else {
                $nouveauDossier->DIFFEREAVIS_DOSSIER = 1;
            }

            if (!$this->_getParam('CNE_DOSSIER')) {
                $nouveauDossier->CNE_DOSSIER = 0;
            } else {
                $nouveauDossier->CNE_DOSSIER = 1;
            }

            if (null != $this->_getParam('servInst')) {
                if ($this->_getParam("servInst") == "servInstGrp") {
                    //service instructeur groupement
                    $nouveauDossier->TYPESERVINSTRUC_DOSSIER = $this->_getParam("servInst");
                    $nouveauDossier->SERVICEINSTRUC_DOSSIER = $this->_getParam("servInstGrp");
                } elseif ($this->_getParam("servInst") == "servInstCommune") {
                    //service instructeur commune
                    $nouveauDossier->TYPESERVINSTRUC_DOSSIER = $this->_getParam("servInst");
                    $nouveauDossier->SERVICEINSTRUC_DOSSIER = $this->_getParam("servInstVille");
                }
            }

            $nouveauDossier->save();

            if ( ($this->_getParam("selectNature") == 21 && $this->_getParam("TYPE_DOSSIER") == 2) || $this->_getParam("selectNature") == 26 ){
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

            $idNature = $this->_getParam("selectNature");

            $DBetablissementDossier = new Model_DbTable_EtablissementDossier();
            if ($this->_getParam('do') == 'new') {
                if (isset($_POST['idEtablissement']) &&  $_POST['idEtablissement'] != "") {
                    $saveEtabDossier = $DBetablissementDossier->createRow();
                    $saveEtabDossier->ID_ETABLISSEMENT = $this->_getParam('idEtablissement');
                    $saveEtabDossier->ID_DOSSIER = $idDossier;
                    $saveEtabDossier->save();
                }
                //Sauvegarde des natures du dossier


                $saveNature = $DBdossierNature->createRow();
                $saveNature->ID_DOSSIER = $idDossier;
                $saveNature->ID_NATURE = $_POST['selectNature'];
                $saveNature->save();

                //Récupération des contacts de l'établissement (Resp. unique de sécu, Proprio, Exploitant, DUS)
                $dbDossierContact = new Model_DbTable_DossierContact();
                $contactsEtab = $dbDossierContact->recupContactEtablissement($this->_getParam('idEtablissement'));

                foreach ($contactsEtab as $contact) {
                    if (8 == $contact['ID_FONCTION'] || 9 == $contact['ID_FONCTION'] || 17 == $contact['ID_FONCTION'] || 7 == $contact['ID_FONCTION']) {
                        $newContact = $dbDossierContact->createRow();
                        $newContact->ID_DOSSIER = $idDossier;
                        $newContact->ID_UTILISATEURINFORMATIONS = $contact['ID_UTILISATEURINFORMATIONS'];
                        $newContact->save();
                    }
                }
            } else {
                //gestion des natures en mode édition
                $DBdossierNature = new Model_DbTable_DossierNature();
                $natureCheck = $DBdossierNature->getDossierNaturesId($idDossier);
                $nature = $DBdossierNature->find($natureCheck['ID_DOSSIERNATURE'])->current();
                $nature->ID_NATURE = $idNature;
                $nature->save();
            }

            //On met le champ ID_DOSSIER_DONNANT_AVIS de établissement avec l'ID du dossier que l'on vient d'enregistrer dans les cas suivant
            if ($this->_getParam("AVIS_DOSSIER_COMMISSION") && ($this->_getParam("AVIS_DOSSIER_COMMISSION") == 1 || $this->_getParam("AVIS_DOSSIER_COMMISSION") == 2)) {
                $MAJEtab = 0;

                if ($this->_getParam("TYPE_DOSSIER") == 1 && in_array($idNature, array(19, 7, 17))) {
                    //Cas d'une étude uniquement dans le cas d'une levée de reserve
                    $MAJEtab = 1;
                } elseif ($this->_getParam("TYPE_DOSSIER") == 2 && (21 == $idNature || 23 == $idNature || 24 == $idNature || 47 == $idNature)) {
                    //Cas d'une viste uniquement dans le cas d'une VP, inopinée, avant ouverture ou controle
                    $MAJEtab = 1;
                } elseif ($this->_getParam("TYPE_DOSSIER") == 3 && (26 == $idNature || 28 == $idNature || 29 == $idNature || 48 == $idNature)) {
                    //Cas d'un groupe deviste uniquement dans le cas d'une VP, inopinée, avant ouverture ou controle
                    $MAJEtab = 1;
                }

                $dbEtab = new Model_DbTable_Etablissement();
                $service_etablissement = new Service_Etablissement();

                if (1 == $MAJEtab) {
                    if ($this->_getParam('do') == 'new') {
                        $listeEtab = array(array(
                            'ID_ETABLISSEMENT' => $this->_getParam('idEtablissement'),
                        ));
                    } else {
                        $listeEtab = $DBetablissementDossier->getEtablissementListe($idDossier);
                    }

                    foreach ($listeEtab as $val => $ue) {

                        $etabToEdit = $dbEtab->find($ue['ID_ETABLISSEMENT'])->current();

                        //Avant la mise à jour du champ ID_DOSSIER_DONNANT_AVIS on s'assure que la date de l'avis est plus récente
                        if(isset($etabToEdit->ID_DOSSIER_DONNANT_AVIS) && $etabToEdit->ID_DOSSIER_DONNANT_AVIS != NULL) {
                            $dossierAncienAvis = $DBdossier->find($etabToEdit->ID_DOSSIER_DONNANT_AVIS)->current();

                            if($dossierAncienAvis->TYPE_DOSSIER == 1 || $dossierAncienAvis->TYPE_DOSSIER == 3){

                                if($dossierAncienAvis->DATECOMM_DOSSIER != NULL && $dossierAncienAvis->DATECOMM_DOSSIER != ''){
                                    $dateAncienAvis = $dossierAncienAvis->DATECOMM_DOSSIER;
                                }else{
                                    $dateAncienAvis = $dossierAncienAvis->DATEINSERT_DOSSIER;
                                }

                                if($nouveauDossier->DATECOMM_DOSSIER != NULL && $nouveauDossier->DATECOMM_DOSSIER != ''){
                                    $dateNewAvis = $nouveauDossier->DATECOMM_DOSSIER;
                                }else{
                                    $dateNewAvis = $nouveauDossier->DATEINSERT_DOSSIER;
                                }

                            }else if($dossierAncienAvis->TYPE_DOSSIER == 2){

                                if($dossierAncienAvis->DATEVISITE_DOSSIER != NULL && $dossierAncienAvis->DATEVISITE_DOSSIER != ''){
                                    $dateAncienAvis = $dossierAncienAvis->DATEVISITE_DOSSIER;
                                }else{
                                    $dateAncienAvis = $dossierAncienAvis->DATEINSERT_DOSSIER;
                                }

                                if($nouveauDossier->DATEVISITE_DOSSIER != NULL && $nouveauDossier->DATEVISITE_DOSSIER != ''){
                                    $dateNewAvis = $nouveauDossier->DATEVISITE_DOSSIER;
                                }else{
                                    $dateNewAvis = $nouveauDossier->DATEINSERT_DOSSIER;
                                }
                            }

                            $dateAncienAvis = new Zend_Date($dateAncienAvis, Zend_Date::DATES);
                            $dateNewAvis = new Zend_Date($dateNewAvis, Zend_Date::DATES);

                            if($dateNewAvis > $dateAncienAvis){
                                $MAJEtab = 1;
                            }else{
                                $MAJEtab = 0;
                            }

                        }else{
                            $MAJEtab == 1;
                        }

                        if(  $MAJEtab == 1 ){
                            $etabToEdit->ID_DOSSIER_DONNANT_AVIS = $idDossier;
                            $etabToEdit->save();
                            $cache->remove('etablissement_id_'.$ue['ID_ETABLISSEMENT']);

                            if ($this->_getParam('repercuterAvis'))
                            {
                                $etablissementInfos = $service_etablissement->get($ue['ID_ETABLISSEMENT']);
                                foreach ($etablissementInfos["etablissement_lies"] as $etabEnfant) {
                                    $etabToEdit = $dbEtab->find($etabEnfant["ID_ETABLISSEMENT"])->current();
                                    $etabToEdit->ID_DOSSIER_DONNANT_AVIS = $idDossier;
                                    $etabToEdit->save();
                                    $cache->remove('etablissement_id_'.$etabEnfant['ID_ETABLISSEMENT']);
                                }
                            }

                            // AVERTISSEMENT SUR L'OUVERTURE D'UN ETABLISSEMENT A EFFECTUER
                            // Dans le cas d'une visite avant ouverture avec avis de commission positif
                            if ($this->_getParam("AVIS_DOSSIER_COMMISSION") == 1 && in_array($idNature, array(47, 48)))
                            {
                                $etabInformation = $dbEtab->getInformations($ue["ID_ETABLISSEMENT"]);
                                // Si l'établissement est en statut projet, et uniquement ce cas
                                if ($etabInformation && 1 == $etabInformation->ID_STATUT) {
                                    $this->_helper->flashMessenger(array(
                                        'context' => 'warning',
                                        'title' => 'Avertissement',
                                        'message' => "La visite d'avant ouverture étant favorable, vous devriez passer le statut de l'établissement <a title='Ouvrir' href='/etablissement/edit/id/".$ue["ID_ETABLISSEMENT"]."'>".$etabInformation["LIBELLE_ETABLISSEMENTINFORMATIONS"]."</a> à 'ouvert' (statut actuellement à 'projet').",
                                    ));
                                }
                            }
                        }
                    }
                }

            }

            //GESTION DE LA RECUPERATION DES TEXTES APPLICABLES DANS CERTAINS CAS
            //lorsque je crée un dossier visite ou groupe de visite VP (21-26), VC (22-27), VI (24-29),
            //il faut que les textes applicables à l’ERP se retrouvent de fait dans le dossier créé
            if ((21 == $idNature ||  22 == $idNature || 24 == $idNature || 26 == $idNature || 27 == $idNature || 29 == $idNature) &&  $_POST['idEtablissement'] != "" && 'new' == $_POST['do']) {
                $dbEtablissementTextAppl = new Model_DbTable_EtsTextesAppl();
                $listeTexteApplEtab = $dbEtablissementTextAppl->recupTextes($_POST['idEtablissement']);
                $dbDossierTexteAppl = new Model_DbTable_DossierTextesAppl();
                foreach ($listeTexteApplEtab as $val => $ue) {
                    $saveTexteAppl = $dbDossierTexteAppl->createRow();
                    $saveTexteAppl->ID_DOSSIER = $idDossier;
                    $saveTexteAppl->ID_TEXTESAPPL = $ue['ID_TEXTESAPPL'];
                    $saveTexteAppl->save();
                }
            }

            //GESTION DE LA RECUPERATION DES PRESCRIPTIONS EN RAPPEL REGLEMETAIRE DANS LE CAS DES ETUDES ET DES VISITES
            $service_prescription = new Service_Prescriptions;
            $service_dossier = new Service_Dossier;
            if($this->_getParam('do') == 'new'){
                if($this->_getParam("TYPE_DOSSIER") == 1 ){
                    $listePrescRegl = $service_prescription->getPrescriptions('etude');
                     $service_dossier->savePrescriptionRegl($idDossier,$listePrescRegl);
                }else if($this->_getParam("TYPE_DOSSIER") == 2 || $this->_getParam("TYPE_DOSSIER") == 3){
                    $listePrescRegl = $service_prescription->getPrescriptions('visite');
                    $service_dossier->savePrescriptionRegl($idDossier,$listePrescRegl);
                }
            }

            //Si le dossier est une levée de prescription ou de reserve on ajoute 5 "documents consultés" de type : Attestation de
            if($this->_getParam('do') == 'new' && ($idNature == 7 || $idNature == 19) ){
                $dbListeDocAjout = new Model_DbTable_ListeDocAjout();
                for($i = 0; $i <5; $i++){
                    $docAttestation = $dbListeDocAjout->createRow();
                    $docAttestation->LIBELLE_DOCAJOUT = "Attestation de";
                    $docAttestation->ID_NATURE = $idNature;
                    $docAttestation->ID_DOSSIER = $idDossier;
                    $docAttestation->save();
                }
            }
            //GESTION DE LA RECUPERATION DES DOCUMENTS CONSULTES DE LA PRECEDENTE VP SI IL EN EXISTE UNE (UNIQUEMENT EN CREATION DE DOSSIER)
            if ((21 == $idNature || 26 == $idNature) &&  $_POST['idEtablissement'] != "" && $this->_getParam('do') == 'new') {
                $lastVP = $DBdossier->findLastVp($this->_getParam("idEtablissement"));
                $idDossierLastVP = $lastVP['ID_DOSSIER'];
                if ($lastVP['ID_DOSSIER'] != '') {
                    $dblistedoc = new Model_DbTable_DossierListeDoc();
                    $dblistedocAjout = new Model_DbTable_ListeDocAjout();

                    //ici on récupère tous les documents qui ont été renseigné dans la base par un utilisateur (avec id du dossier et de la nature)
                    $listeDocRenseigne = $dblistedoc->recupDocDossier($idDossierLastVP,$idNature);

                    //ici on récupère tous les documents qui ont été ajoutés par l'utilisateur (document non proposé par défaut)
                    $listeDocAjout = $dblistedocAjout->getDocAjout($idDossierLastVP,$idNature);

                    //on copie les docrenseigne pour la nouvelle visite
                    $dbDocConsulte = new Model_DbTable_DossierDocConsulte();
                    foreach ($listeDocRenseigne as $val => $ue) {
                        $cpDocConsulte = $dbDocConsulte->createRow();
                        $cpDocConsulte->ID_NATURE = $idNature;
                        $cpDocConsulte->REF_CONSULTE = $ue['REF_CONSULTE'];
                        $cpDocConsulte->DATE_CONSULTE = $ue['DATE_CONSULTE'];
                        $cpDocConsulte->DOC_CONSULTE = $ue['DOC_CONSULTE'];
                        $cpDocConsulte->ID_DOSSIER = $idDossier;
                        $cpDocConsulte->ID_DOC = $ue['ID_DOC'];
                        $cpDocConsulte->save();
                    }

                    $dbListeDocAjout = new Model_DbTable_ListeDocAjout();
                    foreach ($listeDocAjout as $val => $ue) {
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

            if (isset($_POST['docManquant'])) {
                $docManquantArray = array();
                $dateDocManquantArray = array();
				$dateDocManquantRecepArray = array();

                if (isset($_POST['docManquant'])) {
                    foreach ($_POST['docManquant']  as $libelle => $value) {
                        if ($value != "") {
                            array_push($docManquantArray, $value);
                        }
                    }
                }

                if (isset($_POST['dateReceptionDocManquant'])) {
                    foreach ($_POST['dateReceptionDocManquant']  as $libelle => $value) {
                        if ($value != "") {
                            array_push($dateDocManquantRecepArray, $value);
                        }
                    }
                }

				if (isset($_POST['dateDocManquant'])) {
                    foreach ($_POST['dateDocManquant']  as $libelle => $value) {
                        if ($value != "") {
                            array_push($dateDocManquantArray, $value);
                        }
                    }
                }

                $nbDocParam = count($docManquantArray);
                $nbDateParam = count($dateDocManquantArray);

                $dbDossDocManquant = new Model_DbTable_DossierDocManquant();
                $cpt = 0;
                foreach ($docManquantArray  as $libelle => $value) {
					$docEnC = $dbDossDocManquant->getDocManquantDossNum($idDossier,$cpt);

                    if ($docEnC) {
                        $dossDocManquant = $dbDossDocManquant->find($docEnC['ID_DOCMANQUANT'])->current();
                        $dossDocManquant->DOCMANQUANT = $value;
                        if ($nbDateParam > 0 && $cpt < $nbDateParam) {
                            $dateTab = explode("/",$dateDocManquantArray[$cpt]);
                            $value = $dateTab[2]."-".$dateTab[1]."-".$dateTab[0];
                            $dossDocManquant->DATE_DOCSMANQUANT = $value;
							if(isset($dateDocManquantRecepArray[$cpt]) && $dateDocManquantRecepArray[$cpt] != NULL && $dateDocManquantRecepArray[$cpt] != ''){
								$dateTabRecep = explode("/",$dateDocManquantRecepArray[$cpt]);
								$valueRecep = $dateTabRecep[2]."-".$dateTabRecep[1]."-".$dateTabRecep[0];
								$dossDocManquant->DATE_RECEPTION_DOC = $valueRecep;
							}else{
								$dossDocManquant->DATE_RECEPTION_DOC = NULL;
							}
                        }
                        $dossDocManquant->save();
                    } elseif (!$docEnC) {
                        $dossDocManquant = $dbDossDocManquant->createRow();
                        $dossDocManquant->ID_DOSSIER = $idDossier;
                        $dossDocManquant->NUM_DOCSMANQUANT = $cpt;
                        $dossDocManquant->DOCMANQUANT = $value;
                        if ($nbDateParam > 0 && $cpt < $nbDateParam) {
                            $dateTab = explode("/",$dateDocManquantArray[$cpt]);
                            $value = $dateTab[2]."-".$dateTab[1]."-".$dateTab[0];
                            $dossDocManquant->DATE_DOCSMANQUANT = $value;
							if(isset($dateDocManquantRecepArray[$cpt])){
								$dateTabRecep = explode("/",$dateDocManquantRecepArray[$cpt]);
								$valueRecep = $dateTabRecep[2]."-".$dateTabRecep[1]."-".$dateTabRecep[0];
								$dossDocManquant->DATE_RECEPTION_DOC = $valueRecep;
							}
                        }
                        $dossDocManquant->save();
                    }

                    $cpt++;
                }
			}
			$nouveauDossier->INCOMPLET_DOSSIER = $_POST['INCOMPLET_DOSSIER'];
			$nouveauDossier->save();

            //lorsque je crée un nouveau dossier de VP pour un ERP qui a déjà été visité, il faudrait que les « éléments consultés » de base soient les mêmes
            //Sauvegarde des numéro de document d'urbanisme du dossier
            $DBdossierDocUrba = new Model_DbTable_DossierDocUrba();
            $where = $DBdossierDocUrba->getAdapter()->quoteInto('ID_DOSSIER = ?',  $idDossier);
            $DBdossierDocUrba->delete($where);

            if (isset($_POST['docUrba'])) {
                foreach ($_POST['docUrba']  as $libelle => $value) {
                    $saveDocUrba = $DBdossierDocUrba->createRow();
                    $saveDocUrba->ID_DOSSIER = $idDossier;
                    $saveDocUrba->NUM_DOCURBA = $value;
                    $saveDocUrba->save();
                }
            }

            //Sauvegarde des préventionnistes
            $DBdossierPrev = new Model_DbTable_DossierPreventionniste();
            $DBdossierPrev->delete("ID_DOSSIER = ".$idDossier);
            if (isset($_POST['preventionniste'])) {
                foreach ($_POST['preventionniste'] as $prev => $infos) {
                    $savePrev = $DBdossierPrev->createRow();
                    $savePrev->ID_DOSSIER = $idDossier;
                    $savePrev->ID_PREVENTIONNISTE = $infos;
                    $savePrev->save();
                }
            }

            //Sauvegarde des informations concernant l'affectation d'un dossier à une commission
            $dbDossierAffectation = new Model_DbTable_DossierAffectation();
            $dbDateComm = new Model_DbTable_DateCommission();
            if ($this->_getParam('COMMISSION_DOSSIER') == '') {
                $dbDossierAffectation->deleteDateDossierAffect($idDossier);
            } else {
                $listeDateDossAffect = $dbDossierAffectation->getDossierAffectAndType($idDossier);
                foreach ($listeDateDossAffect as $dateAffect) {
                    if (1 == $dateAffect['ID_COMMISSIONTYPEEVENEMENT']) {
                        //Comm en salle
                        $infosDateSalle = $dateAffect;
                    } elseif (2 == $dateAffect['ID_COMMISSIONTYPEEVENEMENT']) {
                        //Visite
                        $infosDateVisite = $dateAffect;
                    } elseif (3 == $dateAffect['ID_COMMISSIONTYPEEVENEMENT']) {
                        //Groupe de visite
                        $infosDateVisite = $dateAffect;
                    }
                }

                //Partie concernant la date de visite
                if ($this->_getParam('ID_AFFECTATION_DOSSIER_VISITE') && $this->_getParam('ID_AFFECTATION_DOSSIER_VISITE') != '') {
                    if (isset($infosDateVisite)) {
                        //la date de visite existe déjà on vérifie si elle a changé
                        if ($infosDateVisite['ID_DATECOMMISSION_AFFECT'] != $this->_getParam('ID_AFFECTATION_DOSSIER_VISITE')) {
                            //Dans le cas ou la date commission est différente de celle passée en paramètre alors on la met à jour
                            $dateEdit = $dbDossierAffectation->find($infosDateVisite['ID_DATECOMMISSION_AFFECT'],$idDossier)->current();
                            $dateEdit->ID_DATECOMMISSION_AFFECT = $this->_getParam('ID_AFFECTATION_DOSSIER_VISITE');
                            $dateEdit->save();
                        }
                    } else {
                        //la date de visite n'existe pas il faut donc la crééer.
                        $affectation = $dbDossierAffectation->createRow();
                        $affectation->ID_DATECOMMISSION_AFFECT = $this->_getParam('ID_AFFECTATION_DOSSIER_VISITE');
                        $affectation->ID_DOSSIER_AFFECT = $idDossier;
                        $affectation->save();
                    }
                    $dateCommDoss = $dbDateComm->find($this->_getParam('ID_AFFECTATION_DOSSIER_VISITE'))->current();
                    $nouveauDossier->DATEVISITE_DOSSIER = $dateCommDoss->DATE_COMMISSION;
                    $nouveauDossier->save();
                } else {
                    $nouveauDossier->DATEVISITE_DOSSIER = null;
                    $nouveauDossier->save();
                    //Supprimer l'affectation si elle existe
                    if (isset($infosDateVisite)) {
                        $dateDelete = $dbDossierAffectation->find($infosDateVisite['ID_DATECOMMISSION_AFFECT'],$idDossier)->current();
                        $dateDelete->delete();
                    }
                }

                //Partie concernant la date de commission
                if ($this->_getParam('ID_AFFECTATION_DOSSIER_COMMISSION') && $this->_getParam('ID_AFFECTATION_DOSSIER_COMMISSION') != '') {
                    if (isset($infosDateSalle)) {
                        //la date de commission existe déjà on vérifie si elle a changé
                        if ($infosDateSalle['ID_DATECOMMISSION_AFFECT'] != $this->_getParam('ID_AFFECTATION_DOSSIER_COMMISSION')) {
                            //Dans le cas ou la date commission est différente de celle passée en paramètre alors on la met à jour
                            $dateEdit = $dbDossierAffectation->find($infosDateSalle['ID_DATECOMMISSION_AFFECT'],$idDossier)->current();
                            $dateEdit->ID_DATECOMMISSION_AFFECT = $this->_getParam('ID_AFFECTATION_DOSSIER_COMMISSION');
                            $dateEdit->save();
                        }
                    } else {
                        //la date de commission n'existe pas il faut donc la crééer.
                        $affectation = $dbDossierAffectation->createRow();
                        $affectation->ID_DATECOMMISSION_AFFECT = $this->_getParam('ID_AFFECTATION_DOSSIER_COMMISSION');
                        $affectation->ID_DOSSIER_AFFECT = $idDossier;
                        $affectation->save();
                    }
                    $dateCommDoss = $dbDateComm->find($this->_getParam('ID_AFFECTATION_DOSSIER_COMMISSION'))->current();
                    $nouveauDossier->DATECOMM_DOSSIER = $dateCommDoss->DATE_COMMISSION;
                    $nouveauDossier->save();
                } else {
                    $nouveauDossier->DATECOMM_DOSSIER = null;
                    $nouveauDossier->save();
                    //Supprimer l'affectation si elle existe
                    if (isset($infosDateSalle)) {
                        $dateDelete = $dbDossierAffectation->find($infosDateSalle['ID_DATECOMMISSION_AFFECT'],$idDossier)->current();
                        $dateDelete->delete();
                    }
                }
            }
            //on envoi l'id à la vue pour qu'elle puisse rediriger vers la bonne page
            echo trim($idDossier);
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de la sauvegarde du dossier',
                'message' => $e->getMessage(),
            ));
        }
    }

//Autocomplétion pour selection ABREVIATION
    public function selectionabreviationAction()
    {
        if (isset($_GET['q'])) {
            $DBprescPrescType = new Model_DbTable_PrescriptionType();
            $this->view->selectAbreviation = $DBprescPrescType->fetchAll("ABREVIATION_PRESCRIPTIONTYPE LIKE '%".$_GET['q']."%'")->toArray();
        }
    }

//Autocomplétion pour selection ETABLISSEMENT
    public function selectionetabAction()
    {
        //$this->_helper->viewRenderer->setNoRender();
        // Création de l'objet recherche
        $search = new Model_DbTable_Search();

        // On set le type de recherche
        $search->setItem("etablissement");
        $search->limit(5);

        if (array_key_exists("ID_GENRE", $_GET)) {
            $search->setCriteria("genre.ID_GENRE", $this->_request->ID_GENRE + 1);
        }

        // On recherche avec le libellé
        $search->setCriteria("LIBELLE_ETABLISSEMENTINFORMATIONS", $this->_request->q, false);

        // On balance le résultat sur la vue
        //$this->view->resultats = $search->run()->getAdapter()->getItems(0, 99999999999)->toArray();
        $this->view->selectEtab = $search->run()->getAdapter()->getItems(0, 99999999999)->toArray();

        $service_etablissement = new Service_Etablissement();
        foreach ($this->view->selectEtab as $etab => $val) {
            $etablissementInfos = $service_etablissement->get($val['ID_ETABLISSEMENT']);
            //$this->view->selectEtab[$etab]['infosEtab'] = $etablissementInfos;
            if (isset($etablissementInfos['parents'][0]['LIBELLE_ETABLISSEMENTINFORMATIONS'])) {
                $this->view->selectEtab[$etab]['libelleParent'] = $etablissementInfos['parents'][0]['LIBELLE_ETABLISSEMENTINFORMATIONS'];
            } else {
                $this->view->selectEtab[$etab]['libelleParent'] = "";
            }
        }
    }

    //Action permettant de lister les établissements et les dossiers liés
    public function lieesAction()
    {
        $DBdossier = new Model_DbTable_Dossier();
        $this->view->infosDossier = $DBdossier->find((int) $this->_getParam("id"))->current();
        $this->view->listeEtablissement = $DBdossier->getEtablissementDossier((int) $this->_getParam("id"));

        $service_dossier = new Service_Dossier;
        if($this->_getParam("id")){
            $this->view->enteteEtab = $service_dossier->getEtabInfos($this->_getParam("id"));
        }

        $service_etablissement = new Service_Etablissement();
        foreach ($this->view->listeEtablissement    as $etab => $val) {
            $this->view->listeEtablissement[$etab]['pereInfos'] = $service_etablissement->get($val['ID_ETABLISSEMENT']);
        }
    }

    public function contactAction()
    {
        $this->view->idDossier = (int) $this->_getParam("id");
        $service_dossier = new Service_Dossier;
        if($this->_getParam("id")){
            $this->view->enteteEtab = $service_dossier->getEtabInfos($this->_getParam("id"));
        }
        $DBdossier = new Model_DbTable_Dossier();
        $this->view->infosDossier = $DBdossier->find((int) $this->_getParam("id"))->current();
    }

    //GESTION DOCUMENTS CONSULTES
    public function docconsulteAction()
    {
        $this->view->inlineScript()->appendFile('/js/dossier/dossierDocConsulte.js','text/javascript');

        //récupération du type de dossier (etude / visite)
        $service_dossier = new Service_Dossier;
        if($this->_getParam("id")){
            $this->view->enteteEtab = $service_dossier->getEtabInfos($this->_getParam("id"));
        }

        $dbdossier = new Model_DbTable_Dossier();
        $this->view->infosDossier = $dbdossier->find((int) $this->_getParam("id"))->current();

        $typeDossier = $this->view->infosDossier['TYPE_DOSSIER'];

        $dossierType = $dbdossier->getTypeDossier((int) $this->_getParam("id"));

        $this->view->idDossier = (int) $this->_getParam("id");

        //récupération de toutes les natures
        $DBdossierNature = new Model_DbTable_DossierNature();
        $this->view->listeNatures = $DBdossierNature->getDossierNaturesLibelle((int) $this->_getParam("id"));

        //suivant le type on récup la liste des docs que l'on met dans un tableau a multi dimension.
        //l'index de chaque liste sera l'id de la nature
        $dblistedoc = new Model_DbTable_DossierListeDoc();
        $dblistedocAjout = new Model_DbTable_ListeDocAjout();

        foreach ($this->view->listeNatures as $index => $nature) {
            if (2 == $dossierType['TYPE_DOSSIER'] || 3 == $dossierType['TYPE_DOSSIER']) {
                if (20 == $nature["ID_NATURE"] || 25 == $nature["ID_NATURE"]) {
                    //cas d'un groupe de visite d'une récption de travaux
                    $listeDocConsulte[$nature["ID_NATURE"]] = $dblistedoc->getDocVisiteRT();
                } elseif (47 == $nature["ID_NATURE"] || 48 == $nature["ID_NATURE"]) {
                    //cas d'une VAO
                    $listeDocConsulte[$nature["ID_NATURE"]] = $dblistedoc->getDocVisiteVAO();
                } else {
                    //cas général d'une visite
                    $listeDocConsulte[$nature["ID_NATURE"]] = $dblistedoc->getDocVisite();
                }
            } elseif (1 == $dossierType['TYPE_DOSSIER']) {
                //cas d'une etude
                $listeDocConsulte[$nature["ID_NATURE"]] = $dblistedoc->getDocEtude();
            } else {
                $listeDocConsulte = 0;
            }
            //ici on récupère tous les documents qui ont été renseigné dans la base par un utilisateur (avec id du dossier et de la nature)
            $listeDocRenseigne[$nature["ID_NATURE"]] = $dblistedoc->recupDocDossier($this->_getParam("id"),$nature["ID_NATURE"]);

            //ici on récupère tous les documents qui ont été ajoutés par l'utilisateur (document non proposé par défaut)
            $listeDocAjout[$nature["ID_NATURE"]] = $dblistedocAjout->getDocAjout((int) $this->_getParam("id"));

        }

        //On envoie à la vue la liste des documents consultés classés par nature (peux y avoir plusieurs fois la même liste)
        $this->view->listeDocs = $listeDocConsulte;
        //on envoie à la vue tous les documents qui ont été renseignés parmi la liste de ceux récupéré dans la boucle ci-dessus
        $this->view->dossierDocConsutle = $listeDocRenseigne;
        //on recup les docs ajouté pr le dossiers
        $this->view->listeDocsAjout = $listeDocAjout;

    }

    public function ajoutdocAction($idDossier)
    {
        try {
            $dblistedocajout = new Model_DbTable_ListeDocAjout();

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

            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'Le document a bien été ajouté',
                'message' => '',
            ));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de l\'ajout du document',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function validdocAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender();
            $id_dossier = (int) $this->_getParam("idDossier");
            $idValid = $this->_getParam("id");
            $datePost = $this->_getParam("date_".$idValid);

            if ('' == $id_dossier || '' == $idValid) {
                return false;
            }

            if ($datePost != "") {
                $dateTab = explode("/",$datePost);
                $date = $dateTab[2]."-".$dateTab[1]."-".$dateTab[0];
            } else {
                $date = "0000-00-00";
            }
            $ref = str_replace("\"","''",$_POST['ref_'.$idValid]);

            //on définit s'il sagid d'un doc ajouté ou nom
            $tabNom = explode("_",$idValid);

            if (count($tabNom) == 2) {
                $dblistedoc = new Model_DbTable_DossierDocConsulte();
                $listevalid = $dblistedoc->getGeneral($id_dossier,$tabNom[1]);

                $liste = $dblistedoc->find($listevalid['ID_DOSSIERDOCCONSULTE'])->current();
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
                //Cas d'une liste ajoutée Doc ajout
                //On commence par isoler l'id de "_aj"
                $idDocAjout = explode("_",$this->_getParam("id"));
                $dblistedocajout = new Model_DbTable_ListeDocAjout();

                $docAjout = $dblistedocajout->find($idDocAjout[1])->current();

                $docAjout->REF_DOCAJOUT = $ref;
                $docAjout->DATE_DOCAJOUT = $date;
                $docAjout->ID_DOSSIER = $id_dossier;

                $docAjout->save();
            }
        } catch (Exception $e) {
        }
    }

//GESTION LIAISON ETABLISSMENTS
    public function addetablissementAction()
    {
        try {
            $DBetablissementDossier = new Model_DbTable_EtablissementDossier();
            $newEtabDossier = $DBetablissementDossier->createRow();
            $newEtabDossier->ID_ETABLISSEMENT = $this->_getParam("idSelect");
            $newEtabDossier->ID_DOSSIER = $this->_getParam("idDossier");
            $newEtabDossier->save();

            $this->view->libelleEtab = $this->_getParam("libelleSelect");
            $this->view->infosEtab = $newEtabDossier;
            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'L\'établissement a bien été ajouté',
                'message' => '',
            ));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de l\'ajout de l\'établissement',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function deleteetablissementAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender();

            $DBetablissementDossier = new Model_DbTable_EtablissementDossier();
            //$deleteEtabDossier = $DBetablissementDossier->delete("ID_ETABLISSEMENTDOSSIER = " . $this->_getParam("idEtabDossier"));
            $deleteEtabDossier = $DBetablissementDossier->find($this->_getParam("idEtabDossier"))->current();
            $deleteEtabDossier->delete();

            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'L\'établissement a bien été supprimé',
                'message' => '',
            ));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de la suppression de l\'établissement',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function deleteliendossierAction()
    {
        try {
            //action appelée lorsque l'on supprime un lien avec un autre dossier
            $this->_helper->viewRenderer->setNoRender();

            $DBetablissementDossier = new Model_DbTable_DossierLie();
            $deleteEtabDossier = $DBetablissementDossier->find($this->_getParam("idLienDossier"))->current();
            $deleteEtabDossier->delete();

            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'Le lien avec le dossier a bien été supprimé',
                'message' => '',
            ));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de la suppression du lien avec ledossier',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function dialogcommshowAction()
    {
        $dbDateComm = new Model_DbTable_DateCommission();
        $infosDateComm = $dbDateComm->find($this->_getParam("idDateComm"))->current();
        $this->view->infosDateComm = $infosDateComm;

        $date = new Zend_Date($infosDateComm['DATE_COMMISSION'], Zend_Date::DATES);
        $this->view->dateSelect = $date->get(Zend_Date::WEEKDAY." ".Zend_Date::DAY_SHORT." ".Zend_Date::MONTH_NAME." ".Zend_Date::YEAR);

        $dbCommission = new Model_DbTable_Commission();
        $infosComm = $dbCommission->find($infosDateComm['COMMISSION_CONCERNE'])->current();
    }

    public function affectationodjAction()
    {
        $this->_helper->viewRenderer->setNoRender();
    }

    public function descriptifsAction()
    {
        $idDossier = (int) $this->_getParam("id");
        $DBdossier = new Model_DbTable_Dossier();
        $this->view->infosDossier = $DBdossier->find($idDossier)->current();
    }

//GENERATION DOCUMENTS
    public function dialoggenrapportAction()
    {
        $files = array();
        $dirname = REAL_DATA_PATH.DS."uploads".DS."documents";
        $dir = opendir($dirname);
        while ($file = readdir($dir)) {
            if (!in_array($file, array('.gitignore', '..', '.')) && !is_dir($dirname.DS.$file)) {
                $files[$dirname.DS.$file] = $file;
            }
        }
        closedir($dir);
        asort($files);
        $this->view->files = $files;

        //Permet de charger la liste des établissements liés au dossier pour la selection des rapports à generer
        $DBdossier = new Model_DbTable_Dossier();
        $this->view->listeEtablissement = $DBdossier->getEtablissementDossier((int) $this->_getParam("idDossier"));
    }

    public function generationrapportAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $listeEtab = $this->_getParam("idEtab");
        $idDossier = $this->_getParam("idDossier");

        foreach ($this->_getParam("idEtab") as $etablissementId) {
            $this->creationdocAction($idDossier,$etablissementId);
        }
    }

    public function creationdocAction($idDossier, $idEtab)
    {
        $this->view->idDossier = $idDossier;

        $this->view->fichierSelect = $this->_getParam("fichierSelect");

        $dateDuJour = new Zend_Date();
        $this->view->dateDuJour = $dateDuJour->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);

        /******
        /
        /RECUPERATIONS DES INFORMATIONS SUR L'ETABLISSEMENT
        /
        ******/

        $model_etablissement = new Model_DbTable_Etablissement();
        $etablissement = $model_etablissement->find($idEtab)->current();
        $this->view->etabDesc = $etablissement;

        $this->view->numWinPrev = $etablissement['NUMEROID_ETABLISSEMENT'];
        $this->view->numTelEtab = $etablissement['TELEPHONE_ETABLISSEMENT'];
        $this->view->numFaxEtab = $etablissement['FAX_ETABLISSEMENT'];
        $this->view->mailEtab = $etablissement['COURRIEL_ETABLISSEMENT'];

        //Informations de l'établissement (catégorie, effectifs, activité / type principal)
        $object_informations = $model_etablissement->getInformations($idEtab);
        $this->view->entite = $object_informations;
        //echo $this->view->entite['ID_ETABLISSEMENT'];

        $this->view->numPublic = $object_informations["EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"];
        $this->view->numPersonnel = $object_informations["EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS"];
        $this->view->numTotal = $object_informations["EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS"] + $object_informations["EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS"];

        $dbCategorie = new Model_DbTable_Categorie();
        if ($object_informations["ID_CATEGORIE"]) {
            $categorie = $dbCategorie->getCategories($object_informations["ID_CATEGORIE"]);
            $categorie = explode(" ",$categorie['LIBELLE_CATEGORIE']);
            $this->view->categorieEtab = $categorie[0];
        }

        $this->view->etablissementLibelle = $object_informations['LIBELLE_ETABLISSEMENTINFORMATIONS'];

        $model_typeactivite = new Model_DbTable_TypeActivite();
        $dbType = new Model_DbTable_Type();

        $lettreType = $dbType->find($object_informations['ID_TYPE'])->current();
        $this->view->typeLettreP = $lettreType['LIBELLE_TYPE'];

        $activitePrincipale = $model_typeactivite->find($object_informations["ID_TYPEACTIVITE"])->current();
        $this->view->libelleActiviteP = $activitePrincipale["LIBELLE_ACTIVITE"];

        // Types / activités secondaires
        $model_typesactivitessecondaire = new Model_DbTable_EtablissementInformationsTypesActivitesSecondaires();
        $array_types_activites_secondaires = $model_typesactivitessecondaire->fetchAll("ID_ETABLISSEMENTINFORMATIONS = ".$object_informations->ID_ETABLISSEMENTINFORMATIONS)->toArray();

        $idGenreEtab = $object_informations['ID_GENRE'];
        $dbGenre = new Model_DbTable_Genre();
        $infosGenre = $dbGenre->find($idGenreEtab)->current();
        $this->view->genreEtab = $infosGenre['LIBELLE_GENRE'];

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
        if (2 == $object_informations['ID_GENRE']) {
            //cas d'un établissement
            $this->view->GN = 2;
        } elseif (3 == $object_informations['ID_GENRE']) {
            //cas d'une céllule
            $this->view->GN = 3;
        }

        $dbEtabLie = new Model_DbTable_EtablissementLie();
        $etabLie = $dbEtabLie->recupEtabCellule($object_informations['ID_ETABLISSEMENT']);
        if ($etabLie != null) {
            $idPere = $etabLie[0]['ID_ETABLISSEMENT'];
            $this->view->infoPere = $model_etablissement->getInformations($idPere);
            $lettreType = $dbType->find($this->view->infoPere['ID_TYPE'])->current();
            $this->view->typeLettrePPere = $lettreType['LIBELLE_TYPE'];
            $activitePrincipale = $model_typeactivite->find($this->view->infoPere["ID_TYPEACTIVITE"])->current();
            $this->view->libelleActivitePPere = $activitePrincipale["LIBELLE_ACTIVITE"];
            $this->view->categorieEtabPere = $this->view->infoPere['ID_CATEGORIE'];
            //Récuperation du genre du pere
            $idGenrePere = $this->view->infoPere['ID_GENRE'];
            $infosGenrePere = $dbGenre->find($idGenrePere)->current();
            $this->view->genrePere = $infosGenrePere['LIBELLE_GENRE'];
        }

        // Adresses
        $model_adresse = new Model_DbTable_EtablissementAdresse();
        $array_adresses = $model_adresse->get($idEtab);

        if (count($array_adresses) > 0) {
            $this->view->communeEtab = $array_adresses[0]["LIBELLE_COMMUNE"];
            $adresse = "";
            if ($array_adresses[0]['NUMERO_ADRESSE'] != 0) {
                $adresse = $array_adresses[0]['NUMERO_ADRESSE']." ";
            }
            if ($array_adresses[0]["LIBELLE_RUE"] != '') {
                $adresse .= $array_adresses[0]["LIBELLE_RUE"]." ";
            }
            if ($array_adresses[0]["CODEPOSTAL_COMMUNE"] != '') {
                $adresse .= $array_adresses[0]["CODEPOSTAL_COMMUNE"]." ";
            }
            if ($array_adresses[0]["LIBELLE_COMMUNE"] != '') {
                $adresse .= strtoupper($array_adresses[0]["LIBELLE_COMMUNE"])." ";
            }
            $this->view->etablissementAdresse = $adresse;
        }

        /******
        /
        /RECUPERATIONS DES INFORMATIONS SUR LE DOSSIER
        /
        ******/

        //Récupération des documents d'urbanisme
        $DBdossierDocUrba = new Model_DbTable_DossierDocUrba();
        $dossierDocUrba = $DBdossierDocUrba->getDossierDocUrba($idDossier);
        $listeDocUrba = "";
        foreach ($dossierDocUrba as $var) {
            $listeDocUrba .= $var['NUM_DOCURBA'].", ";
        }

        $this->view->listeDocUrba = substr($listeDocUrba, 0, -2);

        //Récupération de tous les champs de la table dossier
        $DBdossier = new Model_DbTable_Dossier();
        $this->view->infosDossier = $DBdossier->find($idDossier)->current();
        //Récupération du type et de la nature du dossier
        $dbType = new Model_DbTable_DossierType();
        $typeDossier = $dbType->find($this->view->infosDossier['TYPE_DOSSIER'])->current();
        $this->view->typeDossier = $typeDossier['LIBELLE_DOSSIERTYPE'];

        $dbNature = new Model_DbTable_DossierNature();
        $natureDossier = $dbNature->getDossierNatureLibelle($idDossier);
        $this->view->natureDossier = $natureDossier['LIBELLE_DOSSIERNATURE'];

        //On récupère les informations du préventionniste
        $DBdossierPrev = new Model_DbTable_DossierPreventionniste();
        $this->view->preventionnistes = $DBdossierPrev->getPrevDossier($idDossier);

        $dbGroupement = new Model_DbTable_Groupement();
        $servInstructeur = "";
        $servInstructeurPrenomContact = "";
        $servInstructeurNomContact = "";
        $servInstructeurMail = "";

        if ($this->view->infosDossier["SERVICEINSTRUC_DOSSIER"]
                    && $this->view->infosDossier["TYPESERVINSTRUC_DOSSIER"]) {
            if ('servInstCommune' == $this->view->infosDossier["TYPESERVINSTRUC_DOSSIER"]) {
                $dbCommune = new Model_DbTable_AdresseCommune();
                $commune = $dbCommune->get($this->view->infosDossier["SERVICEINSTRUC_DOSSIER"]);
                if (isset($commune[0])) {
                    $idUtilisateur = $commune[0]["ID_UTILISATEURINFORMATIONS"];
                    $dbUtilisateur = new Model_DbTable_UtilisateurInformations();
                    $infos = $dbUtilisateur->find($idUtilisateur)->current();
                    $this->view->servInstructeur = $infos;
                    $servInstructeur = $this->view->infosDossier["SERVICEINSTRUC_DOSSIER"];
                    $servInstructeurPrenomContact = $infos['PRENOM_UTILISATEURINFORMATIONS'];
                    $servInstructeurNomContact = $infos['NOM_UTILISATEURINFORMATIONS'];
                    $servInstructeurMail = $infos['MAIL_UTILISATEURINFORMATIONS'];
                }
            } else {
                $libelle = $this->view->infosDossier["SERVICEINSTRUC_DOSSIER"];
                $groupement = $dbGroupement->getByLibelle($libelle);
                if (isset($groupement[0])) {
                    $servInstructeur = $groupement[0]['LIBELLE_GROUPEMENT'];
                    $servInstructeurPrenomContact = $groupement[0]['PRENOM_UTILISATEURINFORMATIONS'];
                    $servInstructeurNomContact = $groupement[0]['NOM_UTILISATEURINFORMATIONS'];
                    $servInstructeurMail = $groupement[0]['MAIL_UTILISATEURINFORMATIONS'];
                }
            }
        }
        $this->view->servInstructeur = $servInstructeur;
        $this->view->servInstructeurPrenomContact = $servInstructeurPrenomContact;
        $this->view->servInstructeurNomContact = $servInstructeurNomContact;
        $this->view->servInstructeurMail = $servInstructeurMail;

        $dbDossierContact = new Model_DbTable_DossierContact();
        //On recherche si un maitre d'oeuvre existe
        $contactInfos = $dbDossierContact->recupInfoContact($idDossier,4);
        if (count($contactInfos) == 1) {
            $this->view->maiteOeuvre = $contactInfos[0];
        } else {
            $contactInfos = $dbDossierContact->recupContactEtablissement($idEtab,4);
            if (count($contactInfos) > 0) {
                $this->view->maiteOeuvre = $contactInfos[0];
            }
        }

        $dbDossierContact = new Model_DbTable_DossierContact();
        //On recherche si un directeur unique de sécurité existe
        $contactInfos = $dbDossierContact->recupInfoContact($idDossier,8);
        if (count($contactInfos) == 1) {
            $this->view->dusDossier = $contactInfos[0];
        } else {
            $contactInfos = $dbDossierContact->recupContactEtablissement($idEtab,8);
            if (count($contactInfos) > 0) {
                $this->view->dusDossier = $contactInfos[0];
            }
        }

        //un exploitant existe
        $exploitantInfos = $dbDossierContact->recupInfoContact($idDossier,7);
        if (count($exploitantInfos) == 1) {
            $this->view->exploitantDossier = $exploitantInfos[0];
        } else {
            $contactInfos = $dbDossierContact->recupContactEtablissement($idEtab,7);
            if (count($contactInfos) > 0) {
                $this->view->exploitantDossier = $contactInfos[0];
            }
        }

        //un responsable de sécurité existe
        $respsecuInfos = $dbDossierContact->recupInfoContact($idDossier,9);
        if (count($respsecuInfos) == 1) {
            $this->view->respsecuDossier = $respsecuInfos[0];
        } else {
            $contactInfos = $dbDossierContact->recupContactEtablissement($idEtab,9);
            if (count($contactInfos) > 0) {
                $this->view->respsecuDossier = $contactInfos[0];
            }
        }

        //un proprietaire
        $proprioInfos = $dbDossierContact->recupInfoContact($idDossier,17);
        if (count($proprioInfos) == 1) {
            $this->view->proprioInfos = $proprioInfos[0];
        } else {
            $contactInfos = $dbDossierContact->recupContactEtablissement($idEtab,17);
            if (count($contactInfos) > 0) {
                $this->view->proprioInfos = $contactInfos[0];
            }
        }

        //Affichage dossier incomplet pour generation dossier incomplet
        //Recuperation des documents manquants dans le cas d'un dossier incomplet
        $dbDossDocManquant = new Model_DbTable_DossierDocManquant();
        $this->view->listeDocManquant = $dbDossDocManquant->getDocManquantDossLast($idDossier);

        $DBavisDossier = new Model_DbTable_Avis();
        $libelleAvis = $DBavisDossier->find($this->view->infosDossier["AVIS_DOSSIER"])->current();
        $this->view->avisDossier = $libelleAvis["LIBELLE_AVIS"];

        //Avis commission
        $libelleAvisCommission = $DBavisDossier->find($this->view->infosDossier["AVIS_DOSSIER_COMMISSION"])->current();
        $this->view->avisDossierCommission = $libelleAvisCommission["LIBELLE_AVIS"];

        $DBdossierCommission = new Model_DbTable_Commission();

        if ($this->view->infosDossier['COMMISSION_DOSSIER']) {
            $this->view->commissionInfos = $DBdossierCommission->find($this->view->infosDossier['COMMISSION_DOSSIER'])->current();
        } else {
            $this->view->commissionInfos = "Aucune commission";
        }

        if (1 == $this->view->infosDossier['INCOMPLET_DOSSIER']) {
            $this->view->etatDossier = "Incomplet";
        } else {
            $this->view->etatDossier = "Complet";
        }

        //récup de l'id de la piece jointe qu'aura le rapport
        $DBpieceJointe = new Model_DbTable_PieceJointe();
        $this->view->idRapportPj = $DBpieceJointe->maxPieceJointe();

        if (!isset($this->view->idRapportPj['MAX(ID_PIECEJOINTE)'])) {
            $this->view->idPieceJointe = 1;
        } else {
            $this->view->idPieceJointe = $this->view->idRapportPj['MAX(ID_PIECEJOINTE)'] + 1;
        }

        //récuperation de la date de passage en commission
        $dbAffectDossier = new Model_DbTable_DossierAffectation();
        $affectDossier = $dbAffectDossier->find(null,$idDossier)->current();
        $this->view->affectDossier = $affectDossier;

        //Concernant cette affectation on récupere les infos sur la commission (date aux différents format)
        $dbDateComm = new Model_DbTable_DateCommission();
        $dateComm = $dbDateComm->find($affectDossier['ID_DATECOMMISSION_AFFECT'])->current();
        if ($dateComm['DATE_COMMISSION'] != '') {
            $date = new Zend_Date($dateComm['DATE_COMMISSION'], Zend_Date::DATES);
            $this->view->dateCommEntete = $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
        }

        //Récupération de la (ou des) date(s) de visite
        //VISITE OU GROUPE DE VISITE
        $this->view->dateVisite = $this->view->infosDossier['DATEVISITE_DOSSIER'];
        //on récupère les date liées si il en existe
        //Une fois les infos de la date récupérées on peux aller chercher les date liées à cette commission pour les afficher
        $infosDateComm = $dbDateComm->find($affectDossier['ID_DATECOMMISSION_AFFECT'])->current();
        $this->view->ID_AFFECTATION_DOSSIER_VISITE = $infosDateComm['ID_DATECOMMISSION'];
        if (!$infosDateComm['DATECOMMISSION_LIEES']) {
            $commPrincipale = $affectDossier['ID_DATECOMMISSION_AFFECT'];
        } else {
            $commPrincipale = $infosDateComm['DATECOMMISSION_LIEES'];
        }
        //récupération de l'ensemble des dates liées
        $recupCommLiees = $dbDateComm->getCommissionsDateLieesMaster($commPrincipale);
        $nbDatesTotal = count($recupCommLiees);
        $nbDateDecompte = $nbDatesTotal;

        $listeDateInput = "";
        $listeHeureInput = array();

        foreach ($recupCommLiees as  $val => $ue) {
            $date = new Zend_Date($ue['DATE_COMMISSION'], Zend_Date::DATES);

            if ($nbDateDecompte == $nbDatesTotal) {
                //premiere date = date visite donc on renseigne l'input hidden correspondant avec l'id de cette date
                $this->view->idDateVisiteAffect = $ue['ID_DATECOMMISSION'];
            }
            if ($nbDateDecompte > 1) {
                $listeDateInput .= $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR).", ";
            } elseif (1 == $nbDateDecompte) {
                $listeDateInput .= $date->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
            }
            $listeHeureInput[] = substr($ue['HEUREDEB_COMMISSION'], 0, 5). ' à ' .substr($ue['HEUREFIN_COMMISSION'], 0, 5);

            $this->view->dateVisiteInput = $listeDateInput;
            $nbDateDecompte--;
        }
        $this->view->dateVisite = $this->view->dateVisiteInput;
        $this->view->heureVisite = implode(', ', $listeHeureInput);

        //PARTIE DOC CONSULTE

            //récupération du type de dossier (etude / visite)
            $dbdossier = new Model_DbTable_Dossier();
        $dossierType = $dbdossier->getTypeDossier((int) $idDossier);
        $dossierNature = $dbdossier->getNatureDossier((int) $idDossier);

            //suivant le type on récup la liste des docs
            $dblistedoc = new Model_DbTable_DossierListeDoc();

        if (2 == $dossierType['TYPE_DOSSIER'] || 3 == $dossierType['TYPE_DOSSIER']) {
            if (20 == $dossierNature["ID_NATURE"] || 25 == $dossierNature["ID_NATURE"]) {
                //cas d'un groupe de visite d'une récption de travaux
                    $listeDocConsulte = $dblistedoc->getDocVisiteRT();
            } elseif (47 == $dossierNature["ID_NATURE"] || 48 == $dossierNature["ID_NATURE"]) {
                //cas d'une VAO
                    $listeDocConsulte = $dblistedoc->getDocVisiteVAO();
            } else {
                $listeDocConsulte = $dblistedoc->getDocVisite();
            }
        } elseif (1 == $dossierType['TYPE_DOSSIER']) {
            $listeDocConsulte = $dblistedoc->getDocEtude();
        } else {
            $listeDocConsulte = 0;
        }

            //on envoi la liste de base à la vue
            $this->view->listeDocs = $listeDocConsulte;

            //on recup les docs ajouté pr le dossiers
            $dblistedocAjout = new Model_DbTable_ListeDocAjout();
        $listeDocAjout = $dblistedocAjout->getDocAjout((int) $idDossier,$dossierNature['ID_NATURE']);
        $this->view->listeDocsAjout = $listeDocAjout;

        $this->view->dossierDocConsutle = $dblistedoc->recupDocDossier((int) $idDossier,$dossierNature['ID_NATURE']);

        /*
        PARTIE PRESCRIPTION
        */
        $service_dossier = new Service_Dossier();
        $this->view->prescriptionReglDossier = $service_dossier->getPrescriptions((int) $idDossier,0);
        $this->view->prescriptionExploitation = $service_dossier->getPrescriptions((int) $idDossier,1);
        $this->view->prescriptionAmelioration = $service_dossier->getPrescriptions((int) $idDossier,2);

        //on affiche les prescriptions du dossier
        /*
        $dbPrescDossier = new Model_DbTable_PrescriptionDossier();
        $listePrescDossier = $dbPrescDossier->recupPrescDossier($this->_getParam('idDossier'));
        $dbPrescDossierAssoc = new Model_DbTable_PrescriptionDossierAssoc();

        $prescriptionArray = array();
        foreach ($listePrescDossier as $val => $ue) {
            if ($ue['ID_PRESCRIPTION_TYPE']) {
                //cas d'une prescription type
                    $assoc = $dbPrescDossierAssoc->getPrescriptionTypeAssoc($ue['ID_PRESCRIPTION_TYPE'],$ue['ID_PRESCRIPTION_DOSSIER']);
                array_push($prescriptionArray, $assoc);
            } else {
                //cas d'une prescription particulière
                    $assoc = $dbPrescDossierAssoc->getPrescriptionDossierAssoc($ue['ID_PRESCRIPTION_DOSSIER']);
                array_push($prescriptionArray, $assoc);
            }
        }
        //Zend_Debug::dump($prescriptionArray);
        $this->view->prescriptionDossier = $prescriptionArray;
        */

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
        $DBpieceJointe = new Model_DbTable_PieceJointe();
        $nouvellePJ = $DBpieceJointe->createRow();
        $nouvellePJ->ID_PIECEJOINTE = $this->view->idPieceJointe;
        $nouvellePJ->NOM_PIECEJOINTE = substr(basename($this->view->fichierSelect), 0, strlen(basename($this->view->fichierSelect)) - 3);
        $nouvellePJ->EXTENSION_PIECEJOINTE = ".odt";
        $nouvellePJ->DESCRIPTION_PIECEJOINTE = "Rapport de l'établissement ".$object_informations['LIBELLE_ETABLISSEMENTINFORMATIONS']." généré le ".$dateDuJour->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR)." à ".$dateDuJour->get(Zend_Date::HOUR.":".Zend_Date::MINUTE);
        $nouvellePJ->DATE_PIECEJOINTE = $dateDuJour->get(Zend_Date::YEAR."-".Zend_Date::MONTH."-".Zend_Date::DAY);
        $nouvellePJ->save();

        $this->view->nouvellePJ = $nouvellePJ;

        $this->view->store = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('dataStore');
        $url =  $this->getHelper('url')->url(array('controller' => 'piece-jointe', 'id' => $idDossier, 'action' => 'get', 'idpj' => $nouvellePJ['ID_PIECEJOINTE'], 'type' => 'dossier'));
        echo "<a href='".$url."'>Ouvrir le rapport de l'établissement : ".$object_informations['LIBELLE_ETABLISSEMENTINFORMATIONS']."<a/><br/><br/>";

        $DBsave = new Model_DbTable_DossierPj();
        $linkPj = $DBsave->createRow();
        $linkPj->ID_DOSSIER = $idDossier;
        $linkPj->ID_PIECEJOINTE = $nouvellePJ->ID_PIECEJOINTE;
        $linkPj->save();

        /*
        PARTIE TEXTES APPLICABLES
        */
        //on recupere tout les textes applicables qui ont été cochés dans le dossier
        $dbDossierTextesAppl = new Model_DbTable_DossierTextesAppl();
        $this->view->listeTextesAppl = $dbDossierTextesAppl->recupTextesDossierGenDoc($this->_getParam('idDossier'));

        /*
        DATE DE LA DERNIERE VISITE PERIODIQUE
        */
        $dateVisite = $this->view->infosDossier["DATEVISITE_DOSSIER"];
        if ($dateVisite != '' && isset($dateVisite)) {
            $dateLastVP = $DBdossier->findLastVpCreationDoc($idEtab,$idDossier,$dateVisite);

            if ($dateLastVP['maxdate'] != NULL) {
                $ZendDateLastVP = new Zend_Date($dateLastVP['maxdate'], Zend_Date::DATES);
                $this->view->dateLastVP = $ZendDateLastVP->get(Zend_Date::DAY."/".Zend_Date::MONTH."/".Zend_Date::YEAR);
                $avisLastVP =  $DBdossier->getAvisDossier($dateLastVP['ID_DOSSIER']);
                $this->view->avisLastVP = $avisLastVP['LIBELLE_AVIS'];
            } else {
                $this->view->dateLastVP = null;
            }
        }

        $this->render('creationdoc');
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
            $DBdossier = new Model_DbTable_Dossier();
            $this->view->infosDossier = $DBdossier->find($idDossier)->current();

            $DBdossierNature = new Model_DbTable_DossierNature();
            $this->view->natureConcerne = $DBdossierNature->getDossierNaturesLibelle($idDossier);
        }

        if ($this->_request->DESCRIPTIF_DOSSIER) {
            $DBdossier = new Model_DbTable_Dossier();
            $dossier = $DBdossier->find($this->_request->id)->current();
            $dossier->DESCRIPTIF_DOSSIER = $this->_request->DESCRIPTIF_DOSSIER;
            $dossier->save();

            $this->_helper->_redirector("descriptif", $this->_request->getControllerName(), null, array("id" => $this->_request->id));
        }
    }

    public function textesApplicablesAction()
    {
        $this->_helper->layout->setLayout('dossier');

        $service_dossier = new Service_Dossier();

        if($this->_getParam("id")){
            $this->view->enteteEtab = $service_dossier->getEtabInfos($this->_getParam("id"));
        }

        $this->view->textes_applicables_dossier = $service_dossier->getAllTextesApplicables($this->_request->id);
    }

    public function editTextesApplicablesAction()
    {
        $service_dossier = new Service_Dossier();
        $service_textes_applicables = new Service_TextesApplicables();

        $this->view->textes_applicables_dossier = $service_dossier->getAllTextesApplicables($this->_request->id);
        $this->view->textes_applicables = $service_textes_applicables->getAll();

        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                $service_dossier->saveTextesApplicables($this->_request->id, $post['textes_applicables']);
                $this->_helper->flashMessenger(array('context' => 'success','title' => 'Mise à jour réussie !','message' => 'Les textes applicables ont bien été mis à jour.'));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => 'Mise à jour annulée','message' => 'Les textes applicables n\'ont pas été mis à jour. Veuillez rééssayez. ('.$e->getMessage().')'));
            }

            $this->_helper->redirector('textes-applicables', null, null, array('id' => $this->_request->id));
        }
    }

//GESTION DE LA PARTIE PRESCRIPTION
    public function emplacementAction()
    {
        $this->view->categorie = $this->_getParam('PRESCRIPTIONTYPE_CATEGORIE');
        $this->view->texte = $this->_getParam('PRESCRIPTIONTYPE_TEXTE');
        $this->view->article = $this->_getParam('PRESCRIPTIONTYPE_ARTICLE');

        if (!$this->view->categorie && !$this->view->texte && !$this->view->article) {
            //on affiche les catégories
            $dbPrescriptionCat = new Model_DbTable_PrescriptionCat();
            $listePrescriptionCat = $dbPrescriptionCat->recupPrescriptionCat();
            $this->view->categorieListe = $listePrescriptionCat;
        } elseif (!$this->view->texte && !$this->view->article) {
            $dbPrescriptionCat = new Model_DbTable_PrescriptionCat();
            $categorieLibelle = $dbPrescriptionCat->find($this->view->categorie)->current()->toArray();
            $this->view->categorieLibelle = $categorieLibelle['LIBELLE_PRESCRIPTION_CAT'];
            //on viens de choisir une catégorie il faut afficher les texte de la catégorie
            $dbTexte = new Model_DbTable_PrescriptionTexte();
            $this->view->texteListe = $dbTexte->recupPrescriptionTexte($this->_getParam('PRESCRIPTIONTYPE_CATEGORIE'));
        } elseif (!$this->view->article) {
            $dbPrescriptionCat = new Model_DbTable_PrescriptionCat();
            $categorieLibelle = $dbPrescriptionCat->find($this->view->categorie)->current()->toArray();
            $this->view->categorieLibelle = $categorieLibelle['LIBELLE_PRESCRIPTION_CAT'];
            $dbTexte = new Model_DbTable_PrescriptionTexte();
            $texteLibelle = $dbTexte->find($this->view->texte)->current()->toArray();
            $this->view->texteLibelle = $texteLibelle['LIBELLE_PRESCRIPTIONTEXTE'];
            //on viens de choisir un texte il faut afficher les articles
            $dbArticle = new Model_DbTable_PrescriptionArticle();
            $this->view->texteArticle = $dbArticle->recupPrescriptionArticle($this->_getParam('PRESCRIPTIONTYPE_TEXTE'));
        } else {
            $dbPrescriptionCat = new Model_DbTable_PrescriptionCat();
            $categorieLibelle = $dbPrescriptionCat->find($this->view->categorie)->current()->toArray();
            $this->view->categorieLibelle = $categorieLibelle['LIBELLE_PRESCRIPTION_CAT'];

            $dbTexte = new Model_DbTable_PrescriptionTexte();
            $texteLibelle = $dbTexte->find($this->view->texte)->current()->toArray();
            $this->view->texteLibelle = $texteLibelle['LIBELLE_PRESCRIPTIONTEXTE'];

            $dbArticle = new Model_DbTable_PrescriptionArticle();
            $articleLibelle = $dbArticle->find($this->view->article)->current()->toArray();
            $this->view->articleLibelle = $articleLibelle['LIBELLE_PRESCRIPTIONARTICLE'];
        }
    }

    public function prescriptionAction()
    {
        $service_dossier = new Service_Dossier();
        if($this->_getParam("id")){
            $this->view->enteteEtab = $service_dossier->getEtabInfos($this->_getParam("id"));
        }
        if ($this->_request->isPost()) {
            try {
                $post = $this->_request->getPost();
                if ('edit' == $post['action'] || 'edit-type' == $post['action']) {
                    $service_dossier->savePrescription($post);
                    $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Enregistrement effectué.', 'message' => 'La prescription a bien été modifiée'));
                } elseif ('presc-add' == $post['action']) {
                    $service_dossier->savePrescription($post);
                    $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Enregistrement effectué.', 'message' => 'La prescription a bien été ajoutée'));
                } elseif ('delete' == $post['action']) {
                    $service_dossier->deletePrescription($post);
                    $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Suppression effectué.', 'message' => 'La prescription a bien été supprimée'));
                }
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error', 'title' => 'Erreur lors de l\'enregistrement.', 'message' => 'Une erreur s\'est produite lors de l\enregistrement de la prescription ('.$e->getMessage().')'));
            }
        }

        $this->view->id_dossier = $this->_getParam("id");
		$DbDossier = new Model_DbTable_Dossier();
		$this->view->infosDossier = $DbDossier->find((int) $this->view->id_dossier)->current();

        //$this->view->prescriptionDossier = $service_dossier->getPrescriptions((int) $this->_getParam("id"));
        $this->view->prescriptionReglDossier = $service_dossier->getPrescriptions((int) $this->_getParam("id"),0);
        $this->view->prescriptionExploitation = $service_dossier->getPrescriptions((int) $this->_getParam("id"),1);
        $this->view->prescriptionAmelioration = $service_dossier->getPrescriptions((int) $this->_getParam("id"),2);
    }

    public function prescriptionwordsearchAction()
    {
        if ($this->_getParam('motsCles')) {
            $tabMotCles = explode(" ", $this->_getParam('motsCles'));
            $dbPrescType = new Model_DbTable_PrescriptionType();
            $listePrescType = $dbPrescType->getPrescriptionTypeByWords($tabMotCles);

            $dbPrescAssoc = new Model_DbTable_PrescriptionTypeAssoc();
            $prescriptionArray = array();
            foreach ($listePrescType as $val => $ue) {
                $assoc = $dbPrescAssoc->getPrescriptionAssoc($ue['ID_PRESCRIPTIONTYPE']);
                if(count($assoc) > 0)
                    array_push($prescriptionArray, $assoc);
            }
            $this->view->prescriptionType = $prescriptionArray;
        }
    }

    public function prescriptionAddAction()
    {
        $this->_forward('prescription-edit');
    }

    public function prescriptiontypeformAction()
    {
        $this->showprescriptionTypeAction(0,0,0);
    }

    public function showprescriptionTypeAction($categorie,$texte,$article)
    {
        $dbPrescType = new Model_DbTable_PrescriptionType();
        $listePrescType = $dbPrescType->getPrescriptionType($categorie,$texte,$article);

        $dbPrescAssoc = new Model_DbTable_PrescriptionTypeAssoc();
        $prescriptionArray = array();

        foreach ($listePrescType as $val => $ue) {
            $assoc = $dbPrescAssoc->getPrescriptionAssoc($ue['ID_PRESCRIPTIONTYPE']);
            array_push($prescriptionArray, $assoc);
        }

        $this->view->prescriptionType = $prescriptionArray;
    }

    public function prescriptionEditAction()
    {
        $id_dossier = $this->_getParam('id');
        $id_prescription = $this->_getParam('id-prescription');

        $this->view->id_dossier = $id_dossier;
        $this->view->id_prescription = $id_prescription;

        //On envoi à la vue l'ensemble des textes et articles
        $dbTexte = new Model_DbTable_PrescriptionTexteListe();
        $this->view->listeTextes = $dbTexte->getAllTextes(1);
        $dbArticle = new Model_DbTable_PrescriptionArticleListe();
        $this->view->listeArticles = $dbArticle->getAllArticles(1);

        if (isset($id_prescription)) {
            $service_dossier = new Service_Dossier();
            $this->view->infosPrescription = $service_dossier->getDetailPrescription($id_prescription);

            if (NULL == $this->view->infosPrescription['ID_PRESCRIPTION_TYPE']) {
                $this->view->action = 'edit';
            } elseif ($this->view->infosPrescription['ID_PRESCRIPTION_TYPE'] != NULL) {
                $this->view->action = 'edit-type';
            }
        } else {
            $this->view->action = 'presc-add';
        }
    }

    public function prescriptionshowemplacementAction()
    {
        $this->view->categorie = $this->_getParam('PRESCRIPTIONTYPE_CATEGORIE');
        $this->view->texte = $this->_getParam('PRESCRIPTIONTYPE_TEXTE');
        $this->view->article = $this->_getParam('PRESCRIPTIONTYPE_ARTICLE');
        if (!$this->view->categorie && !$this->view->texte && !$this->view->article) {
            $this->showprescriptionTypeAction(0,0,0);
        } elseif (!$this->view->texte && !$this->view->article) {
            $this->showprescriptionTypeAction($this->view->categorie,0,0);
        } elseif (!$this->view->article) {
            $this->showprescriptionTypeAction($this->view->categorie,$this->view->texte,0);
        } else {
            $this->showprescriptionTypeAction($this->view->categorie,$this->view->texte,$this->view->article);
        }
    }

    public function prescriptionaddtypeAction()
    {
        $idPrescType = $this->_getParam('idPrescType');
        $idDossier = $this->_getParam('idDossier');
        $this->view->typePrescDossier = $this->_getParam('typePrescriptionDossier');

        //on recup le num max de prescription du dossier
        $dbPrescDossier = new Model_DbTable_PrescriptionDossier();
        $numMax = $dbPrescDossier->recupMaxNumPrescDossier($idDossier, $this->_getParam('typePrescriptionDossier'));
        $num = $numMax['maxnum'];

        if (NULL == $numMax['maxnum']) {
            //premiere prescription que l'on ajoute
            $num = 1;
        } else {
            $num++;
        }

        $newPrescDossier = $dbPrescDossier->createRow();
        $newPrescDossier->ID_DOSSIER = $idDossier;
        $newPrescDossier->NUM_PRESCRIPTION_DOSSIER = $num;
        $newPrescDossier->ID_PRESCRIPTION_TYPE = $idPrescType;
        $newPrescDossier->TYPE_PRESCRIPTION_DOSSIER = $this->_getParam('typePrescriptionDossier');
        $newPrescDossier->save();

        $this->view->idPrescriptionDossier = $newPrescDossier->ID_PRESCRIPTION_DOSSIER;

        //On recupere les informations de la prescription type pour l'afficher dans la liste
        $dbPrescTypeAssoc = new Model_DbTable_PrescriptionTypeAssoc();
        $prescType = $dbPrescTypeAssoc->getPrescriptionAssoc($idPrescType);
        $texteArray = array();
        $articleArray = array();

        foreach ($prescType as $libelle => $value) {
            array_push($articleArray, $value['LIBELLE_ARTICLE']);
            array_push($texteArray, $value['LIBELLE_TEXTE']);
            $this->view->libelle = $value['PRESCRIPTIONTYPE_LIBELLE'];
        }


        $this->view->numPresc = $num;
        $this->view->textes = $texteArray;
        $this->view->articles = $articleArray;

        $nbPresc = 1;
        $listeExploit = $dbPrescDossier->recupPrescDossier($idDossier, 1);
        foreach($listeExploit as $prescDossier){
            $prescCount = $dbPrescDossier->find($prescDossier['ID_PRESCRIPTION_DOSSIER'])->current();
            $prescCount->NUM_PRESCRIPTION_DOSSIER = $nbPresc;
            $prescCount->save();
            $nbPresc++;
        }

        $listeAmelio = $dbPrescDossier->recupPrescDossier($idDossier, 2);
        foreach($listeAmelio as $prescDossier){
            $prescCount = $dbPrescDossier->find($prescDossier['ID_PRESCRIPTION_DOSSIER'])->current();
            $prescCount->NUM_PRESCRIPTION_DOSSIER = $nbPresc;
            $prescCount->save();
            $nbPresc++;
        }

    }

    public function prescriptionchangeposAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $stringUpdateReg = $this->_getParam('tableUpdateReg');
        $tabIdReg = explode(",",$stringUpdateReg);

        $stringUpdate = $this->_getParam('tableUpdate');
        $tabId = explode(",",$stringUpdate);


        $service_dossier = new Service_Dossier();
        $service_dossier->changePosPrescription($tabIdReg);
        $service_dossier->changePosPrescription($tabId);
    }

    public function formrecupprescriptionAction()
    {
        //récupération de l'établissement attaché au dossier
        $dbEtabDossier = new Model_DbTable_EtablissementDossier();
        $listeEtab = $dbEtabDossier->getEtablissementListe($this->_getParam('idDossier'));

        $this->view->nbEtab = count($listeEtab);
        $this->view->idDossier = $this->_getParam('idDossier');

        if (1 == $this->view->nbEtab) {
            //si il n'y a qu'un établissement, on affiche la liste des dossiers qu'il contient
            $service_etablissement = new Service_Etablissement();
            $dossiers = $service_etablissement->getDossiers($listeEtab['0']["ID_ETABLISSEMENT"]);
            $this->view->etudes = $dossiers['etudes'];
            $this->view->visites = $dossiers['visites'];
            $this->view->autres = $dossiers['autres'];
        }
    }

    public function recupprescriptionAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        //On reprend les prescriptions du dossier ayant id : dossierSelect pui on les ajoute au dossier ayant id : idDossier

        $service_dossier = new Service_Dossier();

        $prescriptionExploitation = $service_dossier->getPrescriptions((int) $this->_getParam("dossierSelect"),1);
        $service_dossier->copyPrescriptionDossier($prescriptionExploitation,(int) $this->_getParam("idDossier"));

        $prescriptionAmelioration = $service_dossier->getPrescriptions((int) $this->_getParam("dossierSelect"),2);
        $service_dossier->copyPrescriptionDossier($prescriptionAmelioration,(int) $this->_getParam("idDossier"));

    }

    public function lienmultipleAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        foreach ($this->_getParam('etabId') as $val) {
            try {
                $DBetablissementDossier = new Model_DbTable_EtablissementDossier();
                $newEtabDossier = $DBetablissementDossier->createRow();
                $newEtabDossier->ID_ETABLISSEMENT = $val;
                $newEtabDossier->ID_DOSSIER = $this->_getParam("idDossier");
                $newEtabDossier->save();

                $this->_helper->flashMessenger(array(
                    'context' => 'success',
                    'title' => 'L\'établissement a bien été ajouté',
                    'message' => '',
                ));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array(
                    'context' => 'error',
                    'title' => 'Erreur lors de l\'ajout de l\'établissement',
                    'message' => $e->getMessage(),
                ));
            }
        }
    }

//GESTION DE LA VERROUILLAGE
    public function verrouAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $DBdossier = new Model_DbTable_Dossier();
        $lockDosier = $DBdossier->find($this->_getParam('idDossier'))->current();
        $lockDosier->VERROU_DOSSIER = 1;
        $lockDosier->VERROU_USER_DOSSIER = $this->_getParam('ID_CREATEUR');
        $lockDosier->save();
        echo $lockDosier->ID_DOSSIER;
    }

    public function deverrouAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $DBdossier = new Model_DbTable_Dossier();
        $lockDosier = $DBdossier->find($this->_getParam('idDossier'))->current();
        $lockDosier->VERROU_DOSSIER = 0;
        $lockDosier->VERROU_USER_DOSSIER = null;
        $lockDosier->save();
        echo $lockDosier->ID_DOSSIER;
    }
}
