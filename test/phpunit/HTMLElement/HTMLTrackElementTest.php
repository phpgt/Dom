<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\Exception\ClientSideOnlyFunctionalityException;
use GT\Dom\HTMLDocument;

class HTMLTrackElementTest extends HTMLElementTestCase {
	public function testKind():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("track");
		self::assertPropertyAttributeCorrelate($sut, "kind");
	}

	public function testSrc():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("track");
		self::assertPropertyAttributeCorrelate($sut, "src");
	}

	public function testSrclang():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("track");
		self::assertPropertyAttributeCorrelate($sut, "srclang");
	}

	public function testLabel():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("track");
		self::assertPropertyAttributeCorrelate($sut, "label");
	}

	public function testDefault():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("track");
		self::assertPropertyAttributeCorrelateBool($sut, "default");
	}

	public function testReadyState():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("track");
		self::assertSame(0, $sut->readyState);
	}

	public function testTrack():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("track");
		self::expectException(ClientSideOnlyFunctionalityException::class);

		/** @noinspection PhpUnusedLocalVariableInspection */
		$value = $sut->track;
	}
}
