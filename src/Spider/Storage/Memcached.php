<?php

namespace Spider\Storage;

use Exception;

/**
 * Memcached driver
 *
 * @package Storage
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class Memcached implements Storage
{
	/**
	 * @var Memcached
	 */
	private $Memcached = null;

	private $params = array();

	private $table = '';

	private $keys = array();

	public function __construct($host='127.0.0.1', $port=11211)
	{
		$this->Memcached = new \Memcached();
		$this->params['host'] = $host;
		$this->params['port'] = intval($port);
	}

	private function connect()
	{
		// @todo add multiserver support
		$this->Memcached->addServer($this->params['host'], $this->params['port']);
	}

	public function table($table)
	{
		$this->table = $table;
	}

	public function destruct()
	{
		$this->Memcached->deleteMulti($this->keys);
	}

	/**
	 * Driver setup
	 */
	public function init()
	{
		// verify connection
		$this->connect();
	}

	/**
	 * Save value
	 * @param string
	 * @param string
	 */
	public function store($key, $val)
	{
		$val = gzcompress(json_encode($val));
		$this->Memcached->set($this->table.'_'.$key, $val, 600);
	}

	/**
	 * Retrieve value
	 * @param  string
	 * @return mixed
	 */
	public function get($key)
	{	
		$this->keys[] = $this->table.'_'.$key;
		$val = $this->Memcached->get($this->table.'_'.$key);
		// @todo add checking
		return json_decode(gzuncompress($val),true);
	}

	public function all(array $ids)
	{
		$this->keys = array();

		$this->keys = preg_filter('/^/', $this->table.'_', $ids);
		$data = $this->Memcached->getMulti($this->keys);
		
		$result = array();

		foreach ($data as $key => $val) {
			$result[str_replace($this->table.'_', '', $key)] = json_decode(gzuncompress($val),true);
		}

		return $result;
	}

	public function wake($params)
	{
		$this->params = $params;
		$this->connect();
	}

	public function sleep()
	{
		$d = array(
    		'class'  => __CLASS__,
    		'params' => $this->params
    	);

    	return json_encode($d);
	}
}
