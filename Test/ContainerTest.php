<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\DI\Test;

use Windwalker\DI\Container;
use Windwalker\DI\Test\Mock\StubStack;
use Windwalker\DI\Test\Stub\StubServiceProvider;

/**
 * Test class of Container
 *
 * @since {DEPLOY_VERSION}
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test instance.
	 *
	 * @var Container
	 */
	protected $instance;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->instance = new Container;

		$this->instance->set(
			'Hello',
			function()
			{
				return 'World';
			}
		);

		$this->instance->share(
			'flower',
			function()
			{
				return 'sakura';
			}
		);

		$this->instance->protect(
			'olive',
			function()
			{
				return 'peace';
			}
		);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
	}

	/**
	 * Method to test set().
	 *
	 * @return void
	 *
	 * @covers Windwalker\DI\Container::set
	 */
	public function testGetAndSet()
	{
		$container = new Container;

		// Not share, not protect
		$container->set(
			'flower',
			function()
			{
				return new \ArrayObject;
			},
			false,
			false
		);

		$this->assertInstanceOf('ArrayObject', $container->get('flower'));

		$this->assertNotSame($container->get('flower'), $container->get('flower'));

		// Share, not protect
		$container->set(
			'sakura',
			function()
			{
				return new \SplPriorityQueue;
			},
			true,
			false
		);

		$this->assertInstanceOf('SplPriorityQueue', $container->get('sakura'));

		$this->assertSame($container->get('sakura'), $container->get('sakura'));

		// Override it
		$container->set(
			'sakura',
			function()
			{
				return new \SplStack;
			},
			true,
			false
		);

		// Should be override
		$this->assertInstanceOf('SplStack', $container->get('sakura'));
	}

	/**
	 * testSetProtect
	 *
	 * @expectedException \OutOfBoundsException
	 *
	 * @return  void
	 */
	public function testSetAsProtect()
	{
		$container = new Container;

		// Share, Protect
		$container->set(
			'olive',
			function()
			{
				return new \SplStack;
			},
			true,
			true
		);

		$this->assertInstanceOf('SplStack', $container->get('olive'));

		$container->set(
			'olive',
			function()
			{
				return new \SplQueue;
			},
			true,
			false
		);

		// Should not be override
		$this->assertInstanceOf('SplStack', $container->get('olive'));
	}

	public function testGetFromParent()
	{
		$container = new Container($this->instance);

		$this->assertEquals('World', $container->get('Hello'));
	}

	/**
	 * Method to test protect().
	 *
	 * @return void
	 *
	 * @expectedException \OutOfBoundsException
	 *
	 * @covers Windwalker\DI\Container::protect
	 */
	public function testProtect()
	{
		$container = new Container;

		// Share, Protect
		$container->protect(
			'olive',
			function()
			{
				return new \SplStack;
			},
			true
		);

		$this->assertInstanceOf('SplStack', $container->get('olive'));

		$container->set(
			'olive',
			function()
			{
				return new \SplQueue;
			},
			true,
			false
		);

		// Should not be override
		$this->assertInstanceOf('SplStack', $container->get('olive'));
	}

	/**
	 * Method to test share().
	 *
	 * @return void
	 *
	 * @covers Windwalker\DI\Container::share
	 */
	public function testShare()
	{
		$container = new Container;

		// Share, not protect
		$container->share(
			'sakura',
			function()
			{
				return new \SplPriorityQueue;
			},
			false
		);

		$this->assertInstanceOf('SplPriorityQueue', $container->get('sakura'));

		$this->assertSame($container->get('sakura'), $container->get('sakura'));

		// Override it
		$container->set(
			'sakura',
			function()
			{
				return new \SplStack;
			},
			true,
			false
		);

		// Should be override
		$this->assertInstanceOf('SplStack', $container->get('sakura'));
	}

	/**
	 * Method to test alias().
	 *
	 * @return void
	 *
	 * @covers Windwalker\DI\Container::alias
	 */
	public function testAlias()
	{
		$this->instance->alias('foo', 'flower');

		$this->assertEquals('sakura', $this->instance->get('foo'));
	}

	/**
	 * Method to test exists().
	 *
	 * @return void
	 *
	 * @covers Windwalker\DI\Container::exists
	 */
	public function testExists()
	{
		$this->assertTrue($this->instance->exists('Hello'));
		$this->assertFalse($this->instance->exists('Wind'));
	}

	/**
	 * Method to test getNewInstance().
	 *
	 * @return void
	 *
	 * @covers Windwalker\DI\Container::getNewInstance
	 */
	public function testGetNewInstance()
	{
		$container = new Container;

		$container->share(
			'flower',
			function()
			{
				return new \ArrayObject;
			},
			false
		);

		$this->assertInstanceOf('ArrayObject', $container->get('flower'));

		$this->assertNotSame($container->get('flower'), $container->getNewInstance('flower'));
	}

	/**
	 * Method to test createObject().
	 *
	 * @return void
	 *
	 * @covers Windwalker\DI\Container::createObject
	 */
	public function testcreateObject()
	{
		$container = new Container;

		$foo = $container->createObject('Windwalker\\DI\\Test\\Mock\\Foo');

		$this->assertInstanceOf('Windwalker\\DI\\Test\\Mock\\Foo', $foo);
		$this->assertInstanceOf('Windwalker\\DI\\Test\\Mock\\Bar', $foo->bar);
		$this->assertInstanceOf('SplPriorityQueue', $foo->bar->queue);
		$this->assertInstanceOf('SplStack', $foo->bar->stack);

		// Bind a sub class
		$container = new Container;

		$container->share('SplStack', new StubStack);

		$foo = $container->createObject('Windwalker\\DI\\Test\\Mock\\Foo');

		$this->assertInstanceOf('Windwalker\\DI\\Test\\Mock\\StubStack', $foo->bar->stack);

		// Bind not shared classes
		$container = new Container;

		$container->set(
			'SplPriorityQueue',
			function()
			{
				return new \SplPriorityQueue;
			}
		);

		$queue = $container->get('SplPriorityQueue');

		$foo = $container->createObject('Windwalker\\DI\\Test\\Mock\\Foo');

		$this->assertNotSame($queue, $foo->bar->queue, 'Non shared class should be not same.');

		// Auto create classes should be not shared
		$container = new Container;

		$bar1 = $container->createObject('Windwalker\\DI\\Test\\Mock\\Bar');
		$bar2 = $container->createObject('Windwalker\\DI\\Test\\Mock\\Bar2');

		$this->assertNotSame($bar1->queue, $bar2->queue);

		// Not shared object
		$container = new Container;

		$foo = $container->createObject('Windwalker\\DI\\Test\\Mock\\Foo');
		$foo2 = $container->get('Windwalker\\DI\\Test\\Mock\\Foo');

		$this->assertNotSame($foo, $foo2);

		// Shared object
		$container = new Container;

		$foo = $container->createObject('Windwalker\\DI\\Test\\Mock\\Foo', true);
		$foo2 = $container->get('Windwalker\\DI\\Test\\Mock\\Foo');

		$this->assertSame($foo, $foo2);
	}

	/**
	 * Method to test createSharedObject().
	 *
	 * @return void
	 *
	 * @covers Windwalker\DI\Container::createSharedObject
	 */
	public function testcreateSharedObject()
	{
		$container = new Container;

		$foo = $container->createSharedObject('Windwalker\\DI\\Test\\Mock\\Foo');
		$foo2 = $container->get('Windwalker\\DI\\Test\\Mock\\Foo');

		$this->assertSame($foo, $foo2);
	}

	/**
	 * Method to test createChild().
	 *
	 * @return void
	 *
	 * @covers Windwalker\DI\Container::createChild
	 */
	public function testCreateChild()
	{
		$this->assertInstanceOf('Windwalker\\DI\\Container', $this->instance->createChild());
	}

	/**
	 * Method to test extend().
	 *
	 * @return void
	 *
	 * @covers Windwalker\DI\Container::extend
	 */
	public function testExtend()
	{
		$this->instance->extend(
			'Hello',
			function($value, $container)
			{
				return $value . '~~~!!!';
			}
		);

		$this->assertEquals('World~~~!!!', $this->instance->get('Hello'));
	}

	/**
	 * Method to test registerServiceProvider().
	 *
	 * @return void
	 *
	 * @covers Windwalker\DI\Container::registerServiceProvider
	 */
	public function testRegisterServiceProvider()
	{
		$this->instance->registerServiceProvider(new StubServiceProvider);

		$this->assertEquals('Bingo', $this->instance->get('bingo'));
	}
}
