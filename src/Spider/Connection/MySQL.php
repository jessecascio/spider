<?php

namespace Spider\Connection;

use PDO;
use PDOException;

/**
 * MySQL connection
 *
 * @package Connection
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class MySQL implements Decorator
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

		// verfies connection
		$this->connect();
	}

	/**
	 * Connect to database
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
	 * Run a query
	 * @param string
	 * @return array
	 */
	public function query($sql)
	{
		$stmt = $this->pdo->query($sql);
		return $stmt->fetchAll();
	}

	/**
	 * Encoded connection params
	 * @return string
	 */
	public function sleep()
    {
        return json_encode($this->params);
    }
 	
 	/**
	 * Connection params
	 * @return array
	 */
 	public function params()
 	{
 		return $this->params;
 	}
}
