<?php

namespace Spider\Connection;

/**
 * Connection decorator
 *
 * @package Connection
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
interface Connection
{
	/**
	 * Connection params
	 * @return array
	 */
	public function params();

	/**
	 * Encoded connection params
	 * @return string
	 */
	public function sleep();

	/**
	 * @param string
	 * @return array
	 */
	public function query($sql);
}
