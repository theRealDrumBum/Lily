<?php

/**
 * Lily_Thrift_Hbase_ResultSet
 * @author Matt Ward
 *
 */
class Lily_Thrift_Hbase_ResultSet
	implements Iterator
{
	protected $_scanner;
	protected $_client;
	protected $_current;
	protected $_prefix;
	
	public function __construct(Lily_Thrift_Adapter_Abstract& $client, $scanner, $prefix=null)
	{
		$this->_client = $client;
		$this->_scanner = $scanner;
		$this->_prefix = $prefix;
		$this->next();
	}
	
	
	/* (non-PHPdoc)
	 * @see Iterator::current()
	 */
	public function current ()
	{
		return $this->_current;
	}

	/* (non-PHPdoc)
	 * @see Iterator::next()
	 */
	public function next ()
	{
		$result = $this->_client->scannerGet($this->_scanner);
		if (empty($result)) {
			$this->_current = null;
			return $this->_current;
		}
		$temp = current($result);
		if (null !== $this->_prefix) {
			if (substr($temp->row, 0, strlen($this->_prefix) !== $this->_prefix)) { 
				$this->_current = null;
			}
		}
		
		$this->_current = current($result);
		return $this->_current;
	}

	/* (non-PHPdoc)
	 * @see Iterator::key()
	 */
	public function key ()
	{
		return $this->_current->row;
	}

	/* (non-PHPdoc)
	 * @see Iterator::valid()
	 */
	public function valid ()
	{
		if (null === $this->_scanner) {
			return false;
		}
		if (null === $this->_current) {
			return false;
		}
		return true;
	}

	/* (non-PHPdoc)
	 * @see Iterator::rewind()
	 */
	public function rewind ()
	{
	}

	/**
	 * __destruct
	 * 
	 */
	public function __destruct() 
	{
		try {
			if (null !== $this->_scanner) {
				$this->_client->scannerClose($this->_scanner);
			}
		} catch (Exception $e) {
			// This means a transport exception happened connecting to hbase. 
			// Potentially one has already occured, and only one exception can be
			// thrown at a time
		}
	}
	
}