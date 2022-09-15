<?php

declare(strict_types=1);

namespace App\Handler;

use App\Form\TestForm;
use ReactWeb\Handler\Handler;
use ReactWeb\HTTP\Request;
use ReactWeb\HTTP\Response;
use ReactWeb\Logger\Logger;
use ReactWeb\Routing\RouteAwareHandler;

/**
 * FormHandler
 *
 * @package App\Handler
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
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

        if ($request->method->value === $form->method->value) {
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

        return $this->render('form', [
            'form' => $form,
            'errorMessages' => $errorMessages
        ]);
    }
}