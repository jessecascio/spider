<?php

namespace Spider\Component;

use Spider\Storage\Storage;
use Spider\Storage\MySQL;
use Spider\Connection\Connection;

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

	/**
	 * @var Spider\Component\Nest
	 */
	protected $Nest;
	
	/**
	 * @param Spider\Connection\Connection
	 */
	public function __construct(Connection $Connection)
	{
		$this->Connection = $Connection;
		// inject default dependencies
		$this->injectStorage();
		$this->injectNest();
	}

	/**
	 * find a different location
	 */
	private function injectStorage()
	{
		// default to mysql storage, pull params from connection
		$params = $this->Connection->params();

		$this->Storage = new MySQL(
			$params['db'],
			$params['usr'],
			$params['pwd'],
			$params['hst'],
			$params['prt']
		);
	}

	private function injectNest()
	{
		$this->Nest = new Nest();
		// Nest needs ability to pass connection params
		$this->Nest->conn  = $this->Connection->sleep();
		$this->Nest->table = $this->table;
	}

	/**
	 * @param Spider\Storage\Storage
	 */
	public function storage(Storage $Storage)
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
	 * Set config options
	 * @param mixed
	 * @param mixed
	 */
	public function config($key, $val=null)
	{
		// check for a file path
		if (!isset($val) || !trim($val)) {
			$this->Config = new Config($key);
			return;
		}

		if (is_null($this->Config)) {
			$this->Config = new Config();
		}

		$this->Config->{$key} = $val;
	}
}
