<?php

declare(strict_types=1);

namespace ReactMvc\Session;

/**
 * SessionCollector
 *
 * @package ReactMvc\Session
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class SessionCollector
{

    /** @var array<\ReactMvc\Session\Session> */
    private array $sessions = [];

    /**
     * @param \ReactMvc\Session\Session $session
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
     * @return \ReactMvc\Session\Session|null
     * @throws \Exception
     */
    public function get(string $hash): ?Session
    {
        return $this->sessions[$hash] ?? null;
    }
}