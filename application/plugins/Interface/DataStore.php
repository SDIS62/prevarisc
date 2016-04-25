<?php

/**
 * This class abstract the data access to the repository
 */
interface Plugin_Interface_DataStore {

    public function getFilePath($piece_jointe, $linkedObjectType, $linkedObjectId, $createDirIfNotExists = false);

    public function getURLPath($piece_jointe, $linkedObjectType, $linkedObjectId);

    public function getFormattedFilename($piece_jointe, $linkedObjectType, $linkedObjectId);

}
