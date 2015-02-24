<?php

namespace Spider\Component;

use Monolog;
use Monolog\Handler\StreamHandler;

/**
 * Logger factory
 *
 * @package Component
 * @author  Jesse Cascio <jessecascio@gmail.com>
 * @see     jessesnet.com
 */
class Logger
{	
	/**
	 * @var Monolog\Logger
	 */
	private static $Logger;

	/**
	 * @param  string
	 * @return Monolog\Logger
	 */
	public static function instance($path)
	{
		if (!is_null(self::$Logger)) {
			return self::$Logger;
		}

		self::$Logger = new Monolog\Logger('Spider');
		self::$Logger->pushHandler(new StreamHandler($path));

		return self::$Logger;
	}
}

