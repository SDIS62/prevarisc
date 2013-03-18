<?php

    class AdminController extends Zend_Controller_Action
    {
        public function init()
        {
            //$this->_helper->layout->setLayout("admin");
            $this->view->action = $this->_request->getActionName();

            // On check si l'utilisateur peut accéder à cette partie
            if($this->_helper->Droits()->get()->DROITADMINSYS_GROUPE == 0)
                $this->_helper->Droits()->redirect();
        }

        // Accueil forwardé vers groupement de communes
        public function indexAction()
        {
            $this->view->title = "Administration système";

            // Modèle de données
            $model_admin = new Model_DbTable_Admin;
            $this->view->info = $model_admin->getParams();
        }

        public function saveAction()
        {
            // Modèle de données
            $model_admin = new Model_DbTable_Admin;

            $infos = $model_admin->find(1)->current();
            $infos->LDAP_ACTIF = 0;
            $infos->LDAP_LOGIN = 0;
            $infos->setFromArray(array_intersect_key($_POST, $model_admin->info('metadata')))->save();
            $this->_redirect("/admin");
        }

        // Sauvegarde de la base de données
        public function backupAction()
        {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();

            $bootstrap = $this->getInvokeArg("bootstrap");
            $config = $bootstrap->getOptions();

            $host = $config["resources"]["db"]["params"]["host"];
            $user = $config["resources"]["db"]["params"]["username"];
            $pass = $config["resources"]["db"]["params"]["password"]; // On définit les infos de la base de données
            $db = $config["resources"]["db"]["params"]["dbname"];
            $date = date("d-m-Y"); // On définit le variable $date (ici, son format)

            $backup = $db."bdd-backup_".$date.".sql.gz";

            // Utilise les fonctions système : MySQLdump & GZIP pour générer un backup gzipé
            system("mysqldump -h$host -u$user -p$pass $db | gzip> $backup");

            // Démarre la procédure de téléchargement
            $taille = filesize($backup);
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Type: application/gzip");
            header("Content-Disposition: attachment; filename=$backup;");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: ".$taille);
            @readfile($backup);

            // Supprime le fichier temporaire du serveur
            unlink($backup);

        }
    }
