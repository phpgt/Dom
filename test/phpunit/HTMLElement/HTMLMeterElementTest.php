<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLDocument;

class HTMLMeterElementTest extends HTMLElementTestCase {
	public function testHigh():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("meter");
		self::assertPropertyAttributeCorrelateNumber($sut, "?float", "high");
	}

	public function testLow():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("meter");
		self::assertPropertyAttributeCorrelateNumber($sut, "?float", "low");
	}

	public function testMax():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("meter");
		self::assertPropertyAttributeCorrelateNumber($sut, "?float", "max");
	}

	public function testMin():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("meter");
		self::assertPropertyAttributeCorrelateNumber($sut, "?float", "min");
	}

	public function testOptimum():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("meter");
		self::assertPropertyAttributeCorrelateNumber($sut, "?float", "optimum");
	}

	public function testValue():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("meter");
		self::assertPropertyAttributeCorrelateNumber($sut, "?float", "value");
	}

	public function testLabelsNested():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("meter");
		$label = $sut->ownerDocument->createElement("label");
		$label->appendChild($sut);
		self::assertSame($label, $sut->labels[0]);
	}

	public function testLabelsFor():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("meter");
		$sut->id = "test-input";
		$label = $sut->ownerDocument->createElement("label");
		$label->htmlFor = "test-input";

		$sut->ownerDocument->body->append($sut);
		$sut->ownerDocument->body->append($label);

		self::assertSame($label, $sut->labels[0]);
	}

	public function testLabelsMixedNestedFor():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("meter");
		$sut->id = "test-input";
		$label1 = $sut->ownerDocument->createElement("label");
		$label1->htmlFor = "test-input";
		$label2 = $sut->ownerDocument->createElement("label");
		$label2->htmlFor = "test-input";
		$labelParent = $sut->ownerDocument->createElement("label");

		$sut->ownerDocument->body->append($labelParent);
		$labelParent->appendChild($sut);
		$sut->ownerDocument->body->append($label1);
		$sut->ownerDocument->body->append($label2);

		$labelsArray = iterator_to_array($sut->labels->entries());
		self::assertCount(3, $labelsArray);
		foreach([$label1, $label2, $labelParent] as $l) {
			self::assertContains($l, $labelsArray);
		}
	}
}
