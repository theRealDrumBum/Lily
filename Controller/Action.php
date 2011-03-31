<?php


/**
 * Lilypad_Controller_Action class.
 * @author Matt Ward
 */
class Lilypad_Controller_Action
{
    protected $_request;
    protected $_response;

    public function __construct(Lilypad_Controller_Request& $request, Lilypad_Controller_Response& $response){
        // Instantiate smarty here.
        
        $this->_request     = $request;
        $this->_response	= $response;
    }
    
    public function preDispatch(){}
    
    public function postDispatch(){}
    
	public function assign($key, $value) {
		$this->_response->assign($key, $value);
	}
    
    public function setResponse(Lilypad_Controller_Response $response) {
    	$this->_response = $response;
    }
    
    public function getResponse() {
    	return $this->_response;
    }
    
    protected function setCookie($cookie_name, $value, $ttl) {
    	$this->_response->setCookie($cookie_name, $value, $ttl);
    }
    
    protected function getParam($param, $default=null) {
    	$t = $this->_request->getParam($param);
    	if (null === $t) {
    		return $default;
    	}
        return $t;
    }

	protected function getParams() {
		return $this->_request->getParams();
	}
    
    protected function setParam($param, $value) {
        $this->_request->setParam($param, $value);
        return $this;
    }
    
    protected function forward($module, $controller, $action)
    {
        $this->_request->setDispatched(false)
            ->setModule($module)
            ->setController($controller)
            ->setAction($action);
    }
    
    protected function redirect($uri)
    {
        $this->_request->setDispatched(true);
        $this->_response->setNoRender();
        $this->_response->addHeader('Location: '.$uri);    
    }
    
    protected function setNoRender()
    {
    	$this->_response->setNoRender();
    }


}
