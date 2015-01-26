<?php

namespace Spider;

use Spider\Storage;
use Spider\Connection;
use Spider\Nest\Spawn;

/**
 * Iterface 
 *
 * @package Spider
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class Web
{	
	private $Connection = null;

	private $Storage = null;

	private $queries = [];

	private $container = '';

	private $trace = '/dev/null';

	private $memory = 10;

	private $processes = 10;
	
	private $pid_key = [];

	private $pids = [];

	private $callbacks = [];

	public $data = [];

	public function __construct(Connection\Decorator $Connection)
	{
		// unique id
		$this->container = md5(uniqid('jessecascio/spider_'.getmypid(),1));
		$this->Connection = $Connection;
	}

	public function storage(Storage\Decorator $Storage)
	{
		$this->Storage = $Storage;
	}

	public function queries($queries)
	{
		$this->queries = $queries;
	}

	public function trace($path)
	{
		// verify path ????

		// path to where to trace output
		$this->trace = $path;
	}

	public function memory($mb)
	{
		$this->memory = intval($mb);
	}

	public function processes($processes)
	{
		$this->processes = intval($processes);
	}

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
		$Storage->init($this->container);

		$Spawn = new Spawn();
		$Spawn->conn      = $this->Connection->sleep();
		$Spawn->container = $this->container;
		$Spawn->memory  = $this->memory;
		$Spawn->trace   = $this->trace;
		$Spawn->storage = $Storage->sleep();

		foreach ($this->queries as $key => $query) {
			$Spawn->query = $query;
			$Spawn->key = $key;
			$pid = $Spawn->process();
			$this->pid_key[$pid] = $key;
			$this->pids[] = $pid;
		}

		$this->wait($Storage);

		$Storage->destruct($this->container);
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
					$this->data[$key] = $Storage->get($this->container, $key);

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

		// $r = $this->Connection->query("SELECT count(*) as count FROM " . $this->container);

		// var_dump($r[0]['count']);

		// var_dump(gzuncompress($r[0]['data']));


	/*
		//Wait for all the busy workers finish their task before starting insertion
        $stillProcessing = true;
        $start = time();
        while($stillProcessing){
            
             * Get all the php processes that are on memory and make sure the workers process is not in the
             * current process list before moving to the next batch
             
            
            $diff = array_intersect($processIds, $currentProcesses);
            $stillProcessing = !empty($diff);

                         * if the process took more than 2.5 minutes, then kill the offending ones
             
            if ((time() - $start) > 150) {
                foreach ($currentProcesses as $processId) {
                    shell_exec("kill ".$processId);
                }
            }
        }

        return !$stillProcessing;	
	*/
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
	/*
	
        	// spawn a fly, passing in the $i value
			// $c  = "php weeve.php -i3  > /dev/null 2>/dev/null & echo $!"
			// $id = trim(shell_exec($c));
			/*
			$command = "php /var/admixt/modules/imageprocessor/imageScript.php -tadjust -w{$width} -h{$height} --ts {$commandLineUrl} --td {$processedFile} -m{$maxAppendFiles} > /dev/null 2>/dev/null & echo $!";
            $processId = trim(shell_exec($command));
      */
}