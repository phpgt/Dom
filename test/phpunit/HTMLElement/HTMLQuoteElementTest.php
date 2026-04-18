<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\ElementType;
use GT\Dom\HTMLDocument;

class HTMLQuoteElementTest extends HTMLElementTestCase {
	public function testCite():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("blockquote");
		self::assertPropertyAttributeCorrelate($sut, "cite");
	}

	public function testBlockquote():void {
		$document = new HTMLDocument();
		self::assertSame(
			ElementType::HTMLQuoteElement,
			$document->createElement("blockquote")->elementType
		);
	}

	public function testQ():void {
		$document = new HTMLDocument();
		self::assertSame(
			ElementType::HTMLQuoteElement,
			$document->createElement("q")->elementType
		);
	}
}
