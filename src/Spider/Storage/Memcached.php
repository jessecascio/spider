<?php

namespace Spider\Storage;

use Exception;

/**
 * Memcached driver
 *
 * @package Driver
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class Memcached implements Storage
{
	/**
	 * @var Memcached
	 */
	private $Memcached = null;

	public function __construct($host=null, $port=11211)
	{
		$this->Memcached = new \Memcached();

		if (!is_null($host)) {
			$this->addServer($host, $port);
		}
	}

	public function addServer($host, $port)
	{
		$this->Memcached->addServer($host, $port);
	}

	public function addServers($servers)
	{
		$this->Memcached->addServers($servers);
	}

	/**
	 * Driver setup
	 */
	public function init()
	{
		// verify connection
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
