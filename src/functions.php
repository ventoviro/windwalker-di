<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    MIT
 */

declare(strict_types=1);

namespace Windwalker\DI {

    use Windwalker\DI\Definition\ObjectBuilderDefinition;
    use Windwalker\DI\Definition\StoreDefinition;

    if (!function_exists('create')) {
        function create(string|callable $class, ...$args): ObjectBuilderDefinition
        {
            return Container::define($class, $args);
        }
    }

    if (!function_exists('share')) {
        function share(string|callable $class, ...$args): StoreDefinition
        {
            return new StoreDefinition(
                Container::define($class, $args),
                Container::SHARED
            );
        }
    }

    if (!function_exists('prepare')) {
        function prepare(string $class, ?callable $extend, int $options = 0): StoreDefinition
        {
            $def = new StoreDefinition(
                new ObjectBuilderDefinition($class),
                $options
            );

            return $def->extend($extend);
        }
    }

    if (!function_exists('prepare_shared')) {
        function prepare_shared(string $class, ?callable $extend, int $options = 0): StoreDefinition
        {
            return prepare($class, $extend, $options | Container::SHARED);
        }
    }
}
