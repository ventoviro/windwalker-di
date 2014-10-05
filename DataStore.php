<?php
/**
 * Part of windwalker project. 
 *
 * @copyright  Copyright (C) 2014 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\DI;

/**
 * The DataStore class.
 * 
 * @since  {DEPLOY_VERSION}
 */
class DataStore
{
	/**
	 * Property callback.
	 *
	 * @var callable
	 */
	protected $callback;

	/**
	 * Property shared.
	 *
	 * @var  bool
	 */
	protected $shared = true;

	/**
	 * Property protected.
	 *
	 * @var  bool
	 */
	protected $protected = false;

	/**
	 * Property instance.
	 *
	 * @var mixed
	 */
	protected $instance;

	/**
	 * Class init.
	 *
	 * @param callable $callback
	 * @param boolean  $shared
	 * @param boolean  $protected
	 */
	public function __construct($callback, $shared = false, $protected = false)
	{
		$this->setCallback($callback);

		$this->protected = $protected;
		$this->shared    = $shared;
	}

	/**
	 * get
	 *
	 * @param Container $container
	 * @param boolean   $forceNew
	 *
	 * @return  mixed
	 */
	public function get($container, $forceNew = false)
	{
		if ($this->shared)
		{
			if (empty($this->instances) || $forceNew)
			{
				$this->instances = call_user_func($this->callback, $container);
			}

			return $this->instances;
		}

		return call_user_func($this->callback, $container);
	}

	/**
	 * Method to get property Callback
	 *
	 * @return  callable
	 */
	public function getCallback()
	{
		return $this->callback;
	}

	/**
	 * Method to set property callback
	 *
	 * @param   callable $callback
	 *
	 * @throws  \InvalidArgumentException
	 * @return  static  Return self to support chaining.
	 */
	public function setCallback($callback)
	{
		// If the provided $value is not a closure, make it one now for easy resolution.
		if (!is_callable($callback))
		{
			$callback = function () use ($callback) {
				return $callback;
			};
		}

		if ($this->protected)
		{
			return $this;
		}

		$this->callback = $callback;

		return $this;
	}

	/**
	 * Method to get property Shared
	 *
	 * @param   boolean $shared
	 *
	 * @return  boolean
	 */
	public function isShared($shared = null)
	{
		if ($shared !== null)
		{
			$this->shared = $shared;
		}

		return $this->shared;
	}

	/**
	 * Method to get property Protected
	 *
	 * @param   boolean $protected
	 *
	 * @return  boolean
	 */
	public function isProtected($protected = null)
	{
		if ($protected !== null)
		{
			$this->protected = $protected;
		}

		return $this->protected;
	}
}
