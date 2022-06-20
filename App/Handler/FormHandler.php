<?php

declare(strict_types=1);

namespace App\Handler;

use App\Form\TestForm;
use ReactWeb\Handler\Handler;
use ReactWeb\HTTP\Enum\Method;
use ReactWeb\HTTP\Request;
use ReactWeb\HTTP\Response;
use ReactWeb\Routing\RouteAwareHandler;

/**
 * FormHandler
 *
 * @package App\Handler
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class FormHandler extends Handler implements RouteAwareHandler
{

    public function handle(Request $request, array $vars): Response
    {
        $form = new TestForm();

        if ($request->method === Method::POST) {
            $result = $form->validate($request->body);
            $valid = $result->isValid();

            if (!$valid) {
                return new Response\TextResponse(print_r($result->getErrorMessages(), true));
            }
        }

        return new Response\HTMLResponse($form->toHTML());
    }
}