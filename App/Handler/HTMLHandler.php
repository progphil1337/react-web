<?php

declare(strict_types=1);

namespace App\Handler;

use ReactWeb\Handler\Handler;
use ReactWeb\HTML\Attribute\Style;
use ReactWeb\HTML\Element;
use ReactWeb\HTTP\Request;
use ReactWeb\HTTP\Response;
use ReactWeb\Routing\RouteAwareHandler;

/**
 * HTMLHandler
 *
 * @package App\Handler
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class HTMLHandler extends Handler implements RouteAwareHandler
{
    public function handle(Request $request, array $vars): Response
    {
        $html = new Element('html');

        $head = new Element('head');

        $title = (new Element('title'))->innerText('HTML Title Element');
        $head->add($title);

        $html->add($head);

        $body = new Element('body');
        $body->add(new Style([
            'background-color' => '#ecf0f1',
            'width' => '600px',
            'margin' => '0 auto'
        ]));

        $div = new Element('div');
        $div->add(new Style([
            'background-color' => 'white',
            'margin-top' => '50px',
            'border-radius' => '3px',
            'padding' => '13px'
        ]));

        $div->appendHTML('<h1>ReactWeb</h1><br />');
        $div->appendText('div-Element <b>Kein HTML</b>');

        $body->add($div);

        $html->add($body);

        return new Response\HTMLResponse(
            $html->toHTML()
        );
    }
}