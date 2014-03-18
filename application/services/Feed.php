<?php

class Service_Feed
{
    /**
     * Récupération d'un flux de message pour un groupe d'utilisateurs
     *
     * @return array
     */
    public function get($id_groupe)
    {
    	$DB_messages = new Model_DbTable_News;
    	return $DB_messages->getNews($id_groupe);
    }

    /**
     * Ajout d'un message
     *
     * @param int $type
     * @param string $message
     * @param array $confidentialite
     */
    public function addMessage($type, $message, array $confidentialite)
    {
        $model = new Model_DbTable_News;
        $model->add($type, $message, $confidentialite);
    }

    /**
     * Suppression d'un message
     *
     * @param int $id_message
     */
    public function deleteMessage($id_message)
    {
        $model = new Model_DbTable_News;
        $model->deleteNews($id_message);
    }
}
