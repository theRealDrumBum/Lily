<?php 

class Lily_Xmlrpc_Client
{
	private $_resource;
	
	public function __construct(Lily_Rpc_Resource_Abstract& $resource) {
		$this->_resource = $resource;
	}
	
	public function __call($method, $args) {
		Lily_Xmlrpc_Manager::$request_id++;
		$request = new Lily_Rpc_Request();
		
		$meta = $this->_resource->getMethodMeta($method);
		$request->setResource($this->_resource->getName())
			->setMethod($meta['method'])
			->setParams($args)
			->setPath($meta['path'])
			->setId(Lily_Xmlrpc_Manager::$request_id);
		$adapter = Lily_Xmlrpc_Manager::getAdapter($meta['role']);
		try {
			$response = $adapter->sendRequest($request);
		} catch (Exception $e) {
			Lily_Log::error("Lily_Xmlrpc_Client exception detected, `{$e->getMessage()}`,  when sending request:", $request);
			throw $e;
		}
		return $response->getResult();
	}
}
