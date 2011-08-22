<?php

class Lily_Queue_Adapter_Tadpole_Standard
extends Lily_Queue_Adapter_Tadpole_Abstract {
    protected $_host;
    protected $_port;
    
    /**
     * Connect to tadpole.
     * @throws Lily_Queue_Exception
     * @return void
     * @author Tyler
     */
    protected function _connect($config) {
        $hosts = explode(',', $config['host']);
        $hosts_count = count($hosts);        
        $con = new Memcache;
        
        $i = 0;
        $max_tries = 3;
        do {
            $i++;
            $host = $hosts[rand() % $hosts_count];
            if ($con->connect($host, $config['port'], $this->_connection_timeout)) {
                // These are really for inspection of the object state
                // you can see which host:port was chosen
                $this->_host = $host;
                $this->_port = $config['port'];
                return $con;
            } else {
                if ($i >= $max_tries) {
                    throw new Lily_Queue_Exception("Oouch! Can't connect to " . __CLASS__);
                }
            }
        } while (is_null($this->_connection));
        
        return null;
    }
}