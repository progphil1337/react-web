<?php

declare(strict_types=1);

namespace ReactMvc\DependencyInjection;

use ReactMvc\Logger\Logger;

/**
 * ClassLookup
 *
 * @package ReactMvc\DependencyInjection
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class ClassLookup
{

    /** @var array<string, object> */
    private array $lookup = [];

    /** @var array<string,bool> */
    private array $dismiss = [];

    /** @var array<string,string> */
    private array $aliases = [];

    /**
     * @param object $o
     * @return $this
     */
    public function register(object $o): self
    {
        if (!$this->shouldDismiss($this->getResolvedClassName($o)) && !$this->isRegistered($o)) {

            Logger::info($this, sprintf('Registering class %s', get_class($o)));

            $this->lookup[get_class($o)] = $o;
        }

        return $this;
    }

    /**
     * @param object|string $o
     * @return bool
     */
    public function shouldDismiss(object|string $o): bool
    {
        $className = $this->getClassName($o);

        if (
            array_key_exists($className, $this->dismiss) ||
            array_key_exists($this->getResolvedClassName($className), $this->dismiss)

        ) {
            return true;
        }

        foreach (array_keys($this->dismiss) as $class) {
            $resolvedName = $this->getResolvedClassName($class);

            if (is_subclass_of($className, $resolvedName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param object|string $o
     * @return bool
     */
    public function isRegistered(object|string $o): bool
    {
        return array_key_exists($this->getClassName($o), $this->lookup);
    }

    /**
     * @param object|string $o
     * @return object|null
     */
    public function get(object|string $o): ?object
    {
        return $this->lookup[$this->getResolvedClassName($o)] ?? null;
    }

    /**
     * @param object|string $o
     * @return $this
     */
    public function dismiss(object|string $o): self
    {
        $this->dismiss[$this->getClassName($o)] = true;

        return $this;
    }

    /**
     * @param object|string $o
     * @return string
     */
    public function getResolvedClassName(object|string $o): string
    {
        $oName = $this->getClassName($o);

        if (array_key_exists($oName, $this->aliases)) {
            return $this->getResolvedClassName($this->aliases[$oName]);
        }

        return $oName;
    }

    /**
     * @param object|string $o
     * @return string
     */
    private function getClassName(object|string $o): string
    {
        return is_string($o) ? $o : get_class($o);
    }

    /**
     * @param string|object $class1
     * @param string|object $class2
     * @return $this
     */
    public function alias(string|object $class1, string|object $class2): self
    {
        $this->aliases[$this->getClassName($class1)] = $this->getClassName($class2);

        return $this;
    }

}