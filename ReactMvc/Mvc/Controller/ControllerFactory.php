<?php

namespace ReactMvc\Mvc\Controller;

use ReactMvc\Logger\Logger;
use ReactMvc\Mvc\AbstractFactory;
use Twig\Environment as TwigEnvironment;

/**
 * ControllerFactory
 *
 * @package ReactMvc\Mvc\Controller
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
final class ControllerFactory extends AbstractFactory
{

    /**
     * @param TwigEnvironment $twig
     */
    public function __construct(private TwigEnvironment $twig)
    {
    }

    /**
     * @param AbstractController $controller
     * @return void
     */
    public function inject(AbstractController $controller): void
    {
        if (!$controller->isCreated()) {
            Logger::log($this, sprintf('Create controller %s', get_class($controller)));
            $controller->create(
                twig: $this->twig
            );
        }
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            AbstractController::class
        ];
    }

}