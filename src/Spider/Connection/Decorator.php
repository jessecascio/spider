<?php

namespace Spider\Connection;

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