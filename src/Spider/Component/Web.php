<?php

namespace Spider\Component;

use Exception;
use Spider\Connection\Connection;
use Spider\Storage\Storage;

/**
 * Spider interface
 *
 * @package Component
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class Web
{	
	/**
	 * @var Spider\Component\Config
	 */
	private $Config = null;

	/**
	 * @var Spider\Component\Logger
	 */
	private $Logger = null;

	/**
	 * @var array
	 */
	private $queries = array();
	
	/**
	 * Results
	 * @var array
	 */
	private $results = array();

	/**
	 * @var array
	 */
	private $pids = array();

	/**
	 * Map pids to query keys
	 * @var array
	 */
	private $pid_key = array();

	/**
	 * Map query keys to callback functions
	 * @var array
	 */
	private $callbacks = array();

	/**
	 * @param Spider\Connection\Connection
	 * @param Spider\Component\Config
	 */
	public function __construct(Connection $Connection, Config $Config=null)
	{
		$this->Config = is_null($Config) ? new Config() : $Config;
		$this->Config->connection($Connection);

		$this->Logger = Logger::instance($Config->getTrace());
	}

	/**
	 * @param array
	 */
	public function queries(array $queries)
	{
		$this->queries = $queries;
	}

	/**
	 * @return array
	 */
	public function results()
	{
		return $this->results;
	}

	/**
	 * Set the callbacks
	 * @param mixed
	 */
	private function setCallbacks($callback)
	{
		if (is_callable($callback)) {
			$this->callbacks = array_fill_keys(array_keys($this->queries), $callback);
		} else if (is_array($callback)) {
			$this->callbacks = $callback;
		}
	}

	/**
	 * Run queries
	 * @param mixed - callbacks
	 */
	public function crawl($callback=null)
	{
		if (!is_null($callback)) {
			$this->setCallbacks($callback);
		}

		$Storage = $this->Config->getStorage();

		try {
			$Storage->table($this->Config->getTable());
			$Storage->init();	
		} catch (Exception $e) {
			$this->Logger->addError('Unable to init storage', [$e->getMessage()]);
			return;
		}
		
		$this->Nest = new Nest($this->Config);

		$target = count($this->queries);

		$this->start($this->Config->getProcesses());	
		$this->watch($target);

		try {
			$Storage->destruct();
		} catch (Exception $e) {
			$this->Logger->addError('Unable to destroy storage', [$e->getMessage()]);
			return;
		}
	}

	/**
	 * @param int - num processes
	 */
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

			// map pids to query keys
			$this->pid_key[$pid] = $key;
			$this->pids[]        = $pid;

			$started++;
			unset($this->queries[$key]);
		}
	}

	/**
	 * Monitor active processes
	 * @param int
	 */
	private function watch($target)
	{
		$processed = array();

		// continue until all work is finished
		while (true) {
			// number of running processes
			$procs = array_filter(array_map("trim", explode("\n", shell_exec("pgrep php")))); 
			$done  = array_diff($this->pids, $procs); // completed pids

			// jobs just finished
			$finished = array_diff($done, $processed);
			
			if (count($finished)) {
				// start more processes
				$this->start(count($finished));
				// fire callbacks, or track result count
				if (count($this->callbacks)) {
					$this->save($finished);
				} else {
					$this->results = array_merge($this->results, $finished);
				}
			}

			// track queries who have fired callbacks
			$processed = array_unique(array_merge($done, $processed)); 
			
			// done when no more pids are running and all jobs have been processed
			if (count(array_intersect($this->pids, $procs)) == 0 && count($this->results) == $target) {
				break;
			}
		}

		// no callbacks, pull all results at once
		if (!count($this->callbacks)) {
			$Storage = $this->Config->getStorage();

			try {
				$this->results = $Storage->all($this->pid_key);
			} catch (Exception $e) {
				$this->Logger->addError('Unable to pull all results', [$e->getMessage()]);
				return;
			}
		}
	}

	/**
	 * @param array
	 */
	private function save(array $pids)
	{
		$Storage = $this->Config->getStorage();

		// fire the callbacks
		foreach ($pids as $pid) {
			// grab the query key
			$key = $this->pid_key[$pid];
			
			try {
				$this->results[$key] = $Storage->get($key);
			} catch (Exception $e) {
				$this->Logger->addError('Unable to pull results', [$e->getMessage(), 'key'=>$key]);
				$this->results[$key] = null;
				continue;
			}
			
			if (isset($this->callbacks[$key]) && is_callable($this->callbacks[$key])) {
				$this->results[$key] = $this->callbacks[$key]($this->results[$key]);
			}
		}
	}

}

