<?php

use Spider\Component\Config;

/**
 * @package   Test
 * @author    Jesse Cascio <jessecascio@gmail.com>
 * @copyright jessesnet.com
 */
class ConfigTest extends PHPUnit_Framework_TestCase
{   
    public function testEmptyConstruct()
    {
    	$Config = new Config();
        $this->assertInstanceOf('Spider\\Component\\Config', $Config);
    }

    public function testInvalidFile()
    {
		$Config = new Config('asd');
        $this->assertInstanceOf('Spider\\Component\\Config', $Config);     	
    }

    public function testInvalidProperty()
    {
    	$Config = new Config();
        $this->assertInstanceOf('Spider\\Component\\Config', $Config);
    	
    	$this->assertEquals(null, $Config->processes);
    }

    public function testValidProperty()
    {
    	$Config = new Config();
        $this->assertInstanceOf('Spider\\Component\\Config', $Config);
    	
    	$Config->processes = 10;

    	$this->assertEquals(10, $Config->processes);
    }
}