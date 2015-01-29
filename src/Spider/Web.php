<?php

namespace Spider;

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

	/**
	 * @param mixed
	 */
	public function crawl($callback=null)
	{
		if (!is_null($callback)) {
			$this->setCallbacks($callback);
		}

		$Storage = $this->getStorage();
		$Storage->table($this->table);
		$Storage->init();

		$Nest = $this->getNest();
		$Nest->storage = $Storage->sleep();

		foreach ($this->queries as $key => $query) {
			$Nest->query = $query;
			$Nest->key   = $key;

			$pid = $Nest->spawn();

			$this->pid_key[$pid] = $key;
			$this->pids[] = $pid;
		}

		$this->wait($Storage);

		$Storage->destruct($this->table);
	}

	private function wait($Storage)
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
				// echo "Jest finished: " . var_dump($fresh) . PHP_EOL;	
				// grab the query key from the $pid_key
				// fire the callback for that key
				foreach ($fresh as $pid) {
					$key = $this->pid_key[$pid];
					
					// if no callback, can be done in a single call
					$this->data[$key] = $Storage->get($key);

					if (isset($this->callbacks[$key]) && is_callable($this->callbacks[$key])) {
						$this->data[$key] = $this->callbacks[$key]($this->data[$key]);
					}
				}
			}

			$processed = array_unique(array_merge($done, $processed)); 
			
			if (count(array_intersect($this->pids, $procs)) == 0) {
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
		$Nest = new Nest();
		$Nest->conn   = $this->Connection->sleep();
		$Nest->table  = $this->table;
		$Nest->memory = $this->memory;
		$Nest->trace  = $this->trace;
		
		return $Nest;
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