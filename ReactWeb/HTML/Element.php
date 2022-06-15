<?php

declare(strict_types=1);

namespace ReactWeb\HTML;

use ReactWeb\HTML\Element\Value;
use RuntimeException;

/**
 * Element
 *
 * @package ReactWeb\HTML
 * @author Philipp Lohmann <philipp.lohmann@check24.de>
 * @copyright CHECK24 GmbH
 */
class Element
{
    /** @var array<Attribute> */
    private array $attributes = [];

    /** @var array<Element> */
    private array $elements = [];

    /**
     * @param string $name
     * @param bool $voidElement
     */
    public function __construct(private readonly string $name, private readonly bool $voidElement = false)
    {

    }

    /**
     * @param \ReactWeb\HTML\Attribute $attribute
     * @return $this
     */
    public function addAttribute(Attribute $attribute): self
    {
        $this->attributes[] = $attribute;

        return $this;
    }

    /**
     * @param \ReactWeb\HTML\Attribute $attribute
     * @return $this
     */
    public function removeAttribute(Attribute $attribute): self
    {
        for ($i = 0; $i < count($this->attributes); $i++) {
            if ($this->attributes[$i] === $attribute) {
                unset($this->attributes[$i]);
            }
        }

        return $this;
    }

    /**
     * @param \ReactWeb\HTML\Element $e
     * @return $this
     */
    public function addElement(Element $e): self
    {
        if ($this->voidElement) {
            throw new RuntimeException('Cannot add elements to void elements');
        }

        $this->elements[] = $e;

        return $this;
    }

    /**
     * @param \ReactWeb\HTML\Element $removedElement
     * @return $this
     */
    public function removeElement(Element $removedElement): self
    {
        for ($i = 0; $i < count($this->elements); $i++) {
            if ($this->elements[$i] === $removedElement) {
                unset($this->elements[$i]);
            }
        }

        return $this;
    }

    /**
     * @param \ReactWeb\HTML\Element|\ReactWeb\HTML\Attribute $o
     * @return $this
     */
    public function add(Element|Attribute $o): self
    {
        return ($o instanceof Element) ? $this->addElement($o) : $this->addAttribute($o);
    }

    /**
     * @param \ReactWeb\HTML\Element|\ReactWeb\HTML\Attribute $o
     * @return $this
     */
    public function remove(Element|Attribute $o): self
    {
        return ($o instanceof Element) ? $this->removeElement($o) : $this->removeAttribute($o);
    }

    /**
     * @param string $text
     * @return $this
     */
    public function appendText(string $text): self
    {
        if ($this->voidElement) {
            throw new RuntimeException('Cannot append text to void elements');
        }

        return $this->appendValue($text, true);
    }

    /**
     * @param string $html
     * @return $this
     */
    public function appendHTML(string $html): self
    {
        if ($this->voidElement) {
            throw new RuntimeException('Cannot append html to void elements');
        }

        return $this->appendValue($html, false);
    }

    /**
     * @param string $text
     * @return $this
     */
    public function innerText(string $text): self
    {
        if ($this->voidElement) {
            throw new RuntimeException('Cannot set inner text to void elements');
        }

        return $this->innerValue($text, true);
    }

    /**
     * @param string $html
     * @return $this
     */
    public function innerHTML(string $html): self
    {
        if ($this->voidElement) {
            throw new RuntimeException('Cannot set inner html to void elements');
        }

        return $this->innerValue($html, false);
    }

    /**
     * @return string
     */
    public function toHTML(): string
    {
        $attributes = count($this->attributes) > 0 ? ' ' . implode(' ', array_map(fn(Attribute $a) => $a->toHTML(), $this->attributes)) : '';

        if ($this->voidElement) {
            return <<<HTML
<{$this->name}{$attributes}/>
HTML;
        }

        $innerHTML = count($this->elements) > 0 ? implode(PHP_EOL, array_map(fn(Element $e) => $e->toHTML(), $this->elements)) : '';

        return <<<HTML
<{$this->name}{$attributes}>
{$innerHTML}
</{$this->name}>
HTML;
    }

    public function __toString(): string
    {
        return $this->toHTML();
    }

    /**
     * @param string $value
     * @param bool $htmlspecialchars
     * @return $this
     */
    private function appendValue(string $value, bool $htmlspecialchars): self
    {
        $this->add(new Value($value, $htmlspecialchars));

        return $this;
    }

    /**
     * @param string $value
     * @param bool $htmlspecialchars
     * @return $this
     */
    private function innerValue(string $value, bool $htmlspecialchars): self
    {
        // clear existing value elements
        foreach ($this->elements as &$element) {
            if ($element instanceof Value) {
                unset($element);
            }
        }

        $this->add(new Value($value, $htmlspecialchars));

        return $this;
    }
}