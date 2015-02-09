<?php

namespace Spider\Component;

use Spider\Storage;
use Spider\Connection;

/**
 * Base getter/setter functionality 
 *
 * @package Spider
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class Silk
{	
	/**
	 * @var Spider\Connection\Decorator
	 */
	protected $Connection = null;

	/**
	 * @var Spider\Storage\Decorator
	 */
	protected $Storage = null;
	
	/**
	 * @var Spider\Component\Config
	 */
	protected $Config = null;

	/**
	 * @var array
	 */
	protected $queries = array();
	
	/**
	 * Results
	 * @var array
	 */
	public $result = [];

	protected $Nest;
	
	/**
	 * @param Spider\Connection\Decorator
	 */
	public function __construct(Connection\Decorator $Connection)
	{
		$this->Connection = $Connection;
	}

	/**
	 * @param Spider\Storage\Decorator
	 */
	public function storage(Storage\Decorator $Storage)
	{
		$this->Storage = $Storage;
	}

	/**
	 * @param array
	 */
	public function queries(array $queries)
	{
		$this->queries = $queries;
	}

	public function config($ini)
	{
		$this->Config = new Config($ini);
	}
}
