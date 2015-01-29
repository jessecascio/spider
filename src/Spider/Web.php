<?php

namespace Spider;

use Spider\Storage;
use Spider\Connection;
use Spider\Nest\Weeve;

/**
 * Spider interface 
 *
 * @package Spider
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class Web extends Silk
{	

	public function crawl($callback=null)
	{
		// set the callbacks
		if (is_callable($callback)) {
			foreach ($this->queries as $key => $query) {
				$this->callbacks[$key] = $callback;
			}
		} else if (is_array($callback)) {
			$this->callbacks = $callback;
		}

		$Storage = $this->getStorage();
		$Storage->init($this->table);

		$Weeve = $this->getWeeve();
		$Weeve->storage = $Storage->sleep();

		foreach ($this->queries as $key => $query) {
			$Weeve->query = $query;
			$Weeve->key = $key;
			$pid = $Weeve->process();
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
					$this->data[$key] = $Storage->get($this->table, $key);

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

	// update to inject for testing
	private function getWeeve()
	{
		$Weeve = new Weeve();
		$Weeve->conn    = $this->Connection->sleep();
		$Weeve->table   = $this->table;
		$Weeve->memory  = $this->memory;
		$Weeve->trace   = $this->trace;
		

		return $Weeve;
	}

	private function getStorage()
	{
		if (!is_null($this->Storage)) {
			return $this->Storage;
		}

		// default to mysql storage
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