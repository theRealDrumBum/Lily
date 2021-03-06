<?php
/**
 * Copyright (c) 2010, 2011 All rights reserved, Matt Ward
 * This code is subject to the copyright agreement found in 
 * the project root's LICENSE file. 
 */
/**
 * Lily_Controller_Route_Regex class.
 * @author Matt Ward
 * @extends Lily_Controller_Route_Abstract
 */
class Lily_Controller_Route_Regex extends Lily_Controller_Route_Abstract
{
    private $_regex;
    private $map;
    private $matches;
    
    /**
     * @param name	- A unique string to identify the route
     * @param $regex	- The regex to match against. Must be capable of doing a string replace on same regex
     * to clear the string, using remainders as paramters to the desired controller / action
     * @map	- an associative array of infomation for the regex.
     * Example map:
     * $map = array(
     *    'controller'	=> 'user'  #controller, action must be defined
     *	  'action'		=> ':variable' # will value from a regex match named 'variable' 
     *    'someparam'   => 'staticexample'
     *    'anotherparam' => ':value2' #match against 'value2' in regex matches array
     * )
     */
    public function __construct($name, $regex, array $map)
    {
        $this->name = $name;
        $this->_regex = $regex;
        if (!isset($map['controller'])) {
            throw new Lily_Controller_Route_Exception("Controller not specified");
        }
        if (!isset($map['action'])) {
            throw new Lily_Controller_Route_Exception("Action not specified");
        }
        
        if (!isset($map['module'])) $map['module'] = 'default';
        $this->map = $map;
            
    }
    
    
    public function match($uri)
    {
    	Lily_Log::write("lily","Trying to match $uri against {$this->_regex}");
    
        if (preg_match($this->_regex, $uri, $this->matches)) {
        	Lily_Log::write("lily", 'Match found.', $this->matches);
            return true;   
        }
        return false;
    }
    
    public function getRequest($uri, $query_string)
    {
    	$request = new Lily_Controller_Request();
    	
    	foreach ($this->map as $name => $value) {
    		if (substr($value, 0, 1) == ':') {
    			$index	= substr($value,1);
    			if (isset($this->matches[$index])) {
    				$value = !empty($this->matches[$index]) ? $this->matches[$index] : NULL;
    			} else {
    				$value = NULL;
    			}
    		}
    		
    		switch ($name) {
    			case 'module':
    				if ($value) {
    					$request->setModule($value);
    				}
    				break;
    			
    			case 'controller':
    				if ($value) {
    					$request->setController($value);
    				}
    				break;
    				
    			case 'action':	
    				if ($value) {
    					$request->setAction($value);
    				}
    				break;
    				
    			case 'data_type':
    				if ($value) {
    					$request->setDataType($value);
    				}
    				break;
    			
    			default:
    				if (!is_null($value))	$request->setParam($name, $value);
    				break;
    		}
    	}
        
        $remainder = preg_replace($this->_regex, '', $uri);
        $this->_parseParams($request, $remainder);
        $this->_parseParams($request, $query_string);
        return $request;
    }

}
