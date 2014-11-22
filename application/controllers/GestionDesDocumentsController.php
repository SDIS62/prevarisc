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

        //on liste les documents présents dans $path déclaré global pour le controller
        $path = $this->path; // dossier listé (pour lister le répertoir courant : $dir_nom = '.'  --> ('point')
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
        $this->view->path = DATA_PATH . "/uploads/documents";
        $this->view->listeFichiers = $fichier;
    }

    public function formAction()
    {
    }

    public function addAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender(true);
            //Si besoin verificaiton de l'extension du fichier (uniquement odt)
            if (move_uploaded_file($_FILES['fichier']['tmp_name'], $this->path .DS. $_FILES['fichier']['name'])) {
                echo "
                    <script type='text/javascript'>
                        window.top.window.callback(\"".$_FILES['fichier']['name']."\");
                    </script>
                ";
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
    }

    public function checkAction()
    {
        //On verifie si le fichier existe
        $this->view->exists = file_exists( $this->path .DS. $this->_request->nomFich);
    }

    public function suppdocAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender();

            $path = $this->path;
            //On verifie si le fichier existe
            $exist = file_exists( $this->path .DS. $this->_getParam('name'));
            unlink($this->path .DS. $this->_getParam('name'));
            $exist2 = file_exists( $this->path .DS. $this->_getParam('name'));

            if ($exist != $exist2) {
                //echo "le fichier ".$this->_getParam('name')." a bien été supprimé";
                $this->_helper->flashMessenger(array(
                    'context' => 'success',
                    'title' => 'Le document '.$this->_getParam('name').' a bien été supprimé',
                    'message' => ''
                ));
            }
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array(
                'context' => 'error',
                'title' => 'Erreur lors de la suppression du document',
                'message' => $e->getMessage()
            ));
        }
    }

}
