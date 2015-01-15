<?php

namespace Spider\Driver;

/**
 * Driver decorator
 *
 * @package Driver
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
interface Driver
{
	/**
	 * Diff driver types
	 */
	const MYSQL     = 1;
	const MEMCACHED = 2;
	const MONGODB   = 3;

	/**
	 * Driver setup
	 */
	public function init();

	/**
	 * Save value
	 * @param string
	 * @param string
	 */
	public function set($key, $val);

	/**
	 * Retrieve value
	 * @param string
	 */
	public function get($key);

	/**
	 * Remove value
	 * @param string
	 */
	public function remove($key);
}