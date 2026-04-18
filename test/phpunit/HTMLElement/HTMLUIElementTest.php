<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLDocument;

class HTMLUIElementTest extends HTMLElementTestCase {
	public function testWillValidateDisabled():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		$sut->disabled = true;
		self::assertFalse($sut->willValidate);
	}

	public function testWillValidateHidden():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		$sut->type = "hidden";
		self::assertFalse($sut->willValidate);
	}
}
