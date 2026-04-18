<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\Exception\IndexSizeException;
use GT\Dom\HTMLDocument;

class HTMLTableSectionElementTest extends HTMLElementTestCase {
	public function testDeleteRowNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tbody");
		self::expectException(IndexSizeException::class);
		$sut->deleteRow(0);
	}

	public function testDeleteRow():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tbody");
		$sut->appendChild($sut->ownerDocument->createElement("tr"));
		self::assertCount(1, $sut->rows);
		$sut->deleteRow(0);
		self::assertCount(0, $sut->rows);
	}

	public function testInsertRow():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tbody");
		$sut->insertRow();
		self::assertCount(1, $sut->rows);
	}

	public function testInsertRowIndex():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tbody");
		$sut->insertRow();
		$sut->insertRow();
		$inBetween = $sut->insertRow(1);
		self::assertCount(3, $sut->rows);
		self::assertSame($inBetween, $sut->rows[1]);
	}

	public function testInsertRowOutOfBounds():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tbody");
		self::expectException(IndexSizeException::class);
		$sut->insertRow(-1);
	}

	public function testRows():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("tbody");
		$rows = $sut->rows;

		self::assertCount(0, $rows);
		$sut->insertRow();
		self::assertCount(1, $rows);
		$sut->deleteRow(0);
		self::assertCount(0, $rows);
	}
}
