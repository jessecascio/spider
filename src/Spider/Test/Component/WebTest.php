<?php

use Spider\Component\Config;

/**
 * @package   Test
 * @author    Jesse Cascio <jessecascio@gmail.com>
 * @copyright jessesnet.com
 */
class WebTest extends PHPUnit_Framework_TestCase
{   
    private $Config;

    private $Connection;

    private $Nest;

    private $Web;

    private $Storage;

    private $queries;

    public function setUp()
    {
        $this->Connection = \Phake::mock('\Spider\Connection\Connection');
        $this->Config     = \Phake::mock('\Spider\Component\Config');
        $this->Nest       = \Phake::mock('\Spider\Component\Nest');
        $this->Web        = \Phake::mock('\Spider\Component\Web');
        $this->Storage    = \Phake::mock('\Spider\Storage\MySQL');

        // inject mocks
        $this->Config->setStorage($this->Storage);
        $this->Web->setNest($this->Nest);

        $this->queries = ['SELECT SLEEP(FLOOR(0 + (RAND() * 1)))', 'SELECT SLEEP(FLOOR(0 + (RAND() * 1)))'];
    }

    public function testNoQueries()
    {
        $this->Web->crawl();

        // assertion maximus
        $this->assertTrue(is_array($this->Web->results()));
        $this->assertEquals(count($this->Web->results()), 0);
    }

    public function testStorageInit()
    {
        $this->Web->queries($this->queries);
        $this->Web->crawl();

        // assertion maximus
        \Phake::verify($this->Storage, \Phake::times(1))->table(\Phake::anyParameters());
        \Phake::verify($this->Storage, \Phake::times(1))->init();
    }

    public function testNestSpawn()
    {
        Phake::when($this->Nest)->spawn()->thenReturn(1);

        $this->Web->queries($this->queries);
        $this->Web->crawl();

        // assertion maximus
        \Phake::verify($this->Storage, \Phake::times(count($this->queries)))->spawn();
    }

    public function testResults()
    {
        Phake::when($this->Nest)->spawn()->thenReturn(1);
        Phake::when($this->Config)->getStorage()->thenReturn($this->Storage);
        Phake::when($this->Storage)->all(\Phake::anyParameters())->thenReturn([2,4]);

        $this->Web->queries($this->queries);
        $this->Web->crawl();

        // assertion maximus
        \Phake::verify($this->Storage, \Phake::times(1))->all(\Phake::anyParameters());
        $this->assertTrue(is_array($this->Web->results()));
        $this->assertEquals(count($this->Web->results()), count($this->queries));
    }

    public function testResultCallbacks()
    {
        Phake::when($this->Nest)->spawn()->thenReturn(1);
        Phake::when($this->Config)->getStorage()->thenReturn($this->Storage);
        Phake::when($this->Storage)->get(\Phake::anyParameters())->thenReturn(mt_rand());

        $this->Web->queries($this->queries);
        $this->Web->crawl( function($data){return $data;} );

        // assertion maximus
        \Phake::verify($this->Storage, \Phake::times(count($this->queries)))->get(\Phake::anyParameters());
        $this->assertTrue(is_array($this->Web->results()));
        $this->assertEquals(count($this->Web->results()), count($this->queries));
    }

    public function testStorageDestruct()
    {
        $this->Web->queries($this->queries);
        $this->Web->crawl();

        // assertion maximus
        \Phake::verify($this->Storage, \Phake::times(1))->destruct();
    }
}