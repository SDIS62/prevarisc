<?php

class Service_Cache implements Service_Interface_Cache
{
    public function __construct(Zend_Cache_Core $cache)
    {
        if(!($cache->getBackend() instanceof Zend_Cache_Backend_ExtendedInterface)) {
            throw new Exception('Mauvais backend de cache');
        }

        $this->cache = $cache;
    }

    public function remove($id)
    {
        $this->cache->remove($id);
    }

    public function load($id)
    {
        $this->cache->load($id);
    }

    public function save($id, $data)
    {
        $this->cache->save($data, $id);
    }

    public function getIdsBeginningByString($string)
    {
        $ids = $this->cache->getIds();

        return array_filter($ids, function($value) use($string) {
            return substr($value, 0, strlen($string)) === $string;
        });
    }

    public function removeIdsBeginningByString($string)
    {
        $ids = $this->getIdsBeginningByString($string);

        foreach($ids as $id) {
            $this->remove($id);
        }
    }

}
