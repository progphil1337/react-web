<?php

declare(strict_types=1);

namespace ReactWeb\Filesystem\Exception;

use Exception;
use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;

/**
 * FileNotFoundException
 *
 * @package ReactWeb\Filesystem\Exception
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
final class FileNotFoundException extends Exception
{

    public function __construct(string $filePath)
    {
        parent::__construct(sprintf('File %s not found', $filePath));
    }

}