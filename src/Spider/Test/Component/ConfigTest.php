<?php

use Spider\Component\Config;

/**
 * @package   Test
 * @author    Jesse Cascio <jessecascio@gmail.com>
 * @copyright jessesnet.com
 */
class ConfigTest extends PHPUnit_Framework_TestCase
{   
    private $Config;

    public function setUp()
    {
        $this->Config = new Config();
    }

    public function testUniqueTable()
    {
        $this->assertTrue(strlen($this->Config->getTable()) == 32);

        $Config2 = new Config();
        $this->assertTrue(strlen($Config2->getTable()) == 32);

        $this->assertFalse($this->Config->getTable() == $Config2->getTable());
    }

    public function testParse()
    {
        $this->Config->parse(__DIR__."/parameters.ini");

        $data = parse_ini_file(__DIR__."/parameters.ini");

        $this->assertTrue($this->Config->getProcesses() == $data['processes']);
        $this->assertTrue($this->Config->getMemory() == $data['memory']);
        $this->assertTrue($this->Config->getTrace() == $data['trace']);
        
        unlink($data['trace']);
    }

    public function testSetProcesses()
    {
        $this->Config->processes(0);
        $this->assertTrue($this->Config->getProcesses() == 5);

        $this->Config->processes(-10);
        $this->assertTrue($this->Config->getProcesses() == 5);

        $this->Config->processes('asdasds');
        $this->assertTrue($this->Config->getProcesses() == 5);

        $this->Config->processes(15);
        $this->assertTrue($this->Config->getProcesses() == 15); 
    }

    public function testSetMemory()
    {
        $this->Config->memory(0);
        $this->assertTrue($this->Config->getMemory() == 100);

        $this->Config->memory(-10);
        $this->assertTrue($this->Config->getMemory() == 100);

        $this->Config->memory('asdasds');
        $this->assertTrue($this->Config->getMemory() == 100);

        $this->Config->memory(15);
        $this->assertTrue($this->Config->getMemory() == 15); 
    }

    public function testSetTrace()
    {
        $this->Config->trace(0);
        $this->assertTrue($this->Config->getTrace() == "/dev/null");

        $this->Config->trace('asds');
        $this->assertTrue($this->Config->getTrace() == "/dev/null");

        $this->Config->trace('okee.txt');
        $this->assertTrue($this->Config->getTrace() == "okee.txt");
        unlink('okee.txt');
    }

    /**
     * @expectedException LogicException
     */
    public function testRequireConnection()
    {
        $this->Config->getStorage();
    }
}