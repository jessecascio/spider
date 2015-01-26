<?php

namespace Spider\Storage;

/**
 * Driver decorator
 *
 * @package Driver
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
interface Decorator
{
	/**
	 * Driver setup
	 */
	public function init($key);

	/**
	 * Save value
	 * @param string
	 * @param string
	 */
	public function store($container, $key, $val);

	/**
	 * Retrieve value
	 * @param string
	 */
	public function get($container, $key);

	public function sleep();

	public function wake($params);
}