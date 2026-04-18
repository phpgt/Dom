<?php
namespace GT\Dom\Test;

use GT\Dom\HTMLDocument;
use GT\Dom\Text;
use PHPUnit\Framework\TestCase;
use Throwable;

class ChildNodeTest extends TestCase {
	public function testRemoveNoParent():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$exception = null;
		try {
			$sut->remove();
		}
		catch(Throwable $exception) {}
		self::assertNull($exception);
	}

	public function testRemove():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$parent = $sut->ownerDocument->createElement("example-parent");
		$parent->appendChild($sut);
		self::assertSame($parent, $sut->parentElement);
		$sut->remove();
		self::assertNull($sut->parentElement);
	}

	public function testBeforeNoParent():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$exception = null;
		try {
			$sut->before("something");
		}
		catch(Throwable $exception) {}
		self::assertNull($exception);
	}

	public function testBefore():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$parent = $sut->ownerDocument->createElement("example-parent");
		$parent->appendChild($sut);

		self::assertNull($sut->previousSibling);
		$sut->before("example");
		self::assertInstanceOf(Text::class, $sut->previousSibling);
	}

	public function testAfterNoParent():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$exception = null;
		try {
			$sut->after("something");
		}
		catch(Throwable $exception) {}
		self::assertNull($exception);
	}

	public function testAfter():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$parent = $sut->ownerDocument->createElement("example-parent");
		$parent->appendChild($sut);

		self::assertNull($sut->previousSibling);
		$sut->after("example");
		self::assertInstanceOf(Text::class, $sut->nextSibling);
	}

	public function testReplaceWithNoParent():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$exception = null;

		try {
			$sut->replaceWith("nothing");
		}
		catch(Throwable $exception) {}

		self::assertNull($exception);
	}

	public function testReplaceWith():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$sut->innerHTML = "one";
		$parent = $sut->ownerDocument->createElement("example-parent");
		$parent->appendChild($sut);

		self::assertSame("one", $parent->firstChild->textContent);
		$sut->replaceWith("two");
		self::assertSame("two", $parent->firstChild->textContent);
	}
}
