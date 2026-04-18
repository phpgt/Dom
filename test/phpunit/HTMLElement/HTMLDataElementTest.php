<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLDocument;

class HTMLDataElementTest extends HTMLElementTestCase {
	public function testValueNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("data");
		self::assertSame("", $sut->value);
	}

	public function testValue():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("data");
		$sut->value = "test value";
		self::assertEquals("test value", $sut->value);
		self::assertEquals("test value", $sut->getAttribute("value"));
	}
}
