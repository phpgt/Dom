<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\HTMLDocument;
use GT\Dom\Test\TestFactory\DocumentTestFactory;

class HTMLScriptElementTest extends HTMLElementTestCase {
	public function testType():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("script");
		self::assertPropertyAttributeCorrelate($sut, "type");
	}

	public function testSrc():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("script");
		self::assertPropertyAttributeCorrelate($sut, "src");
	}

	public function testAsync():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("script");
		self::assertPropertyAttributeCorrelateBool($sut, "async");
	}

	public function testDefer():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("script");
		self::assertPropertyAttributeCorrelateBool($sut, "defer");
	}

	public function testCrossOrigin():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("script");
		self::assertPropertyAttributeCorrelate($sut, "crossorigin", "crossOrigin");
	}

	public function testTextEmpty():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("script");
		self::assertSame("", $sut->text);
	}

	public function testText():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("script");
		$sut->text = "alert('test')";
		self::assertEquals("alert('test')", $sut->textContent);
	}

	public function testNoModule():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("script");
		self::assertPropertyAttributeCorrelateBool($sut, "nomodule", "noModule");
	}

	public function testReferrerPolicy():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("script");
		self::assertPropertyAttributeCorrelate($sut, "referrerpolicy", "referrerPolicy");
	}

	public function testInlineScriptTags():void {
		$document = new HTMLDocument(DocumentTestFactory::HTML_INLINE_SCRIPT_WITH_TAGS);
		$sut = $document->querySelector("script");
		$innerHTML = $sut->innerHTML;

		self::assertStringContainsString("<h2>lorem ipsum</h2>", $innerHTML);
		self::assertStringContainsString("<p>lorem <strong>lorem ipsum</strong></p>", $innerHTML);
		self::assertStringContainsString("<button class=\"button\">click</button>", $innerHTML);
		self::assertStringContainsString("</div>", $innerHTML);
	}
}
