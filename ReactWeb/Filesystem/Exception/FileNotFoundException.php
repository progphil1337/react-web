<?php

declare(strict_types=1);

namespace ReactWeb\Filesystem\Exception;

use Exception;
use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;

/**
 * FileNotFoundException
 *
 * @package ReactWeb\Filesystem\Exception
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class FileNotFoundException extends Exception
{

    public function __construct(string $filePath)
    {
        parent::__construct(sprintf('File %s not found', $filePath));
    }

}