<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLDocument;

class HTMLTableColElementTest extends HTMLElementTestCase {
	public function testSpan():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("col");
		self::assertPropertyAttributeCorrelateNumber($sut, "int:1", "span");
	}
}
