<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLDocument;

class HTMLMapElementTest extends HTMLElementTestCase {
	public function testName():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("map");
		self::assertPropertyAttributeCorrelate($sut, "name");
	}

	public function testAreasNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("map");
		self::assertCount(0, $sut->areas);
	}

	public function testAreas():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("map");
		$sut->appendChild($sut->ownerDocument->createElement("area"));
		$sut->appendChild($sut->ownerDocument->createElement("area"));
		$sut->appendChild($sut->ownerDocument->createElement("area"));
		self::assertCount(3, $sut->areas);
	}
}
