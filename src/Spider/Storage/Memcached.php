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

	/**
	 * @var array
	 */
	private $params = array();

	/**
	 * @var string
	 */
	private $table = '';

	/**
	 * @var array
	 */
	private $keys = array();

	/**
	 * @param string
	 * @param int
	 */
	public function __construct($host='127.0.0.1', $port=11211)
	{
		$this->Memcached = new \Memcached();
		$this->params['host'] = $host;
		$this->params['port'] = intval($port);
	}

	/**
	 * Connect
	 */
	private function connect()
	{
		// @todo add multiserver support
		$this->Memcached->addServer($this->params['host'], $this->params['port']);
	}

	/**
	 * @param string
	 */
	public function table($table)
	{
		$this->table = $table;
	}

	/**
	 * Tear down
	 */
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
	 * @param  string
	 * @param  string
	 * @throws RunTimeException
	 */
	public function store($key, $val)
	{
		$val = gzcompress(json_encode($val));
		if ($this->Memcached->set($this->table.'_'.$key, $val, 600) === false) {
			$msg  = $this->Memcached->getResultMessage();
			$code = $this->Memcached->getResultCode();
			throw new \RunTimeException("Memcached Error (store): " . $msg . "(".$code.")");
		}
	}

	/**
	 * Retrieve value
	 * @param  string
	 * @return mixed
	 * @throws RunTimeException
	 */
	public function get($key)
	{	
		$this->keys[] = $this->table.'_'.$key;
		$val = $this->Memcached->get($this->table.'_'.$key);
		
		if (!is_string($val)) {
			$msg  = $this->Memcached->getResultMessage();
			$code = $this->Memcached->getResultCode();
			throw new \RunTimeException("Memcached Error (get): " . $msg . "(".$code.")");
		}

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
