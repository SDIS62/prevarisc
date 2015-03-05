<?php

interface Service_Interface_Cache
{
    public function remove($id);
    public function load($id);
    public function save($id, $data);
    public function getIdsBeginningByString($string);
    public function removeIdsBeginningByString($string);
}
