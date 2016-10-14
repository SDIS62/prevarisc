<?php

/**
 * This class abstract the data access to the repository
 */
class Plugin_SimpleFileDataStore extends Zend_Application_Resource_ResourceAbstract
implements Plugin_Interface_DataStore {
    
    /**
     * Format de formattage des noms de fichiers
     * @var string
     */
    protected $format = "%ID_PIECEJOINTE%%EXTENSION_PIECEJOINTE%";
    
    /**
     * Mapping entre les types de pièces jointes et les 
     * répertoires dans lesquels ils sont stockés
     * @var type 
     */
    protected $types = array();
    
    public function __construct($options = null) {
        
        parent::__construct($options);

        $this->types = array(
            'etablissement' => 'pieces-jointes',
            'etablissement_minature' => 'pieces-jointes' . DS . 'miniatures',
            'dossier' => 'pieces-jointes',
            'dateCommission' => 'documents_commission',
            'document' => 'documents',
            'courrier' => 'courriers',
            'avatar' => 'avatars',
        );
        
        if (isset($this->_options['fileFormat'])) {
            $this->format = $this->_options['fileFormat'];
        }
    }
    
    public function init() {
        
    }
    
    /**
     * Retourne le répertoire où se trouve le fichier
     * 
     * @param type $linkedObjectType
     * @param type $linkedObjectId
     * @return type
     */
    public function getBasePath($piece_jointe, $linkedObjectType, $linkedObjectId) {
        
        $type = isset($this->types[$linkedObjectType]) ? $this->types[$linkedObjectType] : $linkedObjectType;
        
        $directory = implode(DS, array(
            REAL_DATA_PATH,
            'uploads',
            $type,
        ));
        
        return $directory;
    }
    
    /**
     * Génère le chemin complet d'accès au fichier sur le file system
     * 
     * @param type $piece_jointe
     * @param type $linkedObjectType
     * @param type $linkedObjectId
     * @param type $createDirIfNotExists
     * @return type
     * @throws Exception
     */
    public function getFilePath($piece_jointe, $linkedObjectType, $linkedObjectId, $createDirIfNotExists = false) {
        
        $directory = $this->getBasePath($piece_jointe, $linkedObjectType, $linkedObjectId);
        
        if ($createDirIfNotExists && !is_dir($directory)){
            if (!@mkdir($directory, 0777, true)) {
                $error = error_get_last();
                throw new Exception('Cannot create base directory '.$directory.": ".$error['message']);
            }
        }
        
        return implode(DS, array(
            $directory,
            $piece_jointe ? $piece_jointe['ID_PIECEJOINTE'].$piece_jointe['EXTENSION_PIECEJOINTE'] : '',
        ));
    }
    
    /**
     * Génère l'URL d'accès réelle au fichier (nécessaire pour les images) 
     * 
     * @param type $piece_jointe
     * @param type $linkedObjectType
     * @param type $linkedObjectId
     * @return null
     */
    public function getURLPath($piece_jointe, $linkedObjectType, $linkedObjectId) {
        if (!$piece_jointe) {
            return null;
        }
        
        $type = isset($this->types[$linkedObjectType]) ? $this->types[$linkedObjectType] : $linkedObjectType;
        
        return implode(DS, array(
            DATA_PATH,
            'uploads',
            $type,
            $piece_jointe['ID_PIECEJOINTE'].$piece_jointe['EXTENSION_PIECEJOINTE']
        ));
    }
    
    /**
     * Génère un nom lisible et formaté pour le téléchargement d'une pièce jointe
     * 
     * @param type $piece_jointe
     * @param type $linkedObjectType
     * @param type $linkedObjectId
     * @return null
     */
    public function getFormattedFilename($piece_jointe, $linkedObjectType, $linkedObjectId) {
        
        if (!$piece_jointe) {
            return null;
        }
        
        $tokens = array(
            '%ID_PIECEJOINTE%' => $piece_jointe['ID_PIECEJOINTE'],
            '%NOM_PIECEJOINTE%' => $piece_jointe['NOM_PIECEJOINTE'],
            '%EXTENSION_PIECEJOINTE%' => $piece_jointe['EXTENSION_PIECEJOINTE'],
            '%DESCRIPTION_PIECEJOINTE%' => $piece_jointe['DESCRIPTION_PIECEJOINTE'],
            '%DATE_PIECEJOINTE%' => $piece_jointe['DATE_PIECEJOINTE'],
            '%ID_OBJECT%' => $linkedObjectId,
            '%TYPE_OBJET%' => $linkedObjectType,
            '%CODE_TYPE_OBJET%' => strtoupper(substr($linkedObjectType, 0, 3)),
            '%SHORT_CODE_TYPE_OBJET%' => strtoupper(substr($linkedObjectType, 0, 1)),
            '%NUMEROID_ETABLISSEMENT%' => null,
        );
        
        switch($linkedObjectType) {
            case 'etablissement':
                $service = new Service_Etablissement;
                $etablissement = $service->get($linkedObjectId);
                $tokens[] = $etablissement['general']['NUMEROID_ETABLISSEMENT'] ? $etablissement['general']['NUMEROID_ETABLISSEMENT'] : $linkedObjectId;
                break;
            case 'dossier':
                $db = new Model_DbTable_EtablissementDossier;
                $dossiers = $db->getEtablissementListe($linkedObjectId);
                $default_numeroid = array();
                if ($dossiers) {
                    foreach($dossiers as $dossier) {                        
                        $service = new Service_Etablissement;
                        $etablissement = $service->get($dossier['ID_ETABLISSEMENT']);
                        $numero_id = $etablissement['general']['NUMEROID_ETABLISSEMENT'] ? $etablissement['general']['NUMEROID_ETABLISSEMENT'] : $linkedObjectId;
                        if (stripos($piece_jointe['DESCRIPTION_PIECEJOINTE'], $numero_id) !== false) {
                            $tokens['%NUMEROID_ETABLISSEMENT%'] = $numero_id;
                            break;
                        }
                        $default_numeroid[] = $numero_id;
                    }
                    if (!$tokens['%NUMEROID_ETABLISSEMENT%']) {
                       $tokens['%NUMEROID_ETABLISSEMENT%'] = implode("_", $default_numeroid);
                    }
                } else
                {
                    $tokens['%NUMEROID_ETABLISSEMENT%'] = $linkedObjectId;
                }
                break;
            default:
                $tokens['%NUMEROID_ETABLISSEMENT%'] = $linkedObjectId;
                break;
        }
  
        return strtr($this->format, $tokens);
    }

}
