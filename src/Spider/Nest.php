<?php

namespace Spider;

/**
 * Creating new processes 
 *
 * @package Nest
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class Nest
{	
	/**
	 * @var string
	 */
	public $table = '';

	/**
	 * Query key
	 * @var string
	 */
	public $key = '';

	/**
	 * @var int
	 */
	public $memory = 10;

	/**
	 * Trace path
	 * @var string
	 */
	public $trace = '/dev/null';

	/**
	 * @var string
	 */
	public $query = '';

	/**
	 * @var string
	 */
	public $conn = '';

	/**
	 * @var string
	 */
	public $storage = '';

	/**
	 * Create new process
	 */
	public function spawn()
	{
		$php     = escapeshellarg(__DIR__ . "/weeve.php");
		$query   = base64_encode($this->query);
		$conn    = base64_encode($this->conn);
		$storage = base64_encode($this->storage);
		$key     = base64_encode($this->key);

		$cmd = "php ".$php." -k".$key." -q".$query." -m".$this->memory." -o".$this->table." -c".$conn." -s".$storage." >> ".$this->trace." 2>&1 & echo $!";
		
		return trim(shell_exec($cmd));
	}
}