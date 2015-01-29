<?php

namespace Spider\Nest;

/**
 * Creating new processes 
 *
 * @package Nest
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class Weeve
{	
	public $table = '';

	public $key = '';

	public $memory = 10;

	public $trace = '/dev/null';

	public $query = '';

	public $conn = '';

	public $storage = '';

	public function process()
	{
		$php     = escapeshellarg(__DIR__ . "/silk.php");
		$query   = base64_encode($this->query);
		$conn    = base64_encode($this->conn);
		$storage = base64_encode($this->storage);
		$key     = base64_encode($this->key);

		$cmd = "php ".$php." -k".$key." -q".$query." -m".$this->memory." -o".$this->table." -c".$conn." -s".$storage." >> ".$this->trace." 2>&1 & echo $!";
		
		return trim(shell_exec($cmd));
	}
}