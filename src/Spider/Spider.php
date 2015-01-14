<?php

require 'Drivers.php';
require 'Memi.php';

class Spider implements Drivers
{
	private $queries = [];

	private $id;

	public function __construct($driver=self::MEMCACHED_DRIVER)
	{
		// set different drivers to connect to
		$this->driver = $driver;
	
		$this->id = md5(uniqid('jessecascio/spider',1));
	}

	public function weeve($queries)
	{
		$this->queries = $queries;
	}

	public function crawl()
	{
		$m = $this->getConnection();
		$i = 0;	
		foreach ($this->queries as $key => $query) {
			$m->set('jessecascio/spider'.$i, json_encode([$key=>$query]));
			
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