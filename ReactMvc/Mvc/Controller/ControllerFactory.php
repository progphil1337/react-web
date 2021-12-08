<?php

namespace ReactMvc\Mvc\Controller;

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

    public function __construct(private TwigEnvironment $twig)
    {
    }

    public function inject(AbstractController $controller): void
    {
        if (!$controller->isCreated()) {
            $controller->create(
                twig: $this->twig
            );
        }
    }

    public function getDependencies(): array
    {
        return [
            AbstractController::class
        ];
    }

}