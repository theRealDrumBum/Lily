<?php
/**
 * Copyright (c) 2010, 2011 All rights reserved, Matt Ward
 * This code is subject to the copyright agreement found in 
 * the project root's LICENSE file. 
 */
/**
 * LilypadMVC_Controller_Response class.
 * @author Matt Ward
 */
class LilypadMVC_Controller_Response
{
    private $_headers;
 	private $_content;
 	private $_data;   
    private $_view;
    private $_template;
    private $_template_dir;
    private $_layout;
    private $_layout_dir;
    private $_should_render;
    private $_cookies;
    
    public function __construct()
    {
        $this->_headers	= array();
        $this->_data	= array();
        $this->_cookie	= array();
        $this->_should_render = true;
    }
    
    public function assign($key, $data) {
    	$this->_data[$key]	= $data;
    	return $this;
    }
    
    public function addHeader($header) {
        $this->_headers[] 	= $header;
        return $this;
    }
    
    public function setCookie($cookie_name, $value, $ttl)
    {
   		$this->_cookie[$cookie_name] = array(
   			'value'	=>$value,
   			'ttl'	=> $ttl
   		);
   		return $this;
    }
    
    public function setContent($content) {
    	$this->_content = $content;
    	return $this;
    }
    
    public function getContent($content) {
    	return $this->_content;
    }
    
    public function setTemplate($template) {
    	$this->_template	 = $template;
    	return $this;
    }
    
    public function getTemplate() {
    	return $this->_template;
    }
    
    public function setTemplateDir($dir) {
    	$this->_template_dir = $dir;
    }
    
    public function getTemplateDir() {
    	return $this->_template_dir;
    }
    
    public function setLayoutDir($dir) {
    	$this->_layout_dir = $dir;
    	return $this;
    }
    
    public function getLayoutDir() {
    	return $this->_layout_dir;
    }
    
    public function setLayout($arg) {
    	$this->_layout = $arg;
    	return $this;
    }
    
    public function getLayout() {
    	return $this->_layout;	
    }
    
    public function setView($view) {
    	$this->_view	= $view;
    	return $this;
    }
    
    public function getView() {
    	return $this->_view;
    }
    
    public function setNoRender() {
    	$this->_should_render = false;
    }
    
    public function __toString() {
    	return $this->_content;
    }
    
    public function render() {	
    	$temp = '';
    	
    	$log = LilypadMVC_Application::getLogger();
     	if ($this->_should_render && $this->_view) {
     		foreach ($this->_data as $key => $value) { 
     			$this->_view->$key = $value;
     		}
        	$log->debug("{$this->_template}", null, 'LilypadMVC_DEBUG');
     		$temp	= $this->_view->render($this->_template_dir . '/' . $this->_template); 
     		$this->_content = $temp . $this->_content;
     	}
     	
        if ($this->_should_render && $this->_layout) {
        	$this->_view->head = '';
        	$this->_view->content = $this->_content;
        	$this->_content = $this->_view->render($this->_layout_dir . '/' . $this->_layout);
        }
        
		// Spit out headers before sending content
     	if (!empty($this->_headers)) {
	     	foreach ($this->_headers as $header) { 	
	     		header($header);
	     	}
     	}
        
        if (!empty($this->_cookie)) {
	     	foreach ($this->_cookie as $name => $payload) {
	     		setcookie($name, $payload['value'], $payload['ttl'], '/');	
	     	}
     	}
     	
		echo $this->_content;     	
    }
}