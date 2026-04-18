<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\Exception\ClientSideOnlyFunctionalityException;
use GT\Dom\HTMLDocument;

class HTMLIFrameElementTest extends HTMLElementTestCase {
	public function testGetContentDocument():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("iframe");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$value = $sut->contentDocument;
	}

	public function testGetContentWindow():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("iframe");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$value = $sut->contentWindow;
	}

	public function testHeight():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("iframe");
		self::assertPropertyAttributeCorrelateNumber($sut, "int", "height");
	}

	public function testName():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("iframe");
		self::assertPropertyAttributeCorrelate($sut, "name");
	}

	public function testReferrerPolicy():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("iframe");
		self::assertPropertyAttributeCorrelate($sut, "referrerpolicy", "referrerPolicy");
	}

	public function testSrc():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("iframe");
		self::assertPropertyAttributeCorrelate($sut, "src");
	}

	public function testSrcdoc():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("iframe");
		self::assertPropertyAttributeCorrelate($sut, "srcdoc");
	}

	public function testWidth():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("iframe");
		self::assertPropertyAttributeCorrelateNumber($sut, "int", "width");
	}
}
