<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLCollection;
use GT\Dom\HTMLDocument;

class HTMLFieldSetElementTest extends HTMLElementTestCase {
	public function testDisabled():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("fieldset");
		self::assertPropertyAttributeCorrelateBool($sut, "disabled");
	}

	public function testElementsEmpty():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("fieldset");
		$elements = $sut->elements;
		self::assertInstanceOf(HTMLCollection::class, $elements);
		self::assertCount(0, $elements);
	}

	public function testElements():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("fieldset");
		$elements = $sut->elements;
		$button = $sut->ownerDocument->createElement("button");
		$input = $sut->ownerDocument->createElement("input");
		$select = $sut->ownerDocument->createElement("select");
		$textArea = $sut->ownerDocument->createElement("textarea");
		$custom = $sut->ownerDocument->createElement("custom-element");
		$custom->setAttribute("name", "example");

		$div = $sut->ownerDocument->createElement("div");
		$sut->append($button, $input, $div);
		$div->append($select, $textArea, $custom);

		self::assertCount(5, $elements);
		foreach($elements as $element) {
			self::assertContains($element, [$button, $input, $select, $textArea, $custom]);
		}
	}

	public function testType():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("fieldset");
		self::assertEquals("fieldset", $sut->type);
	}
}
