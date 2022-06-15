<?php

declare(strict_types=1);

namespace ReactWeb\HTML\Attribute;

/**
 * ClassList
 *
 * @package ReactWeb\HTML\Attribute
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class ClassList extends ArrayValueAttribute
{
    public function __construct(array $classes)
    {
        parent::__construct('class', $classes, ' ', false, false);
    }
}