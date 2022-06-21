<?php

declare(strict_types=1);

namespace App\Handler;

use App\Form\TestForm;
use ReactWeb\Handler\Handler;
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

        $form->setDefaultValues([
            'mail' => 'test'
        ]);

        $errorMessages = [];

        if ($request->method === $form->method) {
            $result = $form->validate($request->body);
            $valid = $result->isValid();

            if (!$valid) {
                foreach ($result->getErrorMessages() as $inputName => $validators) {
                    $errorMessages[$inputName] = [];
                    foreach ($validators as $info) {
                        $errorMessages[$inputName][] = $info['message'];
                    }
                }
            }
        }

        $form->prepare();

        return $this->render('form', [
            'form' => $form,
            'errorMessages' => $errorMessages
        ]);
    }
}