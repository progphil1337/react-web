<?php

declare(strict_types=1);

namespace ReactWeb\HTTP;

use React\Http\Message\Response as HttpResponse;

/**
 * Response
 *
 * @package ReactWeb\HTTP
 * @author Philipp Lohmann <lohmann.philipp@gmx.net>
 */
abstract class Response
{

    /**
     * @param string $content
     * @param int $code
     * @param array $header
     */
    public function __construct(
        private readonly string $content,
        private readonly int    $code = 200,
        private array           $header = ['charset' => 'utf-8']
    )
    {
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function writeHeader(string $key, mixed $value): self
    {
        $this->header[$key] = $value;

        return $this;
    }

    /**
     * @return string|null
     */
    abstract protected function getContentType(): ?string;

    /**
     * @return HttpResponse
     */
    public function toHttpResponse(): HttpResponse
    {
        if ($this->getContentType() !== null) {
            $this->writeHeader('Content-Type', $this->getContentType());
        }

        return new HttpResponse($this->code, $this->header, $this->content);
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return array|string[]
     */
    public function getHeader(): array
    {
        return $this->header;
    }
}