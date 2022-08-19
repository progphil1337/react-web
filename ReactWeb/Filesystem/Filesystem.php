<?php

declare(strict_types=1);

namespace ReactWeb\Filesystem;

use ReactWeb\Config\Config;
use ReactWeb\Filesystem\Exception\FileNotFoundException;

/**
 * Filesystem
 *
 * @package ReactWeb\Filesystem
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
class Filesystem
{
    public function __construct(private readonly Config $config)
    {

    }

    public function find(string $file): ?File
    {
        $path = PROJECT_PATH . $this->config->get('Filesystem::public_directory') . str_replace('/', DIRECTORY_SEPARATOR, $file);

        try {
            $file = new File($path);
        } catch(FileNotFoundException) {
            return null;
        }

         return $file;
    }
}