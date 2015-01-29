<?php

namespace Spider\Connection;

/**
 * Connection decorator
 *
 * @package Connection
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
interface Decorator
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
}