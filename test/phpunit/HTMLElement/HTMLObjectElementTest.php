<?php
namespace Gt\Dom\Test\HTMLElement;

use Gt\Dom\HTMLElement\HTMLObjectElement;
use Gt\Dom\Test\TestFactory\NodeTestFactory;

class HTMLObjectElementTest extends HTMLElementTestCase {
	public function testContentDocument():void {
		/** @var HTMLObjectElement $sut */
		$sut = NodeTestFactory::createHTMLElement("object");
		self::assertNull($sut->contentDocument);
	}

	public function testContentWindow():void {
		/** @var HTMLObjectElement $sut */
		$sut = NodeTestFactory::createHTMLElement("object");
		self::assertNull($sut->contentWindow);
	}

	public function testData():void {
		/** @var HTMLObjectElement $sut */
		$sut = NodeTestFactory::createHTMLElement("object");
		self::assertPropertyAttributeCorrelate($sut, "data");
	}
}
