<?php

namespace Spider\Component;

/**
 * Process Management
 *
 * @package Component
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class Nest
{	
	/**
	 * @var string
	 */
	private $script;

	/**
	 * @var string - Connection sleep params
	 */
	private $conn;

	/**
	 * @var string - Storage sleep params
	 */
	private $storage;

	/**
	 * @var int
	 */
	private $memory;

	/**
	 * @var string
	 */
	private $table;

	/**
	 * @var string
	 */
	private $trace;

	/**
	 * @var string - query key
	 */
	public $key = '';

	/**
	 * @var string
	 */
	public $query = '';

	/**
	 * @param Spider\Component\Config
	 */
	public function __construct(Config $Config)
	{
		$Storage    = $Config->getStorage();
		$Connection = $Config->getConnection();

		$this->script  = escapeshellarg(__DIR__ . "/../bin/weeve.php");
		$this->conn    = base64_encode($Connection->sleep());
		$this->storage = base64_encode($Storage->sleep());
		$this->memory  = $Config->getMemory();
		$this->table   = $Config->getTable();
		$this->trace   = $Config->getTrace();
	}

	/**
	 * @return int - pid
	 */
	public function spawn()
	{
		$query = base64_encode($this->query);
		$key   = base64_encode($this->key);
		
		$cmd = "php ".$this->script." ".
			"-k".$key." ".
			"-q".$query." ".
			"-m".$this->memory." ".
			"-o".$this->table." ".
			"-c".$this->conn." ".
			"-s".$this->storage." ".
			">> ".$this->trace." 2>&1 & echo $!";
		
		return trim(shell_exec($cmd));
	}
}
