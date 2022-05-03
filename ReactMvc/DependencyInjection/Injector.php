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
    /** @var array<string, mixed> */
    private array $lookup = [];

    /** @var array<string, string> */
    private array $aliases = [];

    /**
     * @param mixed $obj
     * @return $this
     */
    public function register(mixed $obj): self
    {
        Logger::debug($this, 'Register ' . get_class($obj));
        $this->lookup[get_class($obj)] = $obj;

        return $this;
    }

    /**
     * @param string $className
     * @param string $className2
     * @return $this
     */
    public function alias(string $className, string $className2): self
    {
        $this->aliases[$className] = $className2;

        return $this;
    }

    /**
     * @param string $className
     * @return bool
     */
    public function isRegistered(string $className): bool
    {
        $className = $this->getClassName($className);

        return array_key_exists($className, $this->lookup);
    }

    /**
     * @param string $className
     * @return mixed
     */
    public function get(string $className): mixed
    {
        $className = $this->getClassName($className);

        Logger::debug($this, 'Get ' . $className);
        return $this->lookup[$className] ?? null;
    }

    /**
     * @param string $className
     * @return object
     */
    public function create(string $className): mixed
    {
        $className = $this->getClassName($className);

        if (!$this->isRegistered($className)) {

            try {
                $reflectionClass = new \ReflectionClass($className);
            } catch (\ReflectionException $e) {
                Logger::error($this, $e->getMessage());

                return null;
            }

            $constructor = $reflectionClass->getConstructor();

            if ($constructor->isPrivate()) {
                Logger::error($this, 'Cannot create private constructors');
            }

            $dependencies = [];

            foreach ($constructor->getParameters() as $parameter) {
                $name = $this->getClassName($parameter->getType()->getName());

                if (!array_key_exists($name, $this->lookup)) {
                    $this->register($this->create($name));
                }

                $dependencies[] = $this->lookup[$name];
            }

            try {
                $instance = $reflectionClass->newInstanceArgs($dependencies);
                $this->register($instance);
            } catch (\ReflectionException $e) {
                Logger::error($this, sprintf('Error creating reflection: %s', $e->getMessage()));
            }
        }

        return $this->get($className);
    }

    /**
     * @param string $className
     * @return string
     */
    private function getClassName(string $className): string
    {
        if (array_key_exists($className, $this->aliases)) {
            $className = $this->aliases[$className];
        }

        return $className;
    }
}