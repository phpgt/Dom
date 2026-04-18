<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLDocument;

class HTMLEmbedElementTest extends HTMLElementTestCase {
	public function testHeight():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("embed");
		self::assertPropertyAttributeCorrelateNumber($sut, "int", "height");
	}

	public function testSrc():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("embed");
		self::assertPropertyAttributeCorrelate($sut, "src");
	}

	public function testType():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("embed");
		self::assertPropertyAttributeCorrelateNumber($sut, "int", "type");
	}

	public function testWidth():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("embed");
		self::assertPropertyAttributeCorrelateNumber($sut, "int", "width");
	}
}
