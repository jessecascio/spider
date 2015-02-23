<?php

namespace Spider\Storage;

use PDO;
use PDOException;

/**
 * MySQL driver
 *
 * @package Storage
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class MySQL implements Storage
{
	/**
	 * @var PDO
	 */
	private $pdo = null;

	/**
	 * @var array
	 */
	private $params = array();

	private $table = '';

	/**
     * @param string - database
	 * @param string - user
	 * @param string - password
	 * @param string - host
	 * @param int    - port
	 * @throws PDOException
	 */
	public function __construct($db='test',$usr='root',$pwd='',$hst='127.0.0.1',$prt=3306)
	{
		$this->params['db']  = $db;
		$this->params['usr'] = $usr;
		$this->params['pwd'] = $pwd;
		$this->params['hst'] = $hst;
		$this->params['prt'] = $prt;
	}

	/**
	 * Make db connection
	 */
	protected function connect()
	{
		$dsn = 'mysql:host='.$this->params['hst'].';port='.$this->params['prt'].';dbname='.$this->params['db'].';';

		$this->pdo = new PDO($dsn, $this->params['usr'], $this->params['pwd']);

		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
		$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}

	/**
	 * @param string
	 */
	public function table($table)
	{
		// only allow alpha numerics
		$this->table = preg_replace("/[^A-Za-z0-9]/", '', $table);
	}

	/**
	 * Storage setup
	 */
	public function init()
	{
		$this->connect();
		
		// create temp table
		$sql = "CREATE TABLE if not exists `".$this->table."` (
					`id` varchar(255) primary key,
					`data` LONGBLOB 
				)ENGINE=innodb";
	
		$this->pdo->query($sql);
	}

	/**
	 * Storage teardown
	 */
	public function destruct()
	{
		$sql = "DROP TABLE `" . $this->table . "`";
		$this->pdo->query($sql);
	}

	/**
	 * Save value
	 * @param string
	 * @param mixed
	 */
	public function store($id, $data)
	{
		$sql = "INSERT INTO `".$this->table."` (id, data)
			    VALUES (".$this->pdo->quote($id).",".$this->pdo->quote(gzcompress(json_encode($data))).")";

		$this->pdo->query($sql);
	}

	/**
	 * Retrieve value
	 * @param string
	 */
	public function get($id)
	{
		$sql = "SELECT data
				FROM `".$this->table."` 
				WHERE id=".$this->pdo->quote($id);

		$stmt = $this->pdo->query($sql);
		$data = $stmt->fetchAll()[0];
		
		$data['data'] = json_decode(gzuncompress($data['data']), true);
			
		return $data['data']; 
	}

	public function all(array $ids)
	{
		$sql = "SELECT data
				FROM `".$this->table."` 
				WHERE id IN ('".implode("','", $ids)."')";

		$stmt = $this->pdo->query($sql);
		$data = $stmt->fetchAll();

		$result = array();

		// @todo Improve
		foreach ($data as $item) {
			$result[] = json_decode(gzuncompress($item['data']), true);
		}
		
		return $result; 
	}

	/**
	 * Remove value
	 * @param string
	 */
	public function remove($id)
	{

	}
 	
 	/**
 	 * Encoded params
 	 * @return string
 	 */
 	public function sleep()
    {
    	$d = array(
    		'class'  => __CLASS__,
    		'params' => $this->params
    	);

    	return json_encode($d);
    }

    /**
     * Re-connect
     * @param array
     */
    public function wake($params)
    {
    	$this->params = $params;
    	$this->connect();
    }
}	
