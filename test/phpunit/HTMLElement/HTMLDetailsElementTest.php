<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLDocument;

class HTMLDetailsElementTest extends HTMLElementTestCase {
	public function testOpenDefault():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("details");
		self::assertFalse($sut->open);
	}

	public function testOpen():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("details");
		$sut->open = true;
		self::assertTrue($sut->open);
		self::assertTrue($sut->hasAttribute("open"));

		$sut->open = false;
		self::assertFalse($sut->open);
		self::assertFalse($sut->hasAttribute("open"));
	}
}
