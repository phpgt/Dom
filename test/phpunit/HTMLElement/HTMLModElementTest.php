<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\ElementType;
use GT\Dom\HTMLDocument;

class HTMLModElementTest extends HTMLElementTestCase {
	public function testCorrectTypes():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("del");
		self::assertSame(ElementType::HTMLModElement, $sut->elementType);
		$sut = $document->createElement("ins");
		self::assertSame(ElementType::HTMLModElement, $sut->elementType);
	}

	public function testCite():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("del");
		self::assertPropertyAttributeCorrelate($sut, "cite");
	}

	public function testDateTime():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("del");
		self::assertPropertyAttributeCorrelate($sut, "datetime", "dateTime");
	}
}
