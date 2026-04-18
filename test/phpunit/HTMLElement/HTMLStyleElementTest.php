<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\Exception\ClientSideOnlyFunctionalityException;
use GT\Dom\HTMLDocument;

class HTMLStyleElementTest extends HTMLElementTestCase {
	public function testMedia():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("style");
		self::assertPropertyAttributeCorrelate($sut, "media");
	}

	public function testType():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("style");
		self::assertPropertyAttributeCorrelate($sut, "type");
	}

	public function testDisabled():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("style");
		self::assertPropertyAttributeCorrelateBool($sut, "disabled");
	}

	public function testSheet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("style");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$sheet = $sut->sheet;
	}
}
