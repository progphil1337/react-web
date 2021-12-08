<?php

namespace ReactMvc\Mvc;

use ReactMvc\Mvc\Controller\AbstractController;

/**
 * AbstractFactory
 *
 * @package ReactMvc\Mvc
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
abstract class AbstractFactory
{
    abstract public function inject(AbstractController $controller): void;

    public function getDependencies(): array
    {
        return [];
    }
}