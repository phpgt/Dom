<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\Exception\ClientSideOnlyFunctionalityException;
use GT\Dom\HTMLDocument;

class HTMLLinkElementTest extends HTMLElementTestCase {
	public function testAs():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("link");
		self::assertPropertyAttributeCorrelate($sut, "as");
	}

	public function testDisabled():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("link");
		self::assertPropertyAttributeCorrelateBool($sut, "disabled");
	}

	public function testHref():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("link");
		self::assertPropertyAttributeCorrelate($sut, "href");
	}

	public function testHreflang():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("link");
		self::assertPropertyAttributeCorrelate($sut, "hreflang");
	}

	public function testMedia():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("link");
		self::assertPropertyAttributeCorrelate($sut, "media");
	}

	public function testReferrerPolicy():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("link");
		self::assertPropertyAttributeCorrelate($sut, "referrerpolicy", "referrerPolicy");
	}

	public function testRel():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("link");
		self::assertPropertyAttributeCorrelate($sut, "rel");
	}

	public function testRelList():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("link");
		$sut->rel = "one";
		$relList = $sut->relList;
		self::assertCount(1, $relList);
		self::assertEquals("one", $relList->item(0));
		$sut->rel .= " two";
		self::assertCount(2, $relList);
		self::assertEquals("one", $relList->item(0));
		self::assertEquals("two", $relList->item(1));
		$relList->value = "three four";
		self::assertEquals("three four", $sut->rel);
	}

	public function testSizes():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("link");
		self::assertPropertyAttributeCorrelate($sut, "sizes");
	}

	public function testSheet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("link");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$value = $sut->sheet;
	}

	public function testType():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("link");
		self::assertPropertyAttributeCorrelate($sut, "type");
	}
}
