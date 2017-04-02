<?php
/**
 * @see       https://github.com/zendframework/zend-mime for the canonical source repository
 * @copyright Copyright (c) 2005-2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-mime/blob/master/LICENSE.md New BSD License
 */

declare(strict_types = 1);

namespace Zend\Mime;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use OutOfRangeException;

/**
 * Mime entity can have repeating groups of headers and their order is
 * important.
 *
 */
class Headers implements Countable, IteratorAggregate
{
    protected $headers = [];
    protected $nameMap = [];

    public function __construct(array $headers = [])
    {
        foreach ($headers as $header) {
            $this->addHeader($header);
        }
    }

    public function getHeader(string $name) : array
    {
        $name = $this->normalizeFieldName($name);
        $keys = array_keys($this->nameMap, $name, true);
        return array_values(array_intersect_key(
            $this->headers,
            array_flip($keys)
        ));
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }

    public function hasHeader($fieldNameOrHeaderInstance) : bool
    {
        if ($fieldNameOrHeaderInstance instanceof Header\HeaderInterface) {
            return in_array($fieldNameOrHeaderInstance, $this->headers, true);
        }
        $name = $this->normalizeFieldName($fieldNameOrHeaderInstance);
        return in_array($name, $this->nameMap);
    }

    public function withPrependedHeaders(array $headers) : self
    {
        if (empty($headers)) {
            return $this;
        }
        $headers = array_reverse($headers);
        $new = clone $this;
        foreach ($headers as $header) {
            $new->addHeader($header, true);
        }
        if ($this->headers === $new->headers) {
            // no change was made
            return $this;
        }
        return $new;
    }

    public function withHeaders(array $headers) : self
    {
        if (empty($headers)) {
            return $this;
        }
        $new = clone $this;
        foreach ($headers as $header) {
            $new->addHeader($header);
        }
        if ($this->headers === $new->headers) {
            // no change was made
            return $this;
        }
        return $new;
    }

    /**
     * Returns new instance without headers
     *
     * @param Header\HeaderInterface[] $headers
     * @return self
     */
    public function withoutHeaders(array $headers) : self
    {
        if (empty($headers)) {
            return $this;
        }
        $new = clone $this;
        foreach ($headers as $header) {
            $new->removeHeader($header);
        }
        if ($this->headers === $new->headers) {
            // no change was made
            return $this;
        }
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function count() : int
    {
        return count($this->headers);
    }

    /**
     * @inheritDoc
     */
    public function getIterator() : ArrayIterator
    {
        return new ArrayIterator($this->headers);
    }

    /**
     * Internal helper function that adds header to the end of the list
     * If header is already set, it will be moved to the end
     *
     * @param Header\HeaderInterface $header
     * @param boolean $prepend Add to the front instead
     */
    protected function addHeader(Header\HeaderInterface $header, $prepend = false) : void
    {
        $this->removeHeader($header);
        if ($prepend) {
            array_unshift($this->headers, $header);
            array_unshift($this->nameMap, $this->normalizeFieldName($header->getFieldName()));
            return;
        }
        $this->headers[] = $header;
        $this->nameMap[] = $this->normalizeFieldName($header->getFieldName());
    }

    /**
     * Internal helper function that removes header from the list
     */
    protected function removeHeader(Header\HeaderInterface $header) : void
    {
        $key = array_search($header, $this->headers, true);
        if ($key === false) {
            return;
        }
        unset($this->headers[$key]);
        unset($this->nameMap[$key]);
        $this->headers = array_values($this->headers);
        $this->nameMap = array_values($this->nameMap);
    }

    private function normalizeFieldName(string $name) : string
    {
        return strtolower($name);
    }
}
