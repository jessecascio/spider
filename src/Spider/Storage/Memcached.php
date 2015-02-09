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
class Memcached implements Decorator
{
	/**
	 * @var Memcached
	 */
	private $Memcached = null;

	private $params = array();

	private $table = '';

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
		// need a way to clear
		// @todo $this->Memcached->deleteMulti()
		$this->Memcached = null;
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
		// figure out time, or part of destruct
		$val = gzcompress(json_encode($val));
		$this->Memcached->set($this->table.'_'.$key, $val, 60);
	}

	/**
	 * Retrieve value
	 * @param  string
	 * @return mixed
	 */
	public function get($key)
	{
		$val = $this->Memcached->get($this->table.'_'.$key);
		// @todo add checking
		return json_decode(gzuncompress($val),true);
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
