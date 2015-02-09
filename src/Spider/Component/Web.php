<?php

namespace Spider\Component;

use Spider\Storage;
use Spider\Connection;

/**
 * Spider interface 
 *
 * @package Spider
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class Web extends Silk
{
	protected $max_process = 5;

	protected $target = 0;
	/**
	 * Unique id used for tmp table name
	 * @var string
	 */
	protected $table = '';

	/**
	 * @var array
	 */
	protected $pids = [];

	/**
	 * Map pids to query keys
	 * @var array
	 */
	protected $pid_key = [];

	/**
	 * Map query keys to callback functions
	 * @var array
	 */
	protected $callbacks = [];

	// @todo Analyize purpose of silk, what else can move, hard to follow ???
	// @todo Browse design patterns to find a match

	/**
	 * @param Spider\Connection\Decorator
	 */
	public function __construct(Connection\Decorator $Connection)
	{
		// set unique id
		$this->table = md5(uniqid('jessecascio/spider_'.getmypid(), true));
		parent::__construct($Connection);
	}

	private function setStorage()
	{
		// see if storage has been set
		if (!is_null($this->Storage)) {
			return;
		}

		// default to mysql storage, pull params from connection
		$params = $this->Connection->params();

		$this->Storage = new Storage\MySQL(
			$params['db'],
			$params['usr'],
			$params['pwd'],
			$params['hst'],
			$params['prt']
		);
	}

	private function setNest()
	{
		$this->Nest = new Nest();
		$this->Nest->conn  = $this->Connection->sleep();
		$this->Nest->table = $this->table;
		
		if (is_null($this->Config)) {
			return $this->Nest;	
		}

		if (trim($this->Config->memory)) {
			$this->Nest->memory = intval($this->Config->memory);
		}

		if (trim($this->Config->trace)) {
			$this->Nest->trace = $this->Config->trace;
		}
		
		return $this->Nest;
	}

	/**
	 * @param mixed
	 */
	public function crawl($callback=null)
	{
		if (!is_null($callback)) {
			$this->setCallbacks($callback);
		}

		$this->setStorage();
		$this->setNest();

		$this->Storage->table($this->table);
		$this->Storage->init();

		$this->Nest->storage = $this->Storage->sleep();

		// @todo set max_process from config
		if (trim($this->Config->processes) && intval($this->Config->processes) > 0) {
			$this->max_process = intval($this->Config->processes);
		}

		// @todo stop passing the nest and storage around everywhere
		$this->target = count($this->queries);
		
		$this->start($this->max_process);
		
		$this->wait();

		/*
		foreach ($this->queries as $key => $query) {
			$this->Nest->query = $query;
			$this->Nest->key   = $key;

			$pid = $this->Nest->spawn();

			$this->pid_key[$pid] = $key;
			$this->pids[] = $pid;
		}
		*/

		$this->Storage->destruct();
	}

	private function start($max)
	{
		$started = 0;

		foreach ($this->queries as $key => $query) {

			if ($started >= $max) {
				break;
			}

			$this->Nest->query = $query;
			$this->Nest->key   = $key;

			$pid = $this->Nest->spawn();

			$this->pid_key[$pid] = $key;
			$this->pids[] = $pid;

			$started++;
			unset($this->queries[$key]);
		}

	}

	private function wait()
	{
		$processed = [];

		while (true) {
			$procs = array_filter(array_map("trim", explode("\n", shell_exec("pgrep php")))); //left
			$done  = array_diff($this->pids, $procs); // completed pids

			// have an array of total done
			// need to determine which have not been processed
			// @todo Dont need to retain key
			$fresh = array_diff($done, $processed);
			
			if (count($fresh)) {
				// start more processes
				$less    = count($fresh);
				var_dump($less); // @ todo remove
				$this->start($less);

				// echo "Jest finished: " . var_dump($fresh) . PHP_EOL;	
				// grab the query key from the $pid_key
				// fire the callback for that key
				foreach ($fresh as $pid) {
					$key = $this->pid_key[$pid];
					
					// if no callback, can be done in a single call
					$this->result[$key] = $this->Storage->get($key);

					if (isset($this->callbacks[$key]) && is_callable($this->callbacks[$key])) {
						$this->result[$key] = $this->callbacks[$key]($this->result[$key]);
					}
				}
			}

			$processed = array_unique(array_merge($done, $processed)); 
			
			if (count(array_intersect($this->pids, $procs)) == 0 && count($this->result) == $this->target) {
				break;
			}
		}
	}

	private function setCallbacks($callback)
	{
		// set the callbacks
		if (is_callable($callback)) {
			// @todo - Do better
			foreach ($this->queries as $key => $query) {
				$this->callbacks[$key] = $callback;
			}
		} else if (is_array($callback)) {
			$this->callbacks = $callback;
		}
	}

	/**
	 * @return Spider\Nest\Nest
	 */
	private function getNest()
	{
		$this->Nest = new Nest();
		$this->Nest->conn  = $this->Connection->sleep();
		$this->Nest->table = $this->table;
		
		if (is_null($this->Config)) {
			return $this->Nest;	
		}

		if (trim($this->Config->memory)) {
			$this->Nest->memory = intval($this->Config->memory);
		}

		if (trim($this->Config->trace)) {
			$this->Nest->trace = $this->Config->trace;
		}
		
		return $this->Nest;
	}

	/**
	 * @return Spider\Storage\Decorator
	 */
	private function getStorage()
	{
		// see if storage has been set
		if (!is_null($this->Storage)) {
			return $this->Storage;
		}

		// default to mysql storage, pull params from connection
		$params = $this->Connection->params();

		return new Storage\MySQL(
			$params['db'],
			$params['usr'],
			$params['pwd'],
			$params['hst'],
			$params['prt']
		);
	}
}