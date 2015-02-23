<?php

namespace Spider\Component;

use Spider\Storage\Storage;
use Spider\Storage\MySQL;
use Spider\Connection\Connection;

/**
 * Parse an .ini file for config options
 *
 * @package Component
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class Config
{	
	protected $processes = 5;

	protected $memory    = 100;

	protected $trace     = '/dev/null';

	protected $table = '';

	protected $Connection;

	protected $Storage;

	public function __construct()
	{
		$this->table = md5(uniqid('jessecascio/spider_'.getmypid(), true));
	}

	/**
	 * Path to .ini file
	 * @param string
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

	public function processes($processes)
	{
		$this->processes = intval($processes) > 0 ? intval($processes) : $this->processes;
	}

	public function getProcesses()
	{
		return $this->processes;
	}

	public function memory($memory)
	{
		$this->memory = intval($memory) > 0 ? intval($memory) : $this->memory;
	}

	public function getMemory()
	{
		return $this->memory;
	}

	public function trace($trace)
	{
		$this->trace = is_writable($trace) ? $trace : $this->trace;
	}

	public function getTrace()
	{
		return $this->trace;
	}

	public function getTable()
	{
		return $this->table;
	}

	public function connection(Connection $Connection)
	{
		$this->Connection = $Connection;
	}

	public function getConnection()
	{
		return $this->Connection;
	}

	public function storage(Storage $Storage)
	{
		$this->Storage = $Storage;
	}

	public function getStorage()
	{
		if (!is_null($this->Storage)) {
			return $this->Storage;
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

