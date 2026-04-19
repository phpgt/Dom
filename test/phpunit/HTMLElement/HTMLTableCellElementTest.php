<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\ElementType;
use GT\Dom\HTMLDocument;

class HTMLTableCellElementTest extends HTMLElementTestCase {
	public function testTd():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("td");
		self::assertSame(ElementType::HTMLTableCellElement, $sut->elementType);
	}

	public function testTh():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("th");
		self::assertSame(ElementType::HTMLTableCellElement, $sut->elementType);
	}

	public function testAbbr():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("th");
		self::assertPropertyAttributeCorrelate($sut, "abbr");
	}

	public function testGetCellIndexNoTr():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("th");
		self::assertSame(-1, $sut->cellIndex);
	}

	public function testGetCellIndexFirst():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("th");
		$tr = $sut->ownerDocument->createElement("tr");
		$tr->appendChild($sut);
		self::assertSame(0, $sut->cellIndex);
	}

	public function testGetCellIndex():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("td");
		$tr = $sut->ownerDocument->createElement("tr");

		for($i = 0; $i < 10; $i++) {
			$tr->appendChild($sut->ownerDocument->createElement("td"));
		}

		$tr->appendChild($sut);

		for($i = 0; $i < 10; $i++) {
			$tr->appendChild($sut->ownerDocument->createElement("td"));
		}

		self::assertSame(10, $sut->cellIndex);
	}

	public function testColSpan():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("td");
		self::assertPropertyAttributeCorrelateNumber($sut, "?int:1", "colspan", "colSpan");
	}

	public function testHeaders():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("td");
		self::assertPropertyAttributeCorrelate($sut, "headers");
	}

	public function testRowSpan():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("td");
		self::assertPropertyAttributeCorrelateNumber($sut, "?int:1", "rowspan", "rowSpan");
	}

	public function testScope():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("td");
		self::assertPropertyAttributeCorrelate($sut, "scope");
	}
}
