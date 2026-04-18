<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLDocument;

class HTMLTextAreaElementTest extends HTMLElementTestCase {
	public function testAutocapitalize():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("textarea");
		self::assertPropertyAttributeCorrelate($sut, "autocapitalize");
	}

	public function testCols():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("textarea");
		self::assertPropertyAttributeCorrelateNumber($sut, "int:20", "cols");
	}

	public function testMaxLength():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("textarea");
		self::assertPropertyAttributeCorrelateNumber($sut, "int:-1", "maxlength", "maxLength");
	}

	public function testMinLength():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("textarea");
		self::assertPropertyAttributeCorrelateNumber($sut, "int:-1", "minlength", "minLength");
	}

	public function testRows():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("textarea");
		self::assertPropertyAttributeCorrelateNumber($sut, "int:2", "rows");
	}

	public function testWrap():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("textarea");
		self::assertPropertyAttributeCorrelate($sut, "wrap");
	}
}
