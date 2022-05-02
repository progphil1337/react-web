<?php

declare(strict_types=1);

namespace ReactMvc\Session;

/**
 * Session
 *
 * @package ReactMvc\Session
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class Session
{
    /** @var array<string,mixed> */
    private ?array $metaData = null;
    private readonly string $filePath;

    /**
     * @param int $id
     * @param string $hash
     * @param string $file
     * @param \DateTime $created
     * @param \DateTime $expires
     * @param \ReactMvc\Session\SessionManager $manager
     */
    public function __construct(
        public  readonly int $id,
        public  readonly string $hash,
        public  readonly string $file,
        public  readonly \DateTime $created,
        public  readonly \DateTime $expires,
        private readonly SessionManager $manager,
        bool    $initialWrite = false
    )
    {
        $this->filePath = $this->manager->getFilePath($file);

        if ($initialWrite) {
            $this->metaData = [];
            $this->save();
        }
    }

    public function get(string $key): mixed
    {
        if ($this->metaData === null) {
            $this->read();
        }

        return $this->metaData[$key];
    }

    public function set(string $key, mixed $value): self
    {
        $this->metaData[$key] = $value;

        return $this;
    }

    private function read(): void
    {
        $this->metaData = json_decode(base64_decode(file_get_contents($this->filePath)), true);
    }

    public function save(): bool
    {
        return (bool)file_put_contents($this->filePath, base64_encode(json_encode($this->metaData)));
    }
}