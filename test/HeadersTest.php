<?php
/**
 * @see       https://github.com/zendframework/zend-mime for the canonical source repository
 * @copyright Copyright (c) 2005-2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-mime/blob/master/LICENSE.md New BSD License
 */

declare(strict_types = 1);

namespace ZendTest\Mime;

use PHPUnit\Framework\TestCase;
use Zend\Mime\Headers;
use Zend\Mime\Header\HeaderInterface;

/**
 *
 * @coversDefaultClass Zend\Mime\Headers
 * @covers ::<!public>
 */
class HeadersTest extends TestCase
{
    protected $header1;
    protected $header2;

    protected function setUp()
    {
        $this->header1 = $this->createMock(HeaderInterface::class);
        $this->header1->method('getFieldName')->willReturn('header1');
        $this->header2 = $this->createMock(HeaderInterface::class);
        $this->header2->method('getFieldName')->willReturn('header2');
        $this->header21 = $this->createMock(HeaderInterface::class);
        $this->header21->method('getFieldName')->willReturn('header2');
    }

    protected function tearDown()
    {
        $this->header1 = null;
        $this->header2 = null;
    }

    /**
     * @covers ::__construct
     * @covers ::getHeaders
     */
    public function testNoHeadersByDefault()
    {
        $headers = new Headers();
        self::assertEquals(0, count($headers));
        self::assertEmpty($headers->getHeaders());
    }

    /**
     * @covers ::__construct
     * @covers ::getHeaders
     */
    public function testNewHeadersAreSet()
    {
        $header1 = $this->header1;
        $header2 = $this->header2;

        $headers = new Headers([$header1, $header2]);
        self::assertEquals(2, count($headers));
        self::assertEquals(
            [$header1, $header2],
            $headers->getHeaders()
        );
    }

    /**
     * @covers ::getHeader
     */
    public function testGetHeader()
    {
        $header1 = $this->header1;
        $header2 = $this->header2;
        $header21 = $this->header21;

        $headers = new Headers([$header2, $header1, $header21]);
        self::assertEquals(
            [$header1],
            $headers->getHeader('header1')
        );
        self::assertEquals(
            [$header2, $header21],
            $headers->getHeader('header2')
        );
    }

    /**
     * @covers ::getHeader
     */
    public function testGetHeaderCaseInsensetivity()
    {
        $header1 = $this->header1;

        $headers = new Headers([$header1]);
        self::assertEquals(
            [$header1],
            $headers->getHeader('HeAdEr1')
        );
        self::assertEquals(
            [$header1],
            $headers->getHeader('Header1')
        );
        self::assertEquals(
            [$header1],
            $headers->getHeader('header1')
        );
    }

    /**
     * @covers ::getHeader
     */
    public function testGetHeaderNoHeader()
    {
        $header1 = $this->header1;

        $headers = new Headers([$header1]);
        self::assertEmpty(
            $headers->getHeader('header2')
        );
    }

    /**
     * @covers ::hasHeader
     */
    public function testHasHeader()
    {
        $header1 = $this->header1;
        $header2 = $this->header2;

        $headers = new Headers([$header1, $header2]);
        self::assertTrue($headers->hasHeader('header1'));
        self::assertTrue($headers->hasHeader('header2'));
    }

    /**
     * @covers ::hasHeader
     */
    public function testHasHeaderInstance()
    {
        $header2 = $this->header2;
        $header21 = $this->header21;

        $headers = new Headers([$header2]);
        self::assertTrue($headers->hasHeader($header2));
        self::assertFalse($headers->hasHeader($header21));
    }

    /**
     * @covers ::hasHeader
     */
    public function testHasHeaderCaseInsensetivity()
    {
        $header1 = $this->header1;

        $headers = new Headers([$header1]);
        self::assertTrue($headers->hasHeader('HeAdEr1'));
        self::assertTrue($headers->hasHeader('Header1'));
        self::assertTrue($headers->hasHeader('header1'));
    }

    /**
     * @covers ::hasHeader
     */
    public function testHasHeaderNoHeader()
    {
        $header1 = $this->header1;

        $headers = new Headers([$header1]);
        self::assertFalse($headers->hasHeader('header2'));
    }

    /**
     * @covers ::withHeaders
     */
    public function testWithHeadersSetsHeadersOnNewInstance()
    {
        $header1 = $this->header1;
        $header2 = $this->header2;

        $headers = new Headers();
        $newHeaders = $headers->withHeaders([$header1, $header2]);
        self::assertNotSame($headers, $newHeaders);
        self::assertCount(0, $headers);
        self::assertCount(2, $newHeaders);
        self::assertEquals(
            [$header1, $header2],
            $newHeaders->getHeaders()
        );
    }

    /**
     * @covers ::withHeaders
     */
    public function testWithHeadersCarriesOnExistingHeaders()
    {
        $header1 = $this->header1;
        $header2 = $this->header2;

        $headers = new Headers([$header1]);
        $newHeaders = $headers->withHeaders([$header2]);
        self::assertNotSame($headers, $newHeaders);
        self::assertCount(1, $headers);
        self::assertCount(2, $newHeaders);
        self::assertEquals(
            [$header1, $header2],
            $newHeaders->getHeaders()
        );
    }

    /**
     * @covers ::withHeaders
     */
    public function testWithHeadersReturnsSelfOnEmptyArray()
    {
        $header1 = $this->header1;

        $headers = new Headers([$header1]);
        $newHeaders = $headers->withHeaders([]);
        self::assertSame($headers, $newHeaders);
    }

    /**
     * @covers ::withHeaders
     */
    public function testWithHeadersReturnsSelfOnNoChanges()
    {
        $header1 = $this->header1;
        $header2 = $this->header2;

        $headers = new Headers([$header1, $header2]);
        $newHeaders = $headers->withHeaders([$header1, $header2]);
        self::assertSame($headers, $newHeaders);
    }

    /**
     * @covers ::withHeaders
     */
    public function testWithHeadersDeduplicatesSameHeaders()
    {
        $header1 = $this->header1;

        $headers = new Headers();
        $newHeaders = $headers->withHeaders([$header1, $header1]);
        self::assertCount(1, $newHeaders);
    }

    /**
     * @covers ::withHeaders
     */
    public function testWithHeadersMovesAlreadyExistingHeadersToEnd()
    {
        $header1 = $this->header1;
        $header2 = $this->header2;

        $headers = new Headers([$header2, $header1]);
        $newHeaders = $headers->withHeaders([$header2]);
        self::assertEquals(
            [$header2, $header1],
            $headers->getHeaders()
        );
        self::assertEquals(
            [$header1, $header2],
            $newHeaders->getHeaders()
        );
    }

    /**
     * @covers ::withPrependedHeaders
     */
    public function testWithPrependedHeadersSetsHeadersOnNewInstance()
    {
        $header1 = $this->header1;
        $header2 = $this->header2;

        $headers = new Headers();
        $newHeaders = $headers->withPrependedHeaders([$header1, $header2]);
        self::assertNotSame($headers, $newHeaders);
        self::assertCount(0, $headers);
        self::assertCount(2, $newHeaders);
        self::assertEquals(
            [$header1, $header2],
            $newHeaders->getHeaders()
        );
    }

    /**
     * @covers ::withPrependedHeaders
     */
    public function testWithPrependedHeadersCarriesOnExistingHeaders()
    {
        $header1 = $this->header1;
        $header2 = $this->header2;

        $headers = new Headers([$header1]);
        $newHeaders = $headers->withPrependedHeaders([$header2]);
        self::assertNotSame($headers, $newHeaders);
        self::assertCount(1, $headers);
        self::assertCount(2, $newHeaders);
        self::assertEquals(
            [$header2, $header1],
            $newHeaders->getHeaders()
        );
    }

    /**
     * @covers ::withPrependedHeaders
     */
    public function testWithPrependedHeadersReturnsSelfOnEmptyArray()
    {
        $header1 = $this->header1;

        $headers = new Headers([$header1]);
        $newHeaders = $headers->withPrependedHeaders([]);
        self::assertSame($headers, $newHeaders);
    }

    /**
     * @covers ::withPrependedHeaders
     */
    public function testWithPrependedHeadersReturnsSelfOnNoChanges()
    {
        $header1 = $this->header1;
        $header2 = $this->header2;

        $headers = new Headers([$header1, $header2]);
        $newHeaders = $headers->withPrependedHeaders([$header1, $header2]);
        self::assertSame($headers, $newHeaders);
    }

    /**
     * @covers ::withPrependedHeaders
     */
    public function testWithPrependedHeadersDeduplicatesSameHeaders()
    {
        $header1 = $this->createMock(HeaderInterface::class);
        $header1->method('getFieldName')->willReturn('header1');

        $headers = new Headers();
        $newHeaders = $headers->withPrependedHeaders([$header1, $header1]);
        self::assertCount(1, $newHeaders);
    }

    /**
     * @covers ::withPrependedHeaders
     */
    public function testWithPrependedHeadersMovesExistingHeaderToTheFrontAndKeepsPrependOrder()
    {
        $header1 = $this->header1;
        $header2 = $this->header2;
        $header21 = $this->header21;

        $headers = new Headers([$header1, $header2]);
        $newHeaders = $headers->withPrependedHeaders([$header2, $header21]);
        self::assertEquals(
            [$header1, $header2],
            $headers->getHeaders()
        );
        self::assertEquals(
            [$header2, $header21, $header1],
            $newHeaders->getHeaders()
        );
    }

    /**
     * @covers ::withoutHeaders
     */
    public function testWithoutHeadersRemovesHeadersOnNewInstance()
    {
        $header1 = $this->header1;

        $headers = new Headers([$header1]);
        $newHeaders = $headers->withoutHeaders([$header1]);
        self::assertNotSame($headers, $newHeaders);
        self::assertCount(1, $headers);
        self::assertCount(0, $newHeaders);
    }

    /**
     * @covers ::withoutHeaders
     */
    public function testWithoutHeadersCarriesOnExistingHeaders()
    {
        $header1 = $this->header1;
        $header2 = $this->header2;

        $headers = new Headers([$header1, $header2]);
        $newHeaders = $headers->withoutHeaders([$header2]);
        self::assertNotSame($headers, $newHeaders);
        self::assertCount(1, $newHeaders);
        self::assertEquals(
            [$header1],
            $newHeaders->getHeaders()
        );
    }

    /**
     * @covers ::withoutHeaders
     */
    public function testWithoutHeadersReturnsSelfOnEmptyArray()
    {
        $header1 = $this->header1;

        $headers = new Headers([$header1]);
        $newHeaders = $headers->withoutHeaders([]);
        self::assertSame($headers, $newHeaders);
    }


    /**
     * @covers ::withoutHeaders
     */
    public function testWithoutHeadersReturnsSelfOnNoChanges()
    {
        $header1 = $this->header1;
        $header2 = $this->header2;
        $header21 = $this->header21;

        $headers = new Headers([$header1, $header2]);
        $newHeaders = $headers->withoutHeaders([$header21]);
        self::assertSame($headers, $newHeaders);
    }

    /**
     * @covers ::count
     */
    public function testCountable()
    {
        $header1 = $this->header1;
        $header2 = $this->header2;

        $headers = new Headers([$header1, $header2]);
        self::assertCount(2, $headers);
    }

    /**
     * @covers ::getIterator
     */
    public function testIterator()
    {
        $header1 = $this->header1;
        $header2 = $this->header2;

        $headers = new Headers([$header1, $header2]);
        self::assertEquals(
            [$header1, $header2],
            iterator_to_array($headers)
        );
    }
}
