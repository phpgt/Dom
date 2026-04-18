<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\Exception\ClientSideOnlyFunctionalityException;
use GT\Dom\HTMLDocument;

class HTMLObjectElementTest extends HTMLElementTestCase {
	public function testContentDocument():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("object");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$test = $sut->contentDocument;
	}

	public function testContentWindow():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("object");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$test = $sut->contentWindow;
	}

	public function testData():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("object");
		self::assertPropertyAttributeCorrelate($sut, "data");
	}

	public function testFormNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("object");
		self::assertNull($sut->form);
	}

	public function testForm():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("object");
		$form = $sut->ownerDocument->createElement("form");
		$form->appendChild($sut);
		self::assertSame($form, $sut->form);
	}

	public function testHeight():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("object");
		self::assertPropertyAttributeCorrelateNumber($sut, "int", "height");
	}

	public function testName():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("object");
		self::assertPropertyAttributeCorrelate($sut, "name");
	}

	public function testType():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("object");
		self::assertPropertyAttributeCorrelate($sut, "type");
	}

	public function testTypeMustMatch():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("object");
		self::assertPropertyAttributeCorrelateBool($sut, "typemustmatch", "typeMustMatch");
	}

	public function testUseMap():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("object");
		self::assertPropertyAttributeCorrelate($sut, "usemap", "useMap");
	}

	public function testValidationMessage():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("object");
		self::assertSame("", $sut->validationMessage);
	}

	public function testValidity():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("object");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$value = $sut->validity;
	}

	public function testWidth():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("object");
		self::assertPropertyAttributeCorrelateNumber($sut, "int", "width");
	}

	public function testWillValidate():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("object");
		self::assertFalse($sut->willValidate);
	}
}
