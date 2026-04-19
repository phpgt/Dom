<?php
namespace GT\Dom\Test;

use GT\Dom\Exception\IndexSizeException;
use GT\Dom\HTMLDocument;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase {
	public function testIsElementContentWhitespaceEmptyContent():void {
		$document = new HTMLDocument();
		$sut = $document->createTextNode("");
		self::assertTrue($sut->isElementContentWhitespace);
	}

	public function testIsElementContentWhitespaceNonEmptyContent():void {
		$document = new HTMLDocument();
		$sut = $document->createTextNode("Hello, World");
		self::assertFalse($sut->isElementContentWhitespace);
	}

	public function testIsElementContentWhitespaceJustSpacesAndTabsContent():void {
		$document = new HTMLDocument();
		$sut = $document->createTextNode("

  	                ");
		self::assertTrue($sut->isElementContentWhitespace);
	}

	public function testWholeTextEmpty():void {
		$document = new HTMLDocument();
		$sut = $document->createTextNode("");
		self::assertSame("", $sut->wholeText);
	}

	public function testWholeTextSingle():void {
		$document = new HTMLDocument();
		$sut = $document->createTextNode("test");
		self::assertSame("test", $sut->wholeText);
	}

	public function testWholeTextSiblings():void {
		$document = new HTMLDocument();
		$sut = $document->createTextNode("test");
		$test1 = $sut->ownerDocument->createTextNode("one");
		$test2 = $sut->ownerDocument->createTextNode("two");
		$parent = $sut->ownerDocument->createElement("test-parent");
		$parent->append($test1, $sut, $test2);
		self::assertSame("onetesttwo", $sut->wholeText);
	}

	public function testWholeTextNonTextSiblings():void {
		$document = new HTMLDocument();
		$sut = $document->createTextNode("test");
		$test1 = $sut->ownerDocument->createElement("test-one");
		$test2 = $sut->ownerDocument->createTextNode("two");
		$test3 = $sut->ownerDocument->createTextNode("three");
		$test4 = $sut->ownerDocument->createElement("test-four");
		$parent = $sut->ownerDocument->createElement("test-parent");
		$parent->append($test1, $test2, $sut, $test3, $test4);
		self::assertSame("twotestthree", $sut->wholeText);
	}

	public function testSplitTextZero():void {
		$document = new HTMLDocument();
		$sut = $document->createTextNode("test");
		self::assertSame("test", $sut->splitText(0)->textContent);
	}

	public function testSplitTextIndex():void {
		$document = new HTMLDocument();
		$sut = $document->createTextNode("test");
		self::assertSame("st", $sut->splitText(2)->textContent);
	}

	public function testSplitTextOutOfBounds():void {
		$document = new HTMLDocument();
		$sut = $document->createTextNode("test");
		self::expectException(IndexSizeException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$value = $sut->splitText(200);
	}

	public function testSplitTextInsertsNewNode():void {
		$document = new HTMLDocument();
		$sut = $document->createTextNode("Hello, {{name}}!");
		$parent = $sut->ownerDocument->createElement("div");
		$parent->appendChild($sut);
		self::assertCount(1, $parent->childNodes);

		$split = $sut->splitText(
			strpos($sut->textContent, "{{")
		);
		self::assertCount(2, $parent->childNodes);
		self::assertSame("Hello, ", $sut->textContent);
		self::assertSame("{{name}}!", $split->textContent);
		self::assertSame("Hello, {{name}}!", $parent->textContent);

		$split->splitText(
			strpos($split->textContent, "}}") + 2
		);

		self::assertSame("{{name}}", $split->textContent);

// A test to emulate how a templating system can work:
		self::assertSame(
			"<div>Hello, {{name}}!</div>",
			$parent->outerHTML
		);
		$split->textContent = "Cody";
		self::assertSame(
			"<div>Hello, Cody!</div>",
			$parent->outerHTML
		);
	}
}
