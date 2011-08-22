<?php

class Lily_Queue_Adapter_Tadpole_Pooled
extends Lily_Queue_Adapter_Tadpole_Abstract {
    
    /**
     * Connect to tadpole.
     * @throws Lily_Queue_Exception
     * @return void
     * @author Tyler
     */
    protected function _connect($config) {
        $con = new Memcache;
        $weight = 1;
        foreach ( explode(',', $config['host']) as $host ) {
            $con->addServer(
                $host,
                $config['port'],
                false,
                $weight,
                $this->_connection_timeout,
                null,
                null,
                array($this, '_con_failure_callback')
            );
            // $weight++;
        }
        return $con;
    }
    
    /**
     * undocumented function
     *
     * @return void
     * @author Tyler
     */
    public function _con_failure_callback($host, $port) {
        throw new Lily_Queue_Exception("Oouch! Can't connect to " . __CLASS__
            . " [ {$host}:{$port} ]");
    }
}