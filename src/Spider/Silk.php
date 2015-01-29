<?php

namespace Spider;

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
	 * @var array
	 */
	protected $queries = array();

	/**
	 * Unique id used for tmp table name
	 * @var string
	 */
	protected $table = '';

	/**
	 * Output path
	 * @var string
	 */
	protected $trace = '/dev/null';

	/**
	 * Memory limit per thread
	 * @var int
	 */
	protected $memory = 10;

	/**
	 * Process limit
	 * @var int
	 */
	protected $processes = 10;
	
	/**
	 * Results
	 * @var array
	 */
	protected $data = [];

	/**
	 * @param Spider\Connection\Decorator
	 */
	public function __construct(Connection\Decorator $Connection)
	{
		// set unique id
		$this->table      = md5(uniqid('jessecascio/spider_'.getmypid(), true));
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

	/**
	 * @param string
	 */
	public function trace($path)
	{
		$this->trace = $path;
	}

	/**
	 * @param int
	 */
	public function memory($mb)
	{
		$this->memory = intval($mb);
	}

	/**
	 * @param int
	 */
	public function processes($processes)
	{
		$this->processes = intval($processes);
	}

	public function data()
	{
		return $this->data;
	}
}
