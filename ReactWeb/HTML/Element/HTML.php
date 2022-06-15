<?php

declare(strict_types=1);

namespace ReactWeb\HTML\Element;

use ReactWeb\HTML\Element;

/**
 * HTML
 *
 * @package ReactWeb\HTML\Element
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class HTML extends Element
{
    public function __construct()
    {
        parent::__construct('html');
    }
}