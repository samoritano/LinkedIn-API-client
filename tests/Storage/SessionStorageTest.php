<?php

namespace Bokokode\LinkedIn\Storage;

use Mockery as m;

/**
 * Class SessionStorageTest.
 *
 * @author Tobias Nyholm
 */
class SessionStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Bokokode\LinkedIn\Storage\SessionStorage storage
     */
    protected $storage;

    protected $prefix = 'linkedIn_';

    public function setUp()
    {
        $this->storage = new SessionStorage();
    }

    public function testSet()
    {
        $this->storage->set('code', 'foobar');
        $this->assertEquals($_SESSION[$this->prefix.'code'], 'foobar');
    }

    /**
     * @expectedException \Bokokode\LinkedIn\Exception\InvalidArgumentException
     */
    public function testSetFail()
    {
        $this->storage->set('foobar', 'baz');
    }

    public function testGet()
    {
        unset($_SESSION[$this->prefix.'state']);
        $result = $this->storage->get('state');
        $this->assertNull($result);

        $expected = 'foobar';
        $_SESSION[$this->prefix.'code'] = $expected;
        $result = $this->storage->get('code');
        $this->assertEquals($expected, $result);
    }

    public function testClear()
    {
        $_SESSION[$this->prefix.'code'] = 'foobar';
        $this->storage->clear('code');
        $this->assertFalse(isset($_SESSION[$this->prefix.'code']));
    }

    /**
     * @expectedException \Bokokode\LinkedIn\Exception\InvalidArgumentException
     */
    public function testClearFail()
    {
        $this->storage->clear('foobar');
    }

    public function testClearAll()
    {
        $validKeys = SessionStorage::$validKeys;

        $storage = m::mock('Bokokode\LinkedIn\Storage\SessionStorage[clear]')
            ->shouldReceive('clear')->times(count($validKeys))
            ->with(m::on(function ($arg) use ($validKeys) {
                return in_array($arg, $validKeys);
            }))
            ->getMock();

        $storage->clearAll();
    }
}
