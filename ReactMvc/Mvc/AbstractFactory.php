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
    /**
     * @param AbstractController $controller
     * @return void
     */
    abstract public function inject(AbstractController $controller): void;

    /**
     * @return array
     */
    abstract public function getDependencies(): array;
}