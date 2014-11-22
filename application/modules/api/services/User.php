<?php

class Api_Service_User
{
    /**
     * Retourne un seul utilisateur identifié par le paramètre id.
     *
     * @param int $id
     * @return string
     */
    public function get($id)
    {
        $service_user = new Service_User;
        $user = $service_user->find($id);
        return $user;
    }

    /**
     * Retourne les preferences d'un utilisateur identifié par le paramètre id.
     *
     * @param int $id
     * @return string
     */
    public function getPreferences($id, $preferences)
    {
        $service_user = new Service_User;
        $preferences = $service_user->savePreferences($id, $preferences);
        
        if (!$preferences) {
            throw new Exception("Failed saving preferences");
        }
        return $preferences->toArray();
    }
    
}