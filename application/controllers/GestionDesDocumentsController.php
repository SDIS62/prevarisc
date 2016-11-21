<?php

class GestionDesDocumentsController extends Zend_Controller_Action
{
    public $path;

    public function init()
    {
        $this->path = REAL_DATA_PATH . DS . "uploads" . DS . "documents";

        // Actions à effectuées en AJAX
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('check', 'json')
            ->initContext();
    }

    public function indexAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        $service_commission = new Service_Commission;

        $liste_commission = $service_commission->getAll();


        //Récupération des documents présents dans le dossier 0. Documents visibles après vérrouillage
        $pathVer = $this->path."/0";
        $dirVer = opendir($pathVer) or die('Erreur de listage : le répertoire n\'existe pas');
        $fichierVer = array();
        $dossierVer = array();
        while ($elementVer = readdir($dirVer)) {
            if ($elementVer != '.' && $elementVer != '..') {
                if($elementVer != '.gitignore')
                    if (!is_dir($pathVer.DS.$elementVer)) {$fichierVer[] = $elementVer;} else {$dossierVer[] = $elementVer;}
            }
        }
        closedir($dirVer);
        sort($fichierVer);

        $this->view->fichierVer = $fichierVer;
        
        //Récupération de l'ensemble des documents des différentes commissions
        foreach($liste_commission as $var => $commission ){
            $path = $this->path. DS .$commission['ID_COMMISSION'];
            $dir = opendir($path) or die('Erreur de listage : le répertoire n\'existe pas'); // on ouvre le contenu du dossier courant
            $fichier= array(); // on déclare le tableau contenant le nom des fichiers
            $dossier= array(); // on déclare le tableau contenant le nom des dossiers

            while ($element = readdir($dir)) {
                if ($element != '.' && $element != '..') {
                    if($element != '.gitignore')
                        if (!is_dir($path.DS.$element)) {$fichier[] = $element;} else {$dossier[] = $element;}
                }
            }
            closedir($dir);
            sort($fichier);

            $liste_commission[$var]['listeFichier'] = $fichier;
        }

        $this->view->path = DATA_PATH . "/uploads/documents";
        $this->view->liste_commission = $liste_commission;
    }

    public function formAction()
    {
        $service_commission = new Service_Commission;

        $this->view->liste_commission = $service_commission->getAll();
    }

    public function addAction()
    {
        try {
            
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            
            // Extension du fichier
            $filename = str_replace(DS, '', $_FILES['fichier']['name']);
            $extension = strtolower(strrchr($filename, "."));
            if (!in_array($extension, array('.odt'))) {
                throw new Exception("Seuls les fichiers .odt sont autorisés en upload.");
            }
            
            //Si besoin verificaiton de l'extension du fichier (uniquement odt)
            if (!move_uploaded_file($_FILES['fichier']['tmp_name'], $this->path . DS. $this->_getParam('commission') .DS. $filename)) {
                throw new Exception('Impossible de déplacer le fichier uploadé');
            }
            $this->_helper->flashMessenger(array(
                'context' => 'success',
                'title' => 'Le document a bien été ajouté',
                'message' => ''
            ));
            
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de l\'ajout du document',
                'message' => $e->getMessage()
            ));
        }
        echo '
            <script type="text/javascript">
                window.top.window.location.reload();
            </script>
        ';
    }

    public function suppdocAction()
    {
        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();

            $path = $this->path.DS.$this->_getParam('idCommission');
            
            $filename = str_replace(DS, '', $this->_getParam('name'));
            
            //On verifie si le fichier existe
            $exist = file_exists($path .DS. $filename);
            unlink($path .DS. $filename);
            $exist2 = file_exists($path .DS. $filename);

            if ($exist == $exist2) {
                throw new Exception('Impossible de supprimer le fichier '.$filename);
            }
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de la suppression du document',
                'message' => $e->getMessage()
            ));
            echo $e->getMessage();
        }
    }

}
