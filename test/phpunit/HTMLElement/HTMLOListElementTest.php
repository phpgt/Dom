<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLDocument;

class HTMLOListElementTest extends HTMLElementTestCase {
	public function testReversed():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("ol");
		self::assertPropertyAttributeCorrelateBool($sut, "reversed");
	}

	public function testStart():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("ol");
		self::assertSame(1, $sut->start);
		self::assertPropertyAttributeCorrelateNumber($sut, "int:1",  "start");
	}

	public function testType():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("ol");
		self::assertPropertyAttributeCorrelate($sut, "type");
	}
}
