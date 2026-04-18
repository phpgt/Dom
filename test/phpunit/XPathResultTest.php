<?php
namespace GT\Dom\Test;

use GT\Dom\Test\TestFactory\DocumentTestFactory;
use GT\Dom\XMLDocument;
use PHPUnit\Framework\TestCase;

class XPathResultTest extends TestCase {
	public function testIterateNext():void {
		$document = new XMLDocument(DocumentTestFactory::XML_BREAKFAST_MENU);
		$sut = $document->evaluate("//food//name");
		$counter = 0;
		while($node = $sut->iterateNext()) {
			$counter++;
		}
		self::assertEquals(
			substr_count(DocumentTestFactory::XML_BREAKFAST_MENU, "<name"),
			$counter
		);
	}

	public function testIterator():void {
		$document = DocumentTestFactory::createXMLDocument(DocumentTestFactory::XML_BREAKFAST_MENU);
		$sut = $document->evaluate("//food//name");
		$tagNamesReceived = [];
		$i = 0;
		foreach($sut as $i => $node) {
			array_push($tagNamesReceived, $node);
		}

		self::assertEquals(
			substr_count(DocumentTestFactory::XML_BREAKFAST_MENU, "<name"),
			$i + 1
		);
		self::assertEquals(
			substr_count(DocumentTestFactory::XML_BREAKFAST_MENU, "<name"),
			count($tagNamesReceived)
		);
	}
}
