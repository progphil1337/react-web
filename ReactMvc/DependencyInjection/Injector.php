<?php

declare(strict_types=1);

namespace ReactMvc\DependencyInjection;

use ReactMvc\Logger\Logger;

/**
 * Injector
 *
 * @package ReactMvc\DependencyInjection
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class Injector
{
    public function __construct(private readonly ClassLookup $lookup)
    {
    }

    /**
     * @param string $className
     * @param array $methods
     * @return ?object
     */
    public function create(string $className, array $methods = []): ?object
    {
        if (!$this->lookup->isRegistered($className)) {
            try {
                $reflectionClass = new \ReflectionClass($className);
            } catch (\ReflectionException $e) {
                Logger::error($this, $e->getMessage());

                return null;
            }

            $constructor = $reflectionClass->getConstructor();

            if ($constructor === null) {
                try {
                    $instance = $reflectionClass->newInstance();
                } catch (\ReflectionException $e) {
                    Logger::error($this, sprintf('Error creating reflection without args: %s', $e->getMessage()));
                }

                $this->lookup->register($instance);
            } else {
                if ($constructor->isPrivate()) {
                    Logger::error($this, 'Cannot create private constructors');
                }

                $dependencies = [];

                foreach ($constructor->getParameters() as $parameter) {
                    $name = $this->lookup->getResolvedClassName($parameter->getType()->getName());

                    $dependency = $this->create($name);

                    $this->lookup->register($dependency);

                    $dependencies[] = $dependency;
                }

                try {
                    $instance = $reflectionClass->newInstanceArgs($dependencies);

                    foreach ($methods as $method => $args) {
                        call_user_func_array([$instance, $method], $args);
                    }

                    $this->lookup->register($instance);
                } catch (\ReflectionException $e) {
                    Logger::error($this, sprintf('Error creating reflection with args: %s', $e->getMessage()));
                }
            }

        } else {
            $instance = $this->lookup->get($className);
        }

        return $instance;
    }
}