<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2008 - 2014 Asikart.com. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Windwalker\DI;

/**
 * Interface ContainerAwareInterface
 */
interface ContainerAwareInterface
{
	/**
	 * Get the DI container.
	 *
	 * @return  Container
	 *
	 * @throws  \UnexpectedValueException May be thrown if the container has not been set.
	 */
	public function getContainer();

	/**
	 * Set the DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  mixed
	 */
	public function setContainer(Container $container);
}

