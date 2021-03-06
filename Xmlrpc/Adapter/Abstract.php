<?php

abstract class Lily_Xmlrpc_Adapter_Abstract
{
	abstract public function setOptions(array $options);

	abstract public function sendRequest(Lily_Rpc_Request& $request);
	
	abstract public function sendResponse(Lily_Rpc_Response& $request);

}
