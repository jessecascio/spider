<?php

namespace Spider\Component;

use Spider\Storage\Storage;
use Spider\Storage\MySQL;
use Spider\Connection\Connection;

/**
 * Hold configuration options
 *
 * @package Component
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class Config
{	
	/**
	 * @var int
	 */
	protected $processes = 5;

	/**
	 * @var int - MB
	 */
	protected $memory = 100;

	/**
	 * @var string - full path
	 */
	protected $trace = '/dev/null';

	/**
	 * @var string
	 */
	protected $table = '';

	/**
	 * @var Spider\Connection\Connection
	 */
	protected $Connection;

	/**
	 * @var Spider\Storage\Storage
	 */
	protected $Storage;

	public function __construct()
	{
		$this->table = md5(uniqid('jessecascio/spider_'.getmypid(), true));
	}

	/**
	 * Parse .ini file
	 * @param string - path
	 */
	public function parse($ini)
	{
		if (!is_file($ini)) {
			return;
		}

		$data = parse_ini_file($ini);
		
		if (isset($data['processes'])) {
			$this->processes($data['processes']);
		}
		if (isset($data['memory'])) {
			$this->memory($data['memory']);
		}
		if (isset($data['trace'])) {
			$this->trace($data['trace']);
		}
	}

	/**
	 * @param int
	 */
	public function processes($processes)
	{
		$this->processes = intval($processes) > 0 ? intval($processes) : $this->processes;
	}

	/**
	 * @return int
	 */
	public function getProcesses()
	{
		return $this->processes;
	}

	/**
	 * @param int - MB
	 */
	public function memory($memory)
	{
		$this->memory = intval($memory) > 0 ? intval($memory) : $this->memory;
	}

	/**
	 * @return int
	 */
	public function getMemory()
	{
		return $this->memory;
	}	

	/**
	 * @param string - path
	 */
	public function trace($trace)
	{
		$this->trace = is_string($trace) && strpos($trace, '.') && touch($trace) ? $trace : $this->trace;
	}

	/**
	 * @return string
	 */
	public function getTrace()
	{
		return $this->trace;
	}

	/**
	 * @return string
	 */
	public function getTable()
	{
		return $this->table;
	}

	/**
	 * @param Spider\Connection\Connection
	 */
	public function connection(Connection $Connection)
	{
		$this->Connection = $Connection;
	}

	/**
	 * @return Spider\Connection\Connection
	 */
	public function getConnection()
	{
		return $this->Connection;
	}

	/**
	 * @param Spider\Storage\Storage
	 */
	public function storage(Storage $Storage)
	{
		$this->Storage = $Storage;
	}

	/**
	 * @throws LogicException
	 * @return Spider\Storage\Storage
	 */
	public function getStorage()
	{
		if (!is_null($this->Storage)) {
			return $this->Storage;
		}

		if (is_null($this->Connection)) {
			throw new \LogicException("Spider\\Connection\\Connection is required");
		}

		// default to mysql storage, pull params from connection
		$params = $this->Connection->params();

		$this->Storage = new MySQL(
			$params['db'],
			$params['usr'],
			$params['pwd'],
			$params['hst'],
			$params['prt']
		);
		
		return $this->Storage;
	}
}

