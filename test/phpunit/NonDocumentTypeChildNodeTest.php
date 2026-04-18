<?php

namespace GT\Dom\Test;

use GT\Dom\HTMLDocument;
use PHPUnit\Framework\TestCase;


class NonDocumentTypeChildNodeTest extends TestCase {
	public function testNextElementSibling():void {
		$document = new HTMLDocument();
		$parent = $document->createElement("parent");
		$c1 = $document->createElement("child");
		$sut = $document->createElement("child");
		$txt = 'non Element';
		$c2 = $document->createElement("child");

		$parent->append($c1, $sut, $txt, $c2);
		self::assertSame($c2, $sut->nextElementSibling);
	}

	public function testNextElementSiblingNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertNull($sut->nextElementSibling);
	}

	public function testPreviousElementSibling():void {
		$document = new HTMLDocument();
		$parent = $document->createElement("parent");
		$c1 = $document->createElement("child");
		$txt = 'non Element';
		$sut = $document->createElement("child");
		$c2 = $document->createElement("child");

		$parent->append($c1, $txt, $sut, $c2);
		self::assertSame($c1, $sut->previousElementSibling);
	}

	public function testPreviousElementSiblingNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertNull($sut->previousElementSibling);
	}
}
