<?php

namespace Spider\Component;

use Spider\Storage;
use Spider\Connection\Connection;

/**
 * Spider interface , make the processes Observable ???
 *
 * @package Spider
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class Web extends Silk
{
	/**
	 * @var int
	 */
	protected $max_process = 5;

	/**
	 * Target number of queries to complete
	 * @var int
	 */
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

	/**
	 * @param Spider\Connection\Connection
	 */
	public function __construct(Connection $Connection)
	{
		// set unique id
		$this->table = md5(uniqid('jessecascio/spider_'.getmypid(), true));
		parent::__construct($Connection);
	}

	/**
	 * Set Nest properties from config
	 */
	private function buildNest()
	{
		if (is_null($this->Config)) {
			return;
		}

		// set the nest properties
		if (trim($this->Config->memory) && intval($this->Config->memory) > 0) {
			$this->Nest->memory = intval($this->Config->memory);
		}

		if (trim($this->Config->trace)) {
			$this->Nest->trace = $this->Config->trace;
		}
	}

	/**
	 * Set the callbacks
	 * @param mixed
	 */
	private function setCallbacks($callback)
	{
		// set the callbacks
		if (is_callable($callback)) {
			// @todo - Do better, map the callback to each query
			foreach ($this->queries as $key => $query) {
				$this->callbacks[$key] = $callback;
			}
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

		$this->buildNest();
		$this->Storage->table($this->table);
		$this->Storage->init();

		// notify Nest of the storage device
		$this->Nest->storage = $this->Storage->sleep();

		// check max process override
		if (trim($this->Config->processes) && intval($this->Config->processes) > 0) {
			$this->max_process = intval($this->Config->processes);
		}

		$this->target = count($this->queries);
	
		$this->start($this->max_process);	
		$this->watch();

		$this->Storage->destruct();
	}

	/**
	 * Start up processes
	 * @param int
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
	 */
	private function watch()
	{
		$processed = [];

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
				$this->save($finished);
			}

			// track queries who have fired callbacks
			$processed = array_unique(array_merge($done, $processed)); 
			
			// done when no more pids are running and all jobs have been processed
			if (count(array_intersect($this->pids, $procs)) == 0 && count($this->result) == $this->target) {
				break;
			}
		}
	}

	/**
	 * @param array
	 */
	private function save(array $pids)
	{
		// fire the callbacks
		foreach ($pids as $pid) {
			// grab the query key
			$key = $this->pid_key[$pid];
			
			// @todo If no callback, can be done in a single (get) call ???
			$this->result[$key] = $this->Storage->get($key);

			if (isset($this->callbacks[$key]) && is_callable($this->callbacks[$key])) {
				$this->result[$key] = $this->callbacks[$key]($this->result[$key]);
			}
		}
	}
}

