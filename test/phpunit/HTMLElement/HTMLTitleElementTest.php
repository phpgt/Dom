<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLDocument;

class HTMLTitleElementTest extends HTMLElementTestCase {
	public function testText():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("title");
		self::assertSame("", $sut->text);

		for($i = 0; $i < 10; $i++) {
			$t = uniqid();
			$sut->text = $t;
			self::assertSame($t, $sut->innerText);
		}
	}
}
