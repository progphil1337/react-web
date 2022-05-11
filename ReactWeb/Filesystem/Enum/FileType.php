<?php

declare(strict_types=1);

namespace ReactWeb\Filesystem\Enum;

/**
 * FileType
 *
 * @package ReactWeb\Filesystem\Enum
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
enum FileType: string
{

    case JS = 'js';
    case JSON = 'json';
    case CSS = 'css';
    case PNG = 'png';
    case JPEG = 'jpeg';
    case GIF = 'gif';
    case ICO = 'ico';
    case XML = 'xml';
    case APNG = 'apng';
    case AVIF = 'avif';
    case SVG = 'svg';
    case WEBP = 'webp';

    public static function getFromString(string $path): ?self
    {
        $split = explode('.', $path);
        $ending = end($split);

        return self::tryFrom(mb_strtolower($ending));
    }

    public function getContentType(): string
    {
        return match ($this) {
            self::JS, self::JSON => 'application/javascript',
            self::CSS => 'text/css',
            self::PNG => 'image/png',
            self::JPEG => 'image/jpeg',
            self::GIF => 'image/gif',
            self::APNG => 'image/apng',
            self::AVIF => 'image/avif',
            self::SVG => 'image/svg+xml',
            self::WEBP => 'image/webp',
            self::ICO => 'image/x-icon',
            self::XML => 'application/xml',
            default => ''
        };
    }
}