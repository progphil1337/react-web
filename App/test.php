<?php

declare(strict_types=1);

require_once 'autoload.php';

const APP_PATH = PROJECT_PATH . 'App' . DIRECTORY_SEPARATOR;

$html = new \ReactWeb\HTML\Element\HTML();

$head = new \ReactWeb\HTML\Element('head');
$title = new \ReactWeb\HTML\Element('title');
$title->innerText('Title');

$head->add($title);
$html->add($head);

$body = new \ReactWeb\HTML\Element('body');

$html->add($body);

$html->add(new \ReactWeb\HTML\Attribute\Style([
    'background-color' => 'red',
    'font-color' => 'blue'
]));

$html->add(new \ReactWeb\HTML\Attribute\ClassList([
    'body',
    'website'
]));


$html->innerText('Hallo inner Text');
$html->appendText('Test');

echo $html;


while (true) ;