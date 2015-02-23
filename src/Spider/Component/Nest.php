<?php

namespace Spider\Component;

/**
 * Creating new processes 
 *
 * @package Nest
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class Nest
{	
	protected $Config;

	/**
	 * Query key
	 * @var string
	 */
	public $key = '';

	/**
	 * @var string
	 */
	public $query = '';

	public function __construct(Config $Config)
	{
		$this->Config = $Config;
	}

	/**
	 * Create new process
	 */
	public function spawn()
	{
		$Storage    = $this->Config->getStorage();
		$Connection = $this->Config->getConnection();

		$php     = escapeshellarg(__DIR__ . "/../bin/weeve.php");
		$query   = base64_encode($this->query);
		$conn    = base64_encode($Connection->sleep());
		$storage = base64_encode($Storage->sleep());
		$key     = base64_encode($this->key);
		$memory  = $this->Config->getMemory();
		$table   = $this->Config->getTable(); // encode ???
		$trace   = $this->Config->getTrace();

		// $this->Storage->sleep()
		$cmd = "php ".$php." -k".$key." -q".$query." -m".$memory." -o".$table." -c".$conn." -s".$storage." >> ".$trace." 2>&1 & echo $!";
		
		return trim(shell_exec($cmd));
	}
}