<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLDocument;

class HTMLOptionElementTest extends HTMLElementTestCase {
	public function testDefaultSelected():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("option");
		self::assertPropertyAttributeCorrelateBool($sut, "selected", "defaultSelected");
	}

	public function testDisabled():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("option");
		self::assertPropertyAttributeCorrelateBool($sut, "disabled");
	}

	public function testFormNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("option");
		self::assertNull($sut->form);
	}

	public function testFormParent():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("option");
		$form = $sut->ownerDocument->createElement("form");
		$form->appendChild($sut);
		self::assertSame($form, $sut->form);
	}

	public function testIndex():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("option");
		self::assertSame(0, $sut->index);
		$select = $sut->ownerDocument->createElement("select");
		$select->appendChild($sut);
		self::assertSame(0, $sut->index);

		for($i = 0; $i < 10; $i++) {
			$select->insertBefore($sut->cloneNode(), $sut);
			self::assertSame($i + 1, $sut->index);
		}
	}

	public function testLabelNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("option");
		self::assertSame("", $sut->label);
		$sut->textContent = "test";
		self::assertSame("test", $sut->label);
	}

	public function testLabel():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("option");
		self::assertPropertyAttributeCorrelate($sut, "label");
	}

	public function testSelected():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("option");
		self::assertPropertyAttributeCorrelateBool($sut, "selected");
	}

	public function testText():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("option");
		self::assertSame("", $sut->text);
		for($i = 0; $i < 10; $i++) {
			$text = uniqid();
			$sut->text = $text;
			self::assertEquals($text, $sut->textContent);
		}

		for($i = 0; $i < 10; $i++) {
			$text = uniqid();
			$sut->textContent = $text;
			self::assertEquals($text, $sut->text);
		}
	}

	public function testValueNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("option");
		self::assertSame("", $sut->value);
		$sut->textContent = "test";
		self::assertSame("test", $sut->value);
	}

	public function testValue():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("option");
		self::assertPropertyAttributeCorrelate($sut, "value");
	}

	public function testValue_textContentRelationship():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("option");
		$sut->textContent = "Test";
		self::assertSame("Test", $sut->value);

		$sut->value = "123";
		self::assertEquals("123", $sut->value);
		self::assertSame("Test", $sut->textContent);
	}
}
