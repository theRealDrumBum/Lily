<?php


class Lily_Xmlrpc_Manager
{
	private static $instance;
	public static $request_id = 0;
	
	private $role_config;
	private $resource_config;
	private $adapter_config;
	
	private $connections = array();
	private $adapters = array();
	private $clients = array();
	
	
	public function __construct($options=null) 
	{
		if (isset($options['role'])) {
			$this->role_config = $options['role'];	
		}
		if (isset($options['adapter'])) {
			$this->adapter_config = $options['adapter'];
		}
		if (isset($options['resource'])) {
			$this->resource_config = $options['resource'];
		}
		
		self::$instance = $this;
	}
	
	public function __toString() {
		return __CLASS__;
	}
	
	public static function getAdapter($role = 'default') {
		if (null === self::$instance) {
			throw new Exception(__CLASS__ . " not initialized");
		}
		if ( !isset(self::$instance->role_config[$role]) ) {
			throw new Lily_Config_Exception("xmlrpc.role.$role");
		}
		
		if ( !isset(self::$instance->adapters[$role]) ) {
			$options = self::$instance->role_config[$role];
			if ( !isset($options['adapter']) ) {
				throw new Lily_Config_Exception("xmlrpc.role.$role.adapter");
			}
			$adapter_type = $options['adapter'];
			$adapter_options = isset(self::$instance->adapter_config[$adapter_type]) ?
				self::$instance->adapter_config[$adapter_type] : array();
			$class = 'Lily_Xmlrpc_Adapter_' . ucfirst($adapter_type);
			$adapter = new $class($options);
			$adapter->setOptions($options);
			self::$instance->adapters[$role] = $adapter;
		}
		return self::$instance->adapters[$role];
	}
	
	public static function getClient($client_name) {
		if (null === self::$instance) {
			throw new Exception(__CLASS__ . " manager not initialized");
		}
		if (!isset(self::$instance->clients[$client_name])) {
			// First check if the class exists, if it doesnt use a generic client with config
			if (class_exists($client_name)) {
				$resource = new $client_name();
			} else { // Class doesnt exist, use simple client and populate with config	
				if ( null === self::$instance->resource_config 
					|| !isset(self::$instance->resource_config[$client_name]) ) {
					//	Lily_Log::debug('', debug_backtrace(false));
					throw new Lily_Config_Exception("xmlrpc.resource.$client_name or class by name of $client_name");
				}
				$resource = new Lily_Xmlrpc_Resource(self::$instance->resource_config[$client_name]);
			}
			$client = new Lily_Xmlrpc_Client($resource);
			self::$instance->clients[$client_name] = $client;
			
		}
		return self::$instance->clients[$client_name];
	}
	
	public static function getServer($server_name) {
		// TODO
	}
}