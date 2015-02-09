<?php

namespace Spider\Component;

class Config
{
	private $data = array();

	public function __construct($ini_path)
	{
		// if (!file_exists($ini_path)), faster!
		if ($d = parse_ini_file($ini_path)) {
			foreach ($d as $key => $val) {
				$this->data[$key] = $val;
			}
		}
	}

	public function __get($key) 
	{
		return $this->data[$key];
	}
}