<?php
namespace GT\Dom\Test;

use GT\Dom\Element;
use GT\Dom\Exception\HTMLCollectionImmutableException;
use GT\Dom\HTMLCollectionFactory;
use GT\Dom\NodeListFactory;
use GT\Dom\RadioNodeList;
use GT\Dom\Test\TestFactory\NodeTestFactory;
use PHPUnit\Framework\TestCase;

class HTMLCollectionTest extends TestCase {
	public function testLength():void {
		$sut = HTMLCollectionFactory::create(fn() => NodeListFactory::create(
			NodeTestFactory::createNode("example"),
			NodeTestFactory::createNode("example")
		));
		self::assertEquals(2, $sut->length);
	}

	public function testCount():void {
		$sut = HTMLCollectionFactory::create(fn() => NodeListFactory::create(
			NodeTestFactory::createNode("example"),
			NodeTestFactory::createNode("example")
		));
		self::assertCount(2, $sut);
	}

	public function testItem():void {
		$element1 = NodeTestFactory::createNode("example");
		$element2 = NodeTestFactory::createNode("example");
		$sut = HTMLCollectionFactory::create(fn() => NodeListFactory::create(
			$element1,
			$element2
		));
		self::assertSame($element2, $sut->item(1));
	}

	public function testItemNone():void {
		$element1 = NodeTestFactory::createNode("example");
		$element2 = NodeTestFactory::createNode("example");
		$sut = HTMLCollectionFactory::create(fn() => NodeListFactory::create(
			$element1,
			$element2
		));
		self::assertNull($sut->item(2));
	}

	public function testNamedItem():void {
		$element1 = NodeTestFactory::createNode("example");
		$element1->id = "first";
		$element1->setAttribute("name", "abc");
		$element2 = NodeTestFactory::createNode("example");
		$element2->id = "second";
		$element2->setAttribute("name", "xyz");

		$sut = HTMLCollectionFactory::create(fn() => NodeListFactory::create(
			$element1,
			$element2
		));
		self::assertSame($element1, $sut->namedItem("first"));
		self::assertSame($element1, $sut->namedItem("abc"));
		self::assertSame($element2, $sut->namedItem("second"));
		self::assertSame($element2, $sut->namedItem("xyz"));
		self::assertNull($sut->namedItem("nope"));
	}

	public function testNamedItemRadio():void {
		$radioElementList = [];

		for($i = 0; $i < 10; $i++) {
			$radio = NodeTestFactory::createHTMLElement("input");
			$radio->type = "radio";
			$radio->name = "example";
			$radio->value = "val-$i";
			array_push($radioElementList, $radio);
		}

		$radioElementList[2]->checked = true;

		$sut = HTMLCollectionFactory::create(fn() => NodeListFactory::create(
			...$radioElementList
		));

		$radioNodeList = $sut->namedItem("example");
		self::assertInstanceOf(RadioNodeList::class, $radioNodeList);
		self::assertEquals("val-2", $radioNodeList->value);
	}

	public function testNamedItemRadio_noChecked():void {
		$radioElementList = [];

		for($i = 0; $i < 10; $i++) {
			$radio = NodeTestFactory::createHTMLElement("input");
			$radio->type = "radio";
			$radio->name = "example";
			$radio->value = "val-$i";
			array_push($radioElementList, $radio);
		}

		$sut = HTMLCollectionFactory::create(fn() => NodeListFactory::create(
			...$radioElementList
		));

		$radioNodeList = $sut->namedItem("example");
		self::assertInstanceOf(RadioNodeList::class, $radioNodeList);
		self::assertEquals("", $radioNodeList->value);
	}

	public function testNamedItemRadio_checkbox():void {
		$radioElementList = [];

		for($i = 0; $i < 10; $i++) {
			$radio = NodeTestFactory::createHTMLElement("input");
			$radio->type = "checkbox";
			$radio->name = "example";
			$radio->value = "val-$i";
			array_push($radioElementList, $radio);
		}

		$radioElementList[2]->checked = true;

		$sut = HTMLCollectionFactory::create(fn() => NodeListFactory::create(
			...$radioElementList
		));

		$radioNodeList = $sut->namedItem("example");
		self::assertInstanceOf(RadioNodeList::class, $radioNodeList);
		self::assertEquals("", $radioNodeList->value);
	}

	public function testNamedItemRadio_setValue():void {
		$radioElementList = [];

		for($i = 0; $i < 10; $i++) {
			$radio = NodeTestFactory::createHTMLElement("input");
			$radio->type = "radio";
			$radio->name = "example";
			$radio->value = "val-$i";
			array_push($radioElementList, $radio);
		}

		$sut = HTMLCollectionFactory::create(fn() => NodeListFactory::create(
			...$radioElementList
		));

		$radioNodeList = $sut->namedItem("example");
		$radioNodeList->value = "val-7";

		foreach($radioElementList as $i => $radio) {
			if($i === 7) {
				self::assertTrue($radio->checked);
			}
			else {
				self::assertFalse($radio->checked);
			}
		}
	}

	public function testNamedItemRadio_setValue_checkbox():void {
		$radioElementList = [];

		for($i = 0; $i < 10; $i++) {
			$radio = NodeTestFactory::createHTMLElement("input");
			$radio->type = "checkbox";
			$radio->name = "example";
			$radio->value = "val-$i";
			array_push($radioElementList, $radio);
		}

		$sut = HTMLCollectionFactory::create(fn() => NodeListFactory::create(
			...$radioElementList
		));

		$radioNodeList = $sut->namedItem("example");
		$radioNodeList->value = "val-7";

		foreach($radioElementList as $radio) {
			self::assertFalse($radio->checked);
		}
	}

	public function testOffsetExists():void {
		$sut = HTMLCollectionFactory::create(fn() => NodeListFactory::create(
			NodeTestFactory::createNode("example"),
			NodeTestFactory::createNode("example")
		));
		self::assertTrue(isset($sut[0]));
		self::assertTrue(isset($sut[1]));
		self::assertFalse(isset($sut[2]));
	}

	public function testOffsetSetImmutable():void {
		$sut = HTMLCollectionFactory::create(fn() => NodeListFactory::create(
			NodeTestFactory::createNode("example"),
			NodeTestFactory::createNode("example")
		));
		self::expectException(HTMLCollectionImmutableException::class);
		$sut[0] = "test";
	}

	public function testOffsetUnsetImmutable():void {
		$sut = HTMLCollectionFactory::create(fn() => NodeListFactory::create(
			NodeTestFactory::createNode("example"),
			NodeTestFactory::createNode("example")
		));
		self::expectException(HTMLCollectionImmutableException::class);
		unset($sut[0]);
	}

	public function testIterator():void {
		$sut = HTMLCollectionFactory::create(fn() => NodeListFactory::create(
			NodeTestFactory::createNode("example"),
			NodeTestFactory::createNode("example")
		));
		foreach($sut as $key => $value) {
			self::assertInstanceOf(Element::class, $value);
		}
		self::assertEquals(1, $key);
	}
}
