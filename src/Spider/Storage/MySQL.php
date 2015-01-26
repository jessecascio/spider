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
class MySQL implements Decorator
{
	private $params = array();

	/**
	 * @todo  REMOVE ????
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

	protected function connect()
	{
		$dsn = 'mysql:host='.$this->params['hst'].';port='.$this->params['prt'].';dbname='.$this->params['db'].';';

		$this->pdo = new PDO($dsn, $this->params['usr'], $this->params['pwd']);

		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
		$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}

	/**
	 * Driver setup
	 */
	public function init($key)
	{
		$this->connect();
		
		// create database
		$sql = "CREATE TABLE if not exists `".$key."` (
					`id` varchar(255) primary key,
					`data` LONGBLOB 
				)ENGINE=innodb";
	
		$this->pdo->query($sql);
	}

	public function destruct($key)
	{
		$sql = "DROP TABLE `" . $key . "`";
		$this->pdo->query($sql);
	}

	/**
	 * Save value
	 * @param string
	 * @param string
	 */
	public function store($container, $key, $val)
	{
		$sql = "INSERT INTO ".$container." (id, data)
			    VALUES (".$this->pdo->quote($key).",".$this->pdo->quote(gzcompress(json_encode($val))).")";

		$this->pdo->query($sql);
	}

	/**
	 * Retrieve value
	 * @param string
	 */
	public function get($container, $key)
	{
		$sql = "SELECT data
				FROM ".$container." 
				WHERE id=".$this->pdo->quote($key);

		$stmt = $this->pdo->query($sql);
		$data = $stmt->fetchAll()[0];
		
		$data['data'] = json_decode(gzuncompress($data['data']), true);
		
		return $data['data']; 
	}

	/**
	 * Remove value
	 * @param string
	 */
	public function remove($key)
	{

	}
 	
 	public function sleep()
    {
    	$d = array(
    		'class'  => __CLASS__,
    		'params' => $this->params
    	);

    	return json_encode($d);
    }

    public function wake($params)
    {
    	$this->params = $params;
    	$this->connect();
    }
}	
