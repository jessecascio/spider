<?php

namespace Spider\Storage;

/**
 * Storage decorator
 *
 * @package Storage
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
interface Storage
{
	/**
	 * Set table name
	 */
	public function table($table);
	
	/**
	 * Storage setup
	 */
	public function init();

	/**
	 * Storage teardown
	 */
	public function destruct();

	/**
	 * @param string
	 * @param mixed
	 */
	public function store($id, $data);

	/**
	 * @param string
	 */
	public function get($id);

	/**
	 * @param array
	 */
	public function all(array $ids);

	/**
 	 * Encoded params
 	 * @return string
 	 */
 	public function sleep();

	 /**
     * Re-connect
     * @param array
     */
    public function wake(array $params);
}