<?php

abstract class Lily_Queue_Adapter_Tadpole_Abstract
extends Lily_Queue_Adapter_Abstract {
    protected $_connection;
    protected $_format = 'json';
    protected $_valid_formats = array('json', 'serialize');
    
    /**
     * Constructor
     * @param array $config 
     */
    public function __construct($config=array()) {
        parent::__construct($config);
        
        if (isset($config['format'])) {
            if ($this->_isValidFormat($config['format'])) {
                $this->_format = $config['format'];
            } else {
                throw new Lily_Queue_Exception("queue.role.{$config['name']}.format is invalid");
            }
        }
        
        if (null === ($con = $this->_connect($config))) {
            throw new Lily_Queue_Exception("Oouch! Can't connect to Tadpole");
        } else {
            $this->_connection = $con;
        }
    }
    
    /**
     * Define how to connect to tadpole.
     * @throws Lily_Queue_Exception
     * @param $config
     * @author Tyler
     */
    abstract protected function _connect($config);
    
    /**
     * push
     * @param Lily_Queue_Job_Abstract $job
     * @return bool
     */
    public function push(Lily_Queue_Job_Abstract& $job) {
        $payload = $job->getPayload();        
        switch ($this->_format) {
            case 'serialize':
                $payload = serialize($payload);
                break;
                
            case 'json':
                if (is_object($payload)) {
                    if (!method_exists($payload, '__toArray')) {
                        throw new Lily_Queue_Exception('Payload must implement __toArray to be enqueued with JSON');
                    }
                    $payload = $payload->__toArray();
                }
                break;
        }
        
        $value = json_encode(array(
            'id' => $job->getId(),
            'attempts' => $job->getAttempts(),
            'payload' => $payload
        ));
        
        return $this->_connection->set($this->_queue_name, $value);
    }
    
    /**
     * pop
     * @return Lily_Queue_Job_Abstract
     */
    public function pop() {
        $result = $this->_connection->get($this->_queue_name);
        
        // Check if there's no jobs left
        if ($result === 'EMPTY') {
            return null;
        }
        
        if (!$result = json_decode($result, true)) {
            return null;
        }
        
        $payload = $result['payload'];
        if (empty($payload)) {
            return null;
        }
        
        $id = $result['id'];
        if (empty($id)) {
            return null;
        }
        
        $attempts = $result['attempts'];
        if (is_null($attempts)) {
            $attempts = 0;
        } else {
            $attempts++;
        }
        
        switch ($this->_format) {
            case 'serialize':
                $payload = unserialize($payload);
                break;
            
            case 'json':
                $payload = json_decode($payload, true);
                break;
        }
        
        $job = Lily_Queue_Manager::getJob($this->_queue_name)
            ->setPayload($payload)
            ->setId($id)
            ->setAttempts($attempts)
            ->setIsEnqueued(false);
        
        return $job;
    }
    
    /**
     * clear
     * @return null
     * @throws Lily_Queue_Exception
     */
    public function clear() {
        throw new Lily_Queue_Exception('Not implemented yet');
    }
    
    /**
     * getStats
     * @return array
     */
    public function getStats() {
        return $this->_connection->getStats();
    }
    
    /**
     * getStatus
     * @return null
     * @throws Lily_Queue_Exception
     */
    public function getStatus() {
        throw new Lily_Queue_Exception('Not implemented yet');
    }
    
    /**
     * Check if given config format is a valid one.
     * @param string $format
     * @return bool
     */
    protected function _isValidFormat($format) {
        return in_array($format, $this->_valid_formats);
    }
}