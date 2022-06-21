<?php

declare(strict_types=1);

namespace App\Form;

use ReactWeb\Form\AbstractInput;
use ReactWeb\Form\Element\Input;
use ReactWeb\Form\Element\Radio;
use ReactWeb\Form\Enum\InputType;
use ReactWeb\Form\Form;
use ReactWeb\Form\Validation\Validator\MaxLength;
use ReactWeb\Form\Validation\Validator\MinLength;
use ReactWeb\HTTP\Enum\Method;

/**
 * TestForm
 *
 * @package App\Form
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class TestForm extends Form
{

    public function __construct()
    {
        parent::__construct('test', Method::POST);
    }

    protected function build(): void
    {
        $username = new Input('username', InputType::TEXT, 'Username');
        $username->addValidator(new MaxLength(1));
        $username->addValidator(new MinLength(1));
        $this->add($username);

        $mail = new Input('mail', InputType::EMAIL, 'E-Mail');
        $this->add($mail);

        $radio = new Radio('language', [
            'php' => 'PHP',
            'csharp' => 'C-Sharp'
        ]);

        $radio->setValue('php');

        $this->add($radio);



        $this->submitButton('Speichern');
    }
}