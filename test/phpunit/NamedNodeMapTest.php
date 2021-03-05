<?php
namespace Gt\Dom\Test;

use Gt\Dom\Attr;
use Gt\Dom\Document;
use Gt\Dom\Element;
use Gt\Dom\Facade\DOMDocumentFacade;
use Gt\Dom\Facade\NamedNodeMapFactory;
use PHPUnit\Framework\TestCase;

class NamedNodeMapTest extends TestCase {
	public function testLength():void {
		$document = new DOMDocumentFacade(new Document());
		$nativeElement = $document->createElement("example");
		$nativeElement->setAttribute("one", "abc");
		$nativeElement->setAttribute("two", "xyz");
		/** @var Element $element */
		$element = $document->getGtDomNode($nativeElement);
		$sut = NamedNodeMapFactory::create(fn() => $nativeElement->attributes, $element);
		self::assertEquals(2, $sut->length);
		self::assertCount(2, $sut);
	}

	public function testGetNamedItem():void {
		$document = new DOMDocumentFacade(new Document());
		$nativeElement = $document->createElement("example");
		$nativeElement->setAttribute("one", "abc");
		$nativeElement->setAttribute("two", "xyz");
		/** @var Element $element */
		$element = $document->getGtDomNode($nativeElement);
		$sut = NamedNodeMapFactory::create(fn() => $nativeElement->attributes, $element);
		$item = $sut->getNamedItem("two");
		self::assertInstanceOf(Attr::class, $item);
		self::assertEquals("xyz", $item->value);
	}

	public function testGetNamedItemNone():void {
		$document = new DOMDocumentFacade(new Document());
		$nativeElement = $document->createElement("example");
		/** @var Element $element */
		$element = $document->getGtDomNode($nativeElement);
		$sut = NamedNodeMapFactory::create(fn() => $nativeElement->attributes, $element);
		$item = $sut->getNamedItem("two");
		self::assertNull($item);
	}

	public function testGetNamedItemNS():void {
		$ns = "example_namespace";

		$document = new Document();
		/** @var Element $element */
		$element = $document->createElementNS(
			$ns,
			"test:example"
		);
		$element->setAttributeNS(
			$ns,
			"test",
			"abc"
		);
		$sut = $element->attributes->getNamedItemNS($ns, "test");
		self::assertEquals(
			"abc",
			$sut->value
		);
	}

	public function testGetNamedItemNSNone():void {
		$ns = "example_namespace";

		$document = new Document();
		/** @var Element $element */
		$element = $document->createElementNS(
			$ns,
			"test:example"
		);
		$element->setAttributeNS(
			$ns,
			"test",
			"abc"
		);
		$sut = $element->attributes->getNamedItemNS($ns, "not-here");
		self::assertNull(
			$sut
		);
	}
}