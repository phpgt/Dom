<?php
namespace GT\Dom\Test\HTMLElement;


use GT\Dom\HTMLDocument;

class HTMLCanvasElementTest extends HTMLElementTestCase {
	public function testHeightDefault():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("canvas");
		self::assertEquals(0, $sut->height);
	}

	public function testHeight():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("canvas");
		$rand = rand(1, 5000);
		$sut->height = $rand;
		self::assertEquals($rand, $sut->height);
	}

	public function testWidthDefault():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("canvas");
		self::assertEquals(0, $sut->width);
	}

	public function testWidth():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("canvas");
		$rand = rand(1, 5000);
		$sut->width = $rand;
		self::assertEquals($rand, $sut->width);
	}
}
