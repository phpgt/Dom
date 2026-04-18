<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLDocument;

class HTMLLabelElementTest extends HTMLElementTestCase {
	public function testControlNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("label");
		self::assertNull($sut->control);
	}

	public function testControlChild():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("label");
		$input = $sut->ownerDocument->createElement("input");
		$sut->appendChild($sut->ownerDocument->createElement("div"));
		$sut->appendChild($input);
		self::assertSame($input, $sut->control);
	}

	public function testControlFor():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("label");
		$sut->htmlFor = "example";
		$input = $sut->ownerDocument->createElement("input");
		$input->id = "example";
		$sut->ownerDocument->body->append($sut, $input);
		self::assertSame($input, $sut->control);
	}

	public function testFormNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("label");
		self::assertNull($sut->form);
	}

	public function testFormParent():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("label");
		$form = $sut->ownerDocument->createElement("form");
		$form->appendChild($sut);
		self::assertSame($form, $sut->form);
	}

	public function testFormForInput():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("label");
		$sut->htmlFor = "example";
		$input = $document->createElement("input");
		$input->id = "example";
		$form = $document->createElement("form");
		$form->appendChild($input);
		$document->body->append($sut, $form);

		self::assertSame($form, $sut->form);
	}

	public function testFor():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("label");
		self::assertPropertyAttributeCorrelate($sut, "for", "htmlFor");
	}
}
