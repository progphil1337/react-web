<?php

declare(strict_types=1);

namespace ReactWeb\Session;

/**
 * Collector
 *
 * @package ReactWeb\Session
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class Collector
{

    /** @var array<\ReactWeb\Session\Session> */
    private array $sessions = [];

    /**
     * @param \ReactWeb\Session\Session $session
     * @return void
     */
    public function add(Session $session): void
    {
        $this->sessions[$session->hash] = $session;
    }

    /**
     * @param string $hash
     * @return bool
     */
    public function has(string $hash): bool
    {
        return array_key_exists($hash, $this->sessions);
    }

    /**
     * @param string $hash
     * @return void
     */
    public function remove(string $hash): void
    {
        unset($this->sessions[$hash]);
    }

    /**
     * @param string $hash
     * @return \ReactWeb\Session\Session|null
     * @throws \Exception
     */
    public function get(string $hash): ?Session
    {
        return $this->sessions[$hash] ?? null;
    }
}