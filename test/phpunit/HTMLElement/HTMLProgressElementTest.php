<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLDocument;

class HTMLProgressElementTest extends HTMLElementTestCase {
	public function testMax():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("progress");
		self::assertPropertyAttributeCorrelateNumber($sut, "?float", "max");
	}

	public function testPositionNoValueOrMax():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("progress");
		self::assertSame(-1.0, $sut->position);
	}

	public function testPositionNoMax():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("progress");
		for($i = 0; $i < 10; $i++) {
			$sut->value = rand(-999, 999);
			self::assertSame(-1.0, $sut->position);
		}
	}

	public function testPosition():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("progress");
		for($i = 0; $i < 10; $i++) {
			do {
				$sut->value = (string)rand(-999, 999);
			}
			while($sut->value > -0.1 && $sut->value < 0.1);

			do {
				$sut->max = rand(-999, 999);
			}
			while($sut->max > -0.1 && $sut->max < 0.1);

			$calc = $sut->value / $sut->max;
			if($calc > 1) {
				$calc = 1;
			}
			self::assertEquals($calc, $sut->position);
		}
	}

	public function testValue():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("progress");
		self::assertPropertyAttributeCorrelateNumber($sut, "?float", "value");
	}
}
