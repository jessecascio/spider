<?php

namespace Spider;

use Spider\Driver\Driver;

/**
 * Iterface 
 *
 * @package Spider
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class Web
{	
	private $Driver  = null;

	private $queries = [];

	private $id = '';

	private $pids = [];

	private $trace = '';

	private $memory = 10;

	public $data = [];
	
	public function __construct(Driver $Driver)
	{
		// unique id
		$this->id = md5(uniqid('jessecascio/spider',1));
		// set different drivers to connect to
		$this->Driver = $Driver;
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
		// restriction ???
		$this->memory = intval($mb);
	}

	public function crawl()
	{
		$this->Driver->init();
		$this->pids = [];
		$cnt = 0;
		
		$cmd_path   = escapeshellarg(__DIR__ . "/Nest/silk.php");
		$trace_path = trim($this->trace) ? escapeshellarg($this->trace) : '/dev/null';

		foreach ($this->queries as $query) {

			$cmd = "php ".$cmd_path." -q".base64_encode($query)." -m".$this->memory." >> ".$trace_path." 2>&1 & echo $!";
			
			$pids[] = trim(shell_exec($cmd));
			$cnt++;
		}

		$this->wait();
	}

	private function wait()
	{
	/*
		//Wait for all the busy workers finish their task before starting insertion
        $stillProcessing = true;
        $start = time();
        while($stillProcessing){
            
             * Get all the php processes that are on memory and make sure the workers process is not in the
             * current process list before moving to the next batch
             
            $currentProcesses = array_map("trim", explode("\n", shell_exec("pgrep php")));
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

	/*
	
        	// spawn a fly, passing in the $i value
			// $c  = "php weeve.php -i3  > /dev/null 2>/dev/null & echo $!"
			// $id = trim(shell_exec($c));
			/*
			$command = "php /var/admixt/modules/imageprocessor/imageScript.php -tadjust -w{$width} -h{$height} --ts {$commandLineUrl} --td {$processedFile} -m{$maxAppendFiles} > /dev/null 2>/dev/null & echo $!";
            $processId = trim(shell_exec($command));
      */
}