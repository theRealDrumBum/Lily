<?php

class Lily_Queue_Job_Stats {
    protected $_start_time = 0;
    protected $_end_time = 0;
    protected $_job;
    
    public function __construct(Lily_Queue_Job_Abstract& $job) {
        $this->_job = $job;
    }
    
    /**
     * __toArray
     * @return array
     */
    public function __toArray() {
        return array(
            'start_time' => $this->_start_time,
            'end_time' => $this->_end_time,
            'elapsed_time' => $this->getElapsedTime(),
            'attempts_left' => $job->getAttemptsLeft()
        );
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
     * @param int $precision
     * @return string
     */
    public function getElapsedTime($precision=5) {
        return round($this->_end_time - $this->_start_time, $precision);
    }
}