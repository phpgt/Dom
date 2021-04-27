<?php
namespace Gt\Dom\Test\HTMLElement;

use Gt\Dom\Exception\IndexIsNegativeOrGreaterThanAllowedAmountException;
use Gt\Dom\HTMLElement\HTMLTableSectionElement;
use Gt\Dom\Test\TestFactory\NodeTestFactory;

class HTMLTableSectionElementTest extends HTMLElementTestCase {
	public function testDeleteRowNone():void {
		/** @var HTMLTableSectionElement $sut */
		$sut = NodeTestFactory::createHTMLElement("tbody");
		self::expectException(IndexIsNegativeOrGreaterThanAllowedAmountException::class);
		$sut->deleteRow(0);
	}

	public function testDeleteRow():void {
		/** @var HTMLTableSectionElement $sut */
		$sut = NodeTestFactory::createHTMLElement("tbody");
		$sut->appendChild($sut->ownerDocument->createElement("tr"));
		self::assertCount(1, $sut->rows);
		$sut->deleteRow(0);
		self::assertCount(0, $sut->rows);
	}
}