<?php

declare(strict_types=1);

namespace ReactWeb\DependencyInjection;

use ReactWeb\Logger\Logger;

/**
 * Injector
 *
 * @package ReactWeb\DependencyInjection
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
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

                if (!array_key_exists('__construct', $methods)) {
                    foreach ($constructor->getParameters() as $parameter) {
                        $name = $this->lookup->getResolvedClassName($parameter->getType()->getName());

                        $dependency = $this->create($name);

                        $this->lookup->register($dependency);

                        $dependencies[] = $dependency;
                    }
                } else {
                    $dependencies = $methods['__construct'];
                    unset($methods['__construct']);
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

    /**
     * @return \ReactWeb\DependencyInjection\ClassLookup
     */
    public function getLookup(): ClassLookup
    {
        return $this->lookup;
    }
}