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

	/**
	 * @var string
	 */
	private $table = '';

	/**
     * @param string - database
	 * @param string - user
	 * @param string - password
	 * @param string - host
	 * @param int    - port
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
	 * @throws PDOException
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
	 * @throws PDOException
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
	 * @throws PDOException
	 */
	public function destruct()
	{
		$sql = "DROP TABLE `" . $this->table . "`";
		$this->pdo->query($sql);
	}

	/**
	 * @param  string
	 * @param  mixed
	 * @throws PDOException
	 */
	public function store($id, $data)
	{
		$sql = "INSERT INTO `".$this->table."` (id, data)
			    VALUES (".$this->pdo->quote($id).",".$this->pdo->quote(gzcompress(json_encode($data))).")";

		$this->pdo->query($sql);
	}

	/**
	 * @param  string
	 * @return mixed
	 * @throws PDOException
	 */
	public function get($id)
	{
		$sql = "SELECT data
				FROM `".$this->table."` 
				WHERE id=".$this->pdo->quote($id);

		$stmt = $this->pdo->query($sql);
		$data = $stmt->fetchAll();
		
		if (!count($data) || !isset($data['data'])) {
			return null;
		}

		$data[0]['data'] = json_decode(gzuncompress($data[0]['data']), true);
			
		return $data[0]['data']; 
	}

	/**
	 * @param array
	 * @throws PDOException
	 */
	public function all(array $ids)
	{
		$sql = "SELECT id, data
				FROM `".$this->table."` 
				WHERE id IN ('".implode("','", $ids)."')";

		$stmt = $this->pdo->query($sql);
		$data = $stmt->fetchAll();

		$result = array();

		foreach ($data as $item) {
			$result[$item['id']] = json_decode(gzuncompress($item['data']), true);
		}
		
		return $result; 
	}
 	
 	/**
 	 * @return string - encoded params
 	 */
 	public function sleep()
    {
    	$d = array(
    		'class'  => __CLASS__, // needed for correct instantiation
    		'params' => $this->params
    	);

    	return json_encode($d);
    }

    /**
     * @param array - re-connect
     * @throws PDOException
     */
    public function wake(array $params)
    {
    	$this->params = $params;
    	$this->connect();
    }
}	
