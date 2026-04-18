<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\Exception\ClientSideOnlyFunctionalityException;
use GT\Dom\HTMLDocument;

class HTMLButtonElementTest extends HTMLElementTestCase {
	public function testAutofocus():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("button");
		self::assertPropertyAttributeCorrelateBool($sut, "autofocus");
	}

	public function testDisabled():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("button");
		self::assertPropertyAttributeCorrelateBool($sut, "disabled");
	}

	public function testFormNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("button");
		self::assertNull($sut->form);
	}

	public function testForm():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("button");
		$form = $sut->ownerDocument->createElement("form");
		$form->appendChild($sut);
		self::assertSame($form, $sut->form);
	}

	public function testFormNested():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("button");
		$form = $sut->ownerDocument->createElement("form");
		$child1 = $sut->ownerDocument->createElement("div");
		$child2 = $sut->ownerDocument->createElement("div");
		$child3 = $sut->ownerDocument->createElement("div");
		$form->appendChild($child1);
		$child1->appendChild($child2);
		$child2->appendChild($child3);
		$child3->appendChild($sut);
		self::assertSame($form, $sut->form);
	}

	public function testLabels():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("button");
		$sut->id = "sut";
		$parent = $sut->ownerDocument->createElement("div");
		$sut->ownerDocument->body->appendChild($parent);
		$label1 = $sut->ownerDocument->createElement("label");
		$label2 = $sut->ownerDocument->createElement("label");
		$parent->append($label1, $sut, $label2);
		$label1->htmlFor = "sut";
		$label2->htmlFor = "sut";

		$labels = $sut->labels;
		self::assertSame($label1, $labels->item(0));
		self::assertSame($label2, $labels->item(1));
	}

	public function testName():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("button");
		self::assertPropertyAttributeCorrelate($sut, "name");
	}

	public function testReadOnly():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("button");
		self::assertPropertyAttributeCorrelateBool($sut, "readonly", "readOnly");
	}

	public function testRequired():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("button");
		self::assertPropertyAttributeCorrelateBool($sut, "required");
	}

	public function testType():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("button");
		self::assertPropertyAttributeCorrelate($sut, "type");
	}

	public function testWillValidate():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("button");
		self::assertFalse($sut->willValidate);
	}

	public function testValidationMessage():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("button");
		self::assertEquals("", $sut->validationMessage);
	}

	public function testValidityState():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("button");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$state = $sut->validity;
	}

	public function testValue():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("button");
		self::assertPropertyAttributeCorrelate($sut, "value");
	}
}
