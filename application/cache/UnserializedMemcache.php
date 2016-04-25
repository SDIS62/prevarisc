<?php

class Cache_UnserializedMemcache extends Memcache {
    
    protected function fixReturnedContent($data) {
        if ($data === false) {
            return false;
        }
        return is_string($data) ? unserialize($data) : $data;
    }
    
    public function get($key, $flags = null) {
        return $this->fixReturnedContent(parent::get($key, $flags));
    }
}