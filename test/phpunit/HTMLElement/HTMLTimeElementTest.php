<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLDocument;

class HTMLTimeElementTest extends HTMLElementTestCase {
	public function testDateTime():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("time");
		self::assertPropertyAttributeCorrelate($sut, "datetime", "dateTime");
	}
}
