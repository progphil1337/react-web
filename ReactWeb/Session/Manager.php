<?php

declare(strict_types=1);

namespace ReactWeb\Session;

use DateInterval;
use DateTime;
use Fiber;
use React\ChildProcess\Process;
use ReactWeb\Config\Config;
use ReactWeb\DependencyInjection\Singleton;
use ReactWeb\Logger\Logger;
use RuntimeException;
use SQLite3;

/**
 * Manager
 *
 * @package ReactWeb\Session
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class Manager implements Singleton
{
    private const DATABASE_NAME = 'session.sqlite';

    private readonly string $directoryPath;
    private readonly string $filePath;
    private SQLite3 $database;
    private bool $open = false;
    private readonly Collector $collector;

    /**
     * @param \ReactWeb\Config\Config $config
     */
    public function __construct(private readonly Config $config)
    {
        $this->directoryPath = PROJECT_PATH . DIRECTORY_SEPARATOR . $this->config->get('Session::file');
        $this->filePath = sprintf('%s%s', $this->directoryPath, self::DATABASE_NAME);
        if (!file_exists($this->filePath)) {
            $this->createDatabase();
        }

        $this->collector = new Collector();

        $this->open();
    }

    /**
     * Open connection
     *
     * @return bool
     */
    public function open(): bool
    {
        $this->database = new SQLite3($this->filePath);

        $this->open = true;
        return true;
    }

    /**
     * @param string $file
     * @return string
     */
    public function getFilePath(string $file): string
    {
        return sprintf('%s%s.data', $this->directoryPath, $file);
    }

    /**
     * @param string $hash
     * @return \ReactWeb\Session\Session|null
     * @throws \Exception
     */
    public function getByHash(string $hash): ?Session
    {
        if ($this->collector->has($hash)) {
            $session = $this->collector->get($hash);
        } else {

            $statement = $this->database->prepare('SELECT * FROM `session` WHERE `hash` = :hash');
            $statement->bindValue(':hash', $hash);
            $result = $statement->execute();
            $data = $result->fetchArray();

            if ($data === false) {
                return null;
            }

            $session = new Session(
                $data['id'],
                $data['hash'],
                $data['file'],
                new DateTime($data['created']),
                new DateTime($data['expires']),
                $this
            );

            $this->collector->add($session);
        }

        if ($session->expires < new DateTime()) {
            unlink($this->getFilePath($session->file));
            $this->collector->remove($hash);

            $statement = $this->database->prepare('DELETE FROM `session` WHERE `hash` = :hash');
            $statement->bindValue(':hash', $hash);
            $statement->execute();

            unset($session);

            return null;
        }

        return $session;
    }

    /**
     * @return \ReactWeb\Session\Session
     * @throws \Exception
     */
    public function createSession(): Session
    {
        if (!$this->open) {
            throw new RuntimeException('Session is not connected');
        }

        $created = new DateTime();
        $expires = $created->add(new DateInterval($this->config->get('Session::lifetime')));

        $i = 0;
        do {
            $hash = md5(sprintf('%s-%s-%s', $created->getTimestamp(), $expires->getTimestamp(), $i++));
        } while ($this->hashExists($hash));

        $i = 0;
        do {
            $file = md5(sprintf('%s-%s', $hash, $i++));
        } while ($this->fileExists($file));

        $this->database->query(
            sprintf('INSERT INTO `session` (`hash`, `file`, `expires`) VALUES ("%s", "%s", "%s")',
                $hash, $file, $expires->format('Y-m-d H:i:s'))
        );

        $session = new Session(
            id: $this->database->lastInsertRowID(),
            hash: $hash,
            file: $file,
            created: $created,
            expires: $expires,
            manager: $this,
            initialWrite: true
        );

        $this->collector->add($session);

        return $session;
    }

    /**
     * @param string $hash
     * @return bool
     */
    private function hashExists(string $hash): bool
    {
        $result = $this->database->querySingle(sprintf('SELECT COUNT(`id`) FROM `session` WHERE `hash` = "%s";', $hash));

        if ($result === 0) {
            return false;
        }

        return true;
    }

    /**
     * @param string $file
     * @return bool
     */
    private function fileExists(string $file): bool
    {
        return file_exists($this->getFilePath($file));
    }

    /**
     * @return void
     */
    private function createDatabase(): void
    {
        Logger::debug($this, 'Creating session database file');
        fwrite(fopen($this->filePath, "wb"), '');

        $this->open();

        // create table
        $this->database->query(<<<SQL
CREATE TABLE `session` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    `hash` VARCHAR(32),
    `file` VARCHAR(32),
    `created` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `expires` DATETIME NOT NULL
)
SQL
        );

    }
}