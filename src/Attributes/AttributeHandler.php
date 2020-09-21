<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    MIT
 */

declare(strict_types=1);

namespace Windwalker\DI\Attributes;

use Windwalker\DI\AttributesResolver;
use Windwalker\DI\Container;

/**
 * The AttributeHandler class.
 */
class AttributeHandler extends \Windwalker\Attributes\AttributeHandler
{
    protected Container $container;

    /**
     * @inheritDoc
     */
    public function __construct(callable $handler, \Reflector $reflactor, AttributesResolver $resolver, Container $container)
    {
        parent::__construct($handler, $reflactor, $resolver);

        $this->container = $container;
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @return AttributesResolver
     */
    public function getResolver(): AttributesResolver
    {
        return $this->resolver;
    }
}
