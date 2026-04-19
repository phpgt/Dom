<?php
namespace GT\Dom\Test;

use GT\Dom\Element;
use GT\Dom\ElementType;
use GT\Dom\HTMLDocument;
use GT\Dom\Text;
use PHPUnit\Framework\TestCase;

class ParentNodeTest extends TestCase {
	public function testChildElementCountEmpty():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertEquals(0, $sut->childElementCount);
	}

	public function testChildElementCount():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$count = rand(50, 500);
		for($i = 0; $i < $count; $i++) {
			$child = $sut->ownerDocument->createElement("child");
			$sut->appendChild($child);
		}

		self::assertEquals($count, $sut->childElementCount);
	}

	public function testChildElementCountMixed():void {
		$document = new HTMLDocument();
		$document->body->append(
			$document->createElement("div"),
			$document->createElement("div"),
			"test",
			$document->createElement("div"),
		);
		self::assertSame($document->body->children->length, $document->body->childElementCount);
	}

	public function testChildElementCountNonElement():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$count = rand(50, 500);
		$textCount = 0;
		for($i = 0; $i < $count; $i++) {
			if($i % 25 === 0) {
				$child = $sut->ownerDocument->createTextNode("Some text");
				$textCount++;
			}
			else {
				$child = $sut->ownerDocument->createElement("child");
			}
			$sut->appendChild($child);
		}

		self::assertEquals($count - $textCount, $sut->childElementCount);
	}

	public function testChildrenEmpty():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertCount(0, $sut->children);
		self::assertEquals(0, $sut->children->length);
	}

	public function testChildren():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");

		$count = rand(10, 50);
		for($i = 0; $i < $count; $i++) {
			$child = $sut->ownerDocument->createElement("child");
			$sut->appendChild($child);
		}

		self::assertCount($count, $sut->children);
		self::assertEquals($count, $sut->children->length);
	}

	public function testChildrenNonElement():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$count = rand(10, 50);
		$textCount = 0;
		for($i = 0; $i < $count; $i++) {
			if($i % 25 === 0) {
				$child = $sut->ownerDocument->createTextNode("Some text");
				$textCount++;
			}
			else {
				$child = $sut->ownerDocument->createElement("child");
			}
			$sut->appendChild($child);
		}

		self::assertEquals($count - $textCount, $sut->children->length);
		self::assertCount($count - $textCount, $sut->children);
	}

	public function testFirstElementChildEmpty():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertNull($sut->firstElementChild);
	}

	public function testFirstElementChild():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$child1 = $sut->ownerDocument->createElement("child");
		$child2 = $sut->ownerDocument->createElement("child");
		$sut->append($child1, $child2);
		self::assertSame($child1, $sut->firstElementChild);
	}

	public function testFirstElementChildTextNode():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$child1 = $sut->ownerDocument->createElement("child");
		$child2 = $sut->ownerDocument->createElement("child");
		$text = $sut->ownerDocument->createTextNode("Some text");
		$sut->append($text, $child1, $child2);
		self::assertSame($child1, $sut->firstElementChild);
	}

	public function testFirstElementChildAllTextNodes():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$child1 = $sut->ownerDocument->createTextNode("Some text");
		$child2 = $sut->ownerDocument->createTextNode("Some more text");
		$sut->append($child1, $child2);
		self::assertNull($sut->firstElementChild);
	}

	public function testLastElementChildEmpty():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertNull($sut->lastElementChild);
	}

	public function testLastElementChild():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$child1 = $sut->ownerDocument->createElement("child");
		$child2 = $sut->ownerDocument->createElement("child");
		$sut->append($child1, $child2);
		self::assertSame($child2, $sut->lastElementChild);
	}

	public function testLastElementChildAllTextNodes():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$child1 = $sut->ownerDocument->createTextNode("Some text");
		$child2 = $sut->ownerDocument->createTextNode("Some more text");
		$sut->append($child1, $child2);
		self::assertNull($sut->lastElementChild);
	}

	public function testLastElementChildAllTextNodesAfterFirst():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$child1 = $sut->ownerDocument->createElement("child");
		$child2 = $sut->ownerDocument->createTextNode("Some text");
		$child3 = $sut->ownerDocument->createTextNode("Some more text");
		$sut->append($child1, $child2, $child3);
		self::assertSame($child1, $sut->lastElementChild);
	}

	public function testPrependString():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$child = $sut->ownerDocument->createElement("child");
		$sut->appendChild($child);
		$sut->prepend("Some text");
		self::assertInstanceOf(Text::class, $sut->firstChild);
	}

	public function testPrependNode():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$child = $sut->ownerDocument->createElement("child");
		$toPrepend = $sut->ownerDocument->createElement("prepend-me");
		$sut->appendChild($child);
		$sut->prepend($toPrepend);
		self::assertSame($toPrepend, $sut->firstChild);
	}

	public function testPrependMixed():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$document->documentElement->appendChild($sut);

		$child = $sut->ownerDocument->createElement("child");
		$sut->appendChild($child);

		$toPrepend = [
			$sut->ownerDocument->createElement("first-prepend"),
			$sut->ownerDocument->createElement("second-prepend"),
			"First text",
			$sut->ownerDocument->createElement("third-prepend"),
			$sut->ownerDocument->createTextNode("Second text"),
		];
		$sut->prepend(...$toPrepend);

		foreach($sut->childNodes as $i => $node) {
			if($i < count($toPrepend)) {
				$expected = $toPrepend[$i];
				if($expected instanceof Element) {
					self::assertSame($toPrepend[$i], $node);
				}
				else {
					$text = $expected;
					if($text instanceof Text) {
						$text = $text->nodeValue;
					}

					self::assertSame($text, $node->nodeValue);
				}
			}
			else {
				self::assertSame($child, $node);
			}
		}
	}

	public function testAppendElement():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$child = $sut->ownerDocument->createElement("child");
		$sut->appendChild($child);
		$toAppend = $sut->ownerDocument->createElement("to-append");
		$sut->append($toAppend);
		self::assertSame($toAppend, $sut->lastChild);
	}

	public function testAppendText():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$child = $sut->ownerDocument->createElement("child");
		$sut->append($child, "One", "Two");
		self::assertCount(3, $sut->childNodes);
		self::assertEquals("One", $sut->childNodes[1]->nodeValue);
		self::assertEquals("Two", $sut->childNodes[2]->nodeValue);
	}

	public function testReplaceChildren():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$child = $sut->ownerDocument->createElement("child");
		$sut->appendChild($child->cloneNode());
		$sut->appendChild($child->cloneNode());
		$sut->appendChild($child->cloneNode());
		self::assertCount(3, $sut->childNodes);
		$sut->replaceChildren("Hello!");
		self::assertCount(1, $sut->childNodes);
		self::assertInstanceOf(Text::class, $sut->firstChild);
		self::assertEquals("Hello!", $sut->firstChild->nodeValue);
	}

	public function testReplaceChildrenMultiple():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("test-parent");
		$child = $document->createElement("test-child");
		$sut->appendChild($child->cloneNode());
		$sut->appendChild($child->cloneNode());
		$sut->appendChild($child->cloneNode());
		self::assertCount(3, $sut->childNodes);
		self::assertCount(3, $sut->children);
		$sut->replaceChildren(
			"Hello!",
			$document->createElement("test-replacement"),
			"PHPUnit!",
		);
		self::assertCount(3, $sut->childNodes);
		self::assertCount(1, $sut->children);
		self::assertEquals("Hello!", $sut->firstChild->nodeValue);
		self::assertEquals("PHPUnit!", $sut->lastChild->nodeValue);
		self::assertEquals("test-replacement", $sut->firstElementChild->tagName);
	}

	public function testQuerySelector():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$document->body->appendChild($sut);

		$child1 = $sut->ownerDocument->createElement("child");
		$child2 = $sut->ownerDocument->createElement("child");
		$child3 = $sut->ownerDocument->createElement("child");
		$inner = $sut->ownerDocument->createElement("inner");

		$sut->append($child1, $child2, $child3);
		$child2->append($inner);

		self::assertSame($inner, $sut->querySelector("child>inner"));
		self::assertSame($inner, $sut->querySelector("inner"));
		self::assertNull($sut->querySelector("nothing"));
	}

	public function testQuerySelectorAll():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$document->body->appendChild($sut);

		$child1 = $sut->ownerDocument->createElement("child");
		$child2 = $sut->ownerDocument->createElement("child");
		$child3 = $sut->ownerDocument->createElement("child");
		$inner = $sut->ownerDocument->createElement("inner");

		$sut->append($child1, $child2, $child3);
		$child2->append($inner);

		self::assertCount(3, $sut->querySelectorAll("child"));
		self::assertCount(4, $sut->querySelectorAll("child, inner"));
		self::assertCount(4, $sut->querySelectorAll("child, inner, inner>child, child>inner"));
	}

	public function testQuerySelectorAttributeWithoutValue():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$sut->innerHTML = "<div data-test></div>";
		$document->body->appendChild($sut);

		$div = $sut->querySelector("div");
		self::assertSame(ElementType::HTMLDivElement, $div->elementType);

		self::assertSame($div, $sut->ownerDocument->querySelector("[data-test='']"));
	}
}
