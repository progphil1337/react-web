<?php

declare(strict_types=1);

namespace App\Form;


use ProgPhil1337\PhpForms\Element\Input;
use ProgPhil1337\PhpForms\Element\Radio;
use ProgPhil1337\PhpForms\Element\Select;
use ProgPhil1337\PhpForms\Enum\InputType;
use ProgPhil1337\PhpForms\Enum\RequestMethod;
use ProgPhil1337\PhpForms\Form;
use ProgPhil1337\PhpForms\Validation\Validator\IsRequired;
use ProgPhil1337\PhpForms\Validation\Validator\MaxLength;
use ProgPhil1337\PhpForms\Validation\Validator\MinLength;

/**
 * TestForm
 *
 * @package App\Form
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
class TestForm extends Form
{

    public function __construct()
    {
        parent::__construct('test', RequestMethod::POST);
    }

    protected function build(): void
    {
        $username = new Input('username', InputType::TEXT, 'Username');
        $username->addValidator(new MaxLength(1));
        $username->addValidator(new MinLength(1));
        $username->addValidator(new IsRequired(true));
        $this->add($username);

        $mail = new Input('mail', InputType::EMAIL, 'E-Mail');
        $this->add($mail);

        $radio = new Radio('language', [
            'php' => 'PHP',
            'csharp' => 'C-Sharp'
        ], 'Sprache');
        $radio->setValue('php');
        $this->add($radio);

        $select = new Select('car', [
            'volvo' => 'Volvo',
            'vw' => 'VW',
            'bmw' => 'BMW',
            'audi' => 'Audi'
        ], 'Auto');


        $select->setValue('audi');
        $this->add($select);

        $this->submitButton('Speichern');
    }
}