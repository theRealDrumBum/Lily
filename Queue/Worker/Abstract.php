<?php

abstract class Lily_Queue_Worker_Abstract {
    protected $_queue;
    protected $_sleep_duration = 3;
    protected $_stats;
    
    // How many fail retries on job re-enquement
    const REENQUEUE_TRIES = 3;
    
    /**
     * Constructor
     * @param array $options
     */
    public function __construct($options) {
        if (isset($options['sleep_duration'])) {
            $this->_sleep_duration = $options['sleep_duration'];
        }
        if (isset($options['name'])) {
            $this->_queue = Lily_Queue_Manager::getAdapter($options['name']);
        } else {
            throw new Lily_Config_Exception("queue.role.$role.name");
        }
        $this->_stats = new Lily_Queue_Worker_Stats();
    }
    
    /**
     * Start the worker on it's infinite journey.
     * Through the space time continuum.
     * @return array
     */
    public function run() { 
        $queue_name = $this->_queue->getQueueName();
        $this->_stats->setStartTime();
        
        while (true) {
            $this->_stats->incLoops();
            $job = $this->_queue->pop();
            
            if (is_null($job)) {
                Lily_Log::write('tadpole', "[$queue_name] Empty queue, sleeping...");
                $this->_stats->incSleeps();
                sleep($this->_sleep_duration);
                continue;
            }
            
            $id = $job->getId();
            
            if (!$job->hasAttemptsLeft()) {
                Lily_Log::write('tadpole', "[$queue_name] Job #{$id} has exceeded max_attempts.");
                $this->_stats->incSleeps();
                continue;
            }
            
            try {
                $job->stats->setStartTime();
                
                if ($job->perform()) {
                    $job->stats->setEndTime();
                    $this->_stats->incJobsCompleted();
                    $attempts_left = $job->getAttemptsLeft();
                    $elapsed_time = $job->stats->getElapsedTime(0);
                    Lily_Log::write('tadpole', "[$queue_name] Job #{$id} completed in $elapsed_time secs with $attempts_left attempts left.");
                } else {
                    // Put it back on the queue
                    $this->_stats->incJobsFailed();
                    $attempts_left = $job->getAttemptsLeft();
                    Lily_Log::write('tadpole', "[$queue_name] Job #{$id} failed. $attempts_left attempts left.");
                    if ( !$this->_safeReenqueue($job) ) {
                        $this->_stats->incEnqueueFails();
                    }
                }
            } catch (Exception $e) {
                $this->_stats->incJobsFailed();
                Lily_Log::write('tadpole', "[$queue_name] Job #{$id} failed with error: " . $e->getMessage());
                if ( !$this->_safeReenqueue($job) ) {
                    $this->_stats->incEnqueueFails();
                }
            }
        }
        
        $this->_stats->setEndTime();
        return $this->_stats->__toArray();
    }
    
    /**
     * Re-enqueue the job with a max_retry.
     * @param Lily_Queue_Job_Abstract $job
     * @return bool
     */
    protected function _safeReenqueue(Lily_Queue_Job_Abstract& $job) {
        $trys_left = self::REENQUEUE_TRIES;
        $result = false;
        
        while ($trys_left > 0) {
            try {
                $result = $job->enqueue();
            } catch (Exception $e) {
                // Just catching any exceptions cuz we
                // don't care for them and don't want them.
                $result = false;
            }

            if ($result) {
                return true;
            } else {   
                $trys_left--;
                Lily_Log::write('tadpole', "[$queue_name] Job #{$id} failed to re-enqueue with $trys_left trys left.");
                if ($trys_left <= 0) {
                    return false;
                }
                usleep(200);
            }
        }
        
        return true;
    }
}