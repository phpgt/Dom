<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLDocument;

class HTMLOptGroupElementTest extends HTMLElementTestCase {
	public function testDisabled():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("optgroup");
		self::assertPropertyAttributeCorrelateBool($sut, "disabled");
	}

	public function testLabel():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("optgroup");
		self::assertPropertyAttributeCorrelate($sut, "label");
	}
}
