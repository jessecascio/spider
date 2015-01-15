<?php

namespace Spider\Driver;

use mysqli;
use Exception;

/**
 * MySQL driver
 *
 * @package Driver
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class MySQL implements Driver
{
	/**
	 * @var mysqli
	 */
	private $mysqli = null;

	/**
     * @param string - database
	 * @param string - user
	 * @param string - password
	 * @param string - host
	 * @param int    - port
	 * @throws Exception
	 */
	public function __construct($db='test',$usr='root',$pwd='',$hst='127.0.0.1',$prt=3306)
	{
		$this->mysqli = new mysqli($hst, $usr, $pwd, $db, $prt);

		if ($this->mysqli->connect_error) {
			throw new Exception($this->mysqli->connect_error);
		}
	}

	/**
	 * Driver setup
	 */
	public function init()
	{
		// create database
	}

	/**
	 * Save value
	 * @param string
	 * @param string
	 */
	public function set($key, $val)
	{

	}

	/**
	 * Retrieve value
	 * @param string
	 */
	public function get($key)
	{

	}

	/**
	 * Remove value
	 * @param string
	 */
	public function remove($key)
	{

	}

}
