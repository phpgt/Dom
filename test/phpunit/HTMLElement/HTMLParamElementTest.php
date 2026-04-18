<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLDocument;

class HTMLParamElementTest extends HTMLElementTestCase {
	public function testName():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("param");
		self::assertPropertyAttributeCorrelate($sut, "name");
	}

	public function testValue():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("param");
		self::assertPropertyAttributeCorrelate($sut, "value");
	}
}
