<?php

class Lily_Queue_Job_Stats {
    protected $_start_time = 0;
    protected $_end_time = 0;
    protected $_attempts_left = 0;
    protected $_job;
    
    public function __construct(Lily_Queue_Job& $job) {
        $this->_job = $job;
    }
    
    /**
     * __toArray
     * @return array
     */
    public function __toArray() {
        
    }
    
    
    
    /**
     * Set the start time (time()).
     * @return string
     */
    public function setStartTime() {
        $this->_start_time = time();
        return $this;
    }
    
    /**
     * Get the start time.
     * @return string
     */
    public function getStartTime() {
        return $this->_start_time;
    }
    
    /**
     * Set the end time.
     * @return string
     */
    public function setEndTime() {
        $this->_end_time = time();
        return $this;
    }
    
    /**
     * Get end time.
     * @return string
     */
    public function getEndTime() {
        return $this->_end_time;
    }
    
    /**
     * Get time elapsed.
     * @return string
     */
    public function getElapsedTime() {
        return round($this->_end_time - $this->_start_time, 5);
    }
}