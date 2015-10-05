<?php

/**
 * @see Zend_Cache_Backend_Memcached
 */
class Cache_MySQLMemcached extends Zend_Cache_Backend_Memcached
{

    /**
     * Overide default memcache object's connexion for mysql memcache implementation
     * Get method returns serialized objects instead of serialized
     */
    public function __construct(array $options = array())
    {
        if (!extension_loaded('memcache')) {
            Zend_Cache::throwException('The memcache extension must be loaded for using this backend !');
        }
        parent::__construct($options);
        if (isset($this->_options['servers'])) {
            $value= $this->_options['servers'];
            if (isset($value['host'])) {
                // in this case, $value seems to be a simple associative array (one server only)
                $value = array(0 => $value); // let's transform it into a classical array of associative arrays
            }
            $this->setOption('servers', $value);
        }
        $this->_memcache = new Cache_UnserializedMemcache;
        foreach ($this->_options['servers'] as $server) {
            if (!array_key_exists('port', $server)) {
                $server['port'] = self::DEFAULT_PORT;
            }
            if (!array_key_exists('persistent', $server)) {
                $server['persistent'] = self::DEFAULT_PERSISTENT;
            }
            if (!array_key_exists('weight', $server)) {
                $server['weight'] = self::DEFAULT_WEIGHT;
            }
            if (!array_key_exists('timeout', $server)) {
                $server['timeout'] = self::DEFAULT_TIMEOUT;
            }
            if (!array_key_exists('retry_interval', $server)) {
                $server['retry_interval'] = self::DEFAULT_RETRY_INTERVAL;
            }
            if (!array_key_exists('status', $server)) {
                $server['status'] = self::DEFAULT_STATUS;
            }
            if (!array_key_exists('failure_callback', $server)) {
                $server['failure_callback'] = self::DEFAULT_FAILURE_CALLBACK;
            }
            if ($this->_options['compatibility']) {
                // No status for compatibility mode (#ZF-5887)
                $this->_memcache->addServer($server['host'], $server['port'], $server['persistent'],
                                        $server['weight'], $server['timeout'],
                                        $server['retry_interval']);
            } else {
                $this->_memcache->addServer($server['host'], $server['port'], $server['persistent'],
                                        $server['weight'], $server['timeout'],
                                        $server['retry_interval'],
                                        $server['status'], $server['failure_callback']);
            }
        }
    }

}