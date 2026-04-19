<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\Exception\ClientSideOnlyFunctionalityException;
use GT\Dom\HTMLDocument;

class HTMLImageElementTest extends HTMLElementTestCase {
	public function testAlt():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("img");
		self::assertPropertyAttributeCorrelate($sut, "alt");
	}

	public function testComplete():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("img");
		self::assertFalse($sut->complete);
	}

	public function testCrossOrigin():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("img");
		self::assertPropertyAttributeCorrelate($sut, "crossorigin", "crossOrigin");
	}

	public function testCurrentSrc():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("img");
		self::assertSame("", $sut->currentSrc);
	}

	public function testDecoding():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("img");
		self::assertPropertyAttributeCorrelate($sut, "decoding");
	}

	public function testHeight():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("img");
		self::assertPropertyAttributeCorrelateNumber($sut, "?int", "height");
	}

	public function testIsMap():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("img");
		self::assertPropertyAttributeCorrelateBool($sut, "ismap", "isMap");
	}

	public function testLoading():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("img");
		self::assertPropertyAttributeCorrelate($sut, "loading");
	}

	public function testNaturalHeight():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("img");
		self::assertEquals(0, $sut->naturalHeight);
	}

	public function testNaturalWidth():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("img");
		self::assertEquals(0, $sut->naturalWidth);
	}

	public function testReferrerPolicy():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("img");
		self::assertPropertyAttributeCorrelate($sut, "referrerpolicy", "referrerPolicy");
	}

	public function testSizes():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("img");
		self::assertPropertyAttributeCorrelate($sut, "sizes");
	}

	public function testSrc():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("img");
		self::assertPropertyAttributeCorrelate($sut, "src");
	}

	public function testSrcset():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("img");
		self::assertPropertyAttributeCorrelate($sut, "srcset");
	}

	public function testUseMap():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("img");
		self::assertPropertyAttributeCorrelate($sut, "usemap", "useMap");
	}

	public function testWidth():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("img");
		self::assertPropertyAttributeCorrelateNumber($sut, "?int", "width");
	}

	public function testX():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("img");
		self::assertSame(0, $sut->x);
	}

	public function testY():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("img");
		self::assertSame(0, $sut->y);
	}
}
