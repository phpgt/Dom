<?php
namespace GT\Dom\Test;

use GT\Dom\HTMLDocument;
use PHPUnit\Framework\TestCase;

class DocumentFragmentTest extends TestCase {
	public function testGetElementByIdEmpty():void {
		$document = new HTMLDocument();
		$sut = $document->createDocumentFragment();
		self::assertNull($sut->getElementById("nothing"));
	}

	public function testGetElementById():void {
		$document = new HTMLDocument();
		$sut = $document->createDocumentFragment();
		$nodeWithId = $sut->ownerDocument->createElement("div");
		$nodeWithId->id = "test";
		$sut->appendChild($nodeWithId);
		self::assertSame($nodeWithId, $sut->getElementById("test"));
	}

	public function testAppendMultipleNodesThenAddToParentElement():void {
		$document = new HTMLDocument();
		$sut = $document->createDocumentFragment();
		$expectedString = "";
		for($i = 0; $i < 10; $i++) {
			$textNode = $document->createTextNode("Node$i");
			$sut->append($textNode);
			$expectedString .= "Node$i";
		}

		$document->documentElement->append($sut);
		self::assertSame($expectedString, $document->textContent);
	}
}
