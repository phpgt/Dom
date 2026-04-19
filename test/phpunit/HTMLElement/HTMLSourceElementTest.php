<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLDocument;

class HTMLSourceElementTest extends HTMLElementTestCase {
	public function testMedia():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("source");
		self::assertPropertyAttributeCorrelate($sut, "media");
	}

	public function testSizes():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("source");
		self::assertPropertyAttributeCorrelate($sut, "sizes");
	}

	public function testSrc():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("source");
		self::assertPropertyAttributeCorrelate($sut, "src");
	}

	public function testSrcset():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("source");
		self::assertPropertyAttributeCorrelate($sut, "srcset");
	}

	public function testType():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("source");
		self::assertPropertyAttributeCorrelate($sut, "type");
	}
}
