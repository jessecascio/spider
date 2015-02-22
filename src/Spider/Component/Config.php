<?php

namespace Spider\Component;

/**
 * Parse an .ini file for config options
 *
 * @package Nest
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class Config
{	
	/**
	 * @var array
	 */
	private $data = array();

	/**
	 * Path to .ini file
	 * @param string
	 */
	public function __construct($ini='')
	{
		if (!is_file($ini)) {
			return;
		}

		// test passing in an invalid file
		$this->data = parse_ini_file($ini);
	}

	/**
	 * @param mixed
	 * @param mixed
	 */
	public function __set($key, $val)
	{
		$this->data[$key] = $val;
	}

	/**
	 * @param  mixed
	 * @return mixed
	 */
	public function __get($key) 
	{
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}

}

