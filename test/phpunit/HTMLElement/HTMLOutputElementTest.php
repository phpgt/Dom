<?php
namespace GT\Dom\Test\HTMLElement;


use GT\Dom\HTMLDocument;

class HTMLOutputElementTest extends HTMLElementTestCase {
	public function testDefaultValue():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("output");
		self::assertPropertyAttributeCorrelate($sut, "value", "defaultValue");
	}

	public function testHtmlFor():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("output");
		self::assertCount(0, $sut->htmlFor);
		$sut->htmlFor->add("one", "two", "three");
		self::assertEquals("one two three", $sut->getAttribute("for"));
	}
}
