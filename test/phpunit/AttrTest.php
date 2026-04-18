<?php
namespace GT\Dom\Test;

use GT\Dom\HTMLDocument;
use PHPUnit\Framework\TestCase;

class AttrTest extends TestCase {
	public function testPrefixNoNamespace():void {
		$document = new HTMLDocument();
		$testElement = $document->createElement("test-element");
		$testElement->innerHTML = "<example abc:example='123'></example>";
		$document->body->appendChild($testElement);
		$node = $testElement->children[0];
		$sut = $node->attributes[0];
		self::assertEquals("", $sut->prefix);
		self::assertEquals("abc:example", $sut->name);
	}

	public function testOwnerElementEmpty():void {
		$document = new HTMLDocument();
		$root = $document->createElement("root");
		$document->body->appendChild($root);
		$sut = $document->createAttribute("example");
		self::assertNull($sut->ownerElement);
	}
//
	public function testSpecified():void {
		$sut = (new HTMLDocument())->createAttribute("example");
// Another weird DOM quirk, but again, true to spec. Always true.
		self::assertTrue($sut->specified);
	}

	public function testValue():void {
		$sut = (new HTMLDocument())->createAttribute("example");
		self::assertEquals("", $sut->value);
		$sut->value = "test";
		self::assertEquals("test", $sut->value);
	}
}
