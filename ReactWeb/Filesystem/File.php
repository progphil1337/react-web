<?php

declare(strict_types=1);

namespace ReactWeb\Filesystem;

use ReactWeb\Filesystem\Enum\FileType;
use ReactWeb\Filesystem\Exception\FileNotFoundException;
use ReactWeb\HTTP\Response\FileResponse;

/**
 * File
 *
 * @package ReactWeb\Filesystem
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
class File
{

    private readonly string $name;

    /**
     * @param string $path
     * @throws \ReactWeb\Filesystem\Exception\FileNotFoundException
     */
    public function __construct(private readonly string $path)
    {
        if (!file_exists($path) || is_dir($this->path)) {
            throw new FileNotFoundException($path);
        }

        $split = explode(DIRECTORY_SEPARATOR, $path);
        $this->name = end($split);
    }

    /**
     * @param array $config
     * @return \ReactWeb\HTTP\Response\FileResponse
     */
    public function createResponse(array $config): FileResponse
    {
        $response = new FileResponse(
            $this->name,
            FileType::getFromString($this->path)->getContentType(),
            $this->path
        );

        $response->writeHeader('Cache-Control', str_replace('%cache_duration%', (string)$config['cache_duration'], $config['cache_control']));
        $response->writeHeader('Date', gmdate('D, d M Y H:i:s', time()) . ' GMT');
        $response->writeHeader('Last-Modified', sprintf('%s GMT', gmdate('D, d M Y H:i:s', filemtime($this->path))));
        $response->writeHeader('Expires', sprintf('%s GMT', gmdate('D, d M Y H:i:s', time() + $config['cache_duration'])));

        return $response;
    }

}