<?php

abstract class Lily_Queue_Adapter_Abstract {
    protected $_queue_name;
    protected $_connection_timeout = 1;
    
    /**
     * Constructor
     * @param array $config 
     */
    public function __construct($config=array()) {
        if (isset($config['name'])) {
            $this->_queue_name = $config['name'];
        } else {
            throw new Lily_Queue_Exception('Adapter must have a name');
        }
        if (isset($config['timeout'])) {
            $this->_connection_timeout = $config['timeout'];
        }
    }
    
    /**
     * Get the queue name.
     * @return string
     */
    public function getQueueName() {
        return $this->_queue_name;
    }
    
    /**
     * Add a job to the queue.
     * @param Lily_Queue_Job_Abstract $job 
     * @return mixed
     */
    abstract public function push(Lily_Queue_Job_Abstract& $job);
    
    /**
     * Take an item off the queue.
     * @return Lily_Queue_Job_Abstract
     */
    abstract public function pop();
    
    /**
     * Clear out any jobs in the current queue.
     * @return bool
     */
    abstract public function clear();
    
    /**
     * Get stats on queue service.
     * @return array
     */
    abstract public function getStats();
    
    /**
     * Get status on the queue server.
     * @return bool
     */
    abstract public function getStatus();
}