<?php

 class Lily_Indeed_Manager
 {
	 private static $instance;
 	
	 private $clientid; 	
 	
	 public function __construct(array $options) {
		 if (null !== self::$instance) {
			 throw new Exception(__CLASS__ . " previously initialized.");
		 }
 		
		 if (!isset($options['clientid'])) {
			 throw new Lily_Config_Exception("indeed.clientid");
		 }
		 $this->clientid = $options['clientid'];
 		
		 self::$instance = $this;
	 }
 	
	public static function getClientId() {
		return self::$instance->clientid;
	}
 	
 }
