<?php

namespace Spider\Nest;

/**
 * Iterface 
 *
 * @package Nest
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class Weeve
{	
	private $Driver  = null;

	private $queries = [];

	private $id;

	public function __construct(Driver $Driver)
	{
		// unique id
		$this->id = md5(uniqid('jessecascio/spider',1));
		// set different drivers to connect to
		$this->Driver = $Driver;
	}

	public function weeve($queries)
	{
		$this->queries = $queries;
	}

	public function crawl()
	{
		$this->Driver->init();

		foreach ($this->queries as $key => $query) {


			
			// spawn a fly, passing in the $i value
			// $c  = "php weeve.php -i3  > /dev/null 2>/dev/null & echo $!"
			// $id = trim(shell_exec($c));
			/*
			$command = "php /var/admixt/modules/imageprocessor/imageScript.php -tadjust -w{$width} -h{$height} --ts {$commandLineUrl} --td {$processedFile} -m{$maxAppendFiles} > /dev/null 2>/dev/null & echo $!";
            $processId = trim(shell_exec($command));
            */	


			$i++;
		}
	}

	private function getConnection()
	{
		switch ($this->driver) {
			case self::MEMCACHED_DRIVER:
			$m = new Memcached();
			$m->addServer('127.0.0.1', 11211);		
			
			return $m;
		}
	}

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