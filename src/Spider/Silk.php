<?php

namespace Spider;

use Spider\Storage;
use Spider\Connection;
use Spider\Nest\Spawn;

/**
 * Base functionality 
 *
 * @package Spider
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class Silk
{	
	protected $Connection = null;

	protected $Storage = null;

	protected $queries = [];

	protected $table = '';

	protected $trace = '/dev/null';

	protected $memory = 10;

	protected $processes = 10;
	
	protected $pid_key = [];

	protected $pids = [];

	protected $callbacks = [];

	public $data = [];

	public function __construct(Connection\Decorator $Connection)
	{
		// unique id
		$this->table      = md5(uniqid('jessecascio/spider_'.getmypid(), true));
		$this->Connection = $Connection;
	}

	public function storage(Storage\Decorator $Storage)
	{
		$this->Storage = $Storage;
	}

	public function queries(array $queries)
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


		// $r = $this->Connection->query("SELECT count(*) as count FROM " . $this->table);

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
	

	
	/*
	
        	// spawn a fly, passing in the $i value
			// $c  = "php weeve.php -i3  > /dev/null 2>/dev/null & echo $!"
			// $id = trim(shell_exec($c));
			/*
			$command = "php /var/admixt/modules/imageprocessor/imageScript.php -tadjust -w{$width} -h{$height} --ts {$commandLineUrl} --td {$processedFile} -m{$maxAppendFiles} > /dev/null 2>/dev/null & echo $!";
            $processId = trim(shell_exec($command));
      */
}