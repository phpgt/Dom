<?php
namespace GT\Dom\Test;

use GT\Dom\Exception\NodeListImmutableException;
use GT\Dom\Node;
use GT\Dom\NodeListFactory;
use PHPUnit\Framework\TestCase;

class NodeListTest extends TestCase {
	public function testLengthEmpty():void {
		$sut = NodeListFactory::create();
		self::assertEquals(0, $sut->length);
		self::assertCount(0, $sut);
	}

	public function testLength():void {
		$node1 = self::createMock(Node::class);
		$node2 = self::createMock(Node::class);
		$node3 = self::createMock(Node::class);
		$sut = NodeListFactory::create($node1, $node2, $node3);
		self::assertEquals(3, $sut->length);
	}

	public function testLengthLive():void {
		$nodeArray = [];
		$sut = NodeListFactory::createLive(function() use(&$nodeArray) {
			return $nodeArray;
		});
		self::assertEquals(0, $sut->length);
		array_push($nodeArray, self::createMock(Node::class));
		self::assertEquals(1, $sut->length);
	}

	public function testItemEmpty():void {
		$sut = NodeListFactory::create();
		self::assertNull($sut->item(0));
		self::assertNull($sut->item(1));
		self::assertNull($sut->item(2));
	}

	public function testItem():void {
		$node1 = self::createMock(Node::class);
		$node2 = self::createMock(Node::class);
		$node3 = self::createMock(Node::class);
		$sut = NodeListFactory::create($node1, $node2, $node3);
		self::assertSame($node1, $sut->item(0));
		self::assertSame($node3, $sut->item(2));
		self::assertSame($node2, $sut->item(1));
		self::assertNull($sut->item(3));
	}

	public function testItemLive():void {
		$node1 = self::createMock(Node::class);
		$node2 = self::createMock(Node::class);
		$node3 = self::createMock(Node::class);
		$nodeArray = [$node1, $node2, $node3];
		$sut = NodeListFactory::createLive(function() use(&$nodeArray) {
			return array_values($nodeArray);
		});

		while($node = $sut->item(0)) {
			self::assertEquals($nodeArray[0], $node);
			array_shift($nodeArray);
		}
	}

	public function testEntriesEmpty():void {
		$sut = NodeListFactory::create();
		$iterable = $sut->entries();
		self::assertEmpty(iterator_to_array($iterable));
	}

	public function testEntries():void {
		$node1 = self::createMock(Node::class);
		$node2 = self::createMock(Node::class);
		$node3 = self::createMock(Node::class);
		$sut = NodeListFactory::create($node1, $node2, $node3);
		$array = iterator_to_array($sut->entries());
		self::assertCount(3, $array);
		self::assertSame($node1, $array[0]);
		self::assertSame($node2, $array[1]);
		self::assertSame($node3, $array[2]);
	}

	public function testForEachEmpty():void {
		$called = [];
		$sut = NodeListFactory::create();
		$sut->forEach(function(Node $node) use(&$called) {
			array_push($called, $node);
		});
		self::assertEmpty($called);
	}

	public function testForEach():void {
		$called = [];
		$node1 = self::createMock(Node::class);
		$node2 = self::createMock(Node::class);
		$node3 = self::createMock(Node::class);
		$sut = NodeListFactory::create($node1, $node2, $node3);
		$sut->forEach(function(Node $node) use(&$called) {
			array_push($called, $node);
		});
		self::assertCount(3, $called);
		self::assertSame($node1, $called[0]);
		self::assertSame($node2, $called[1]);
		self::assertSame($node3, $called[2]);
	}

	public function testKeysEmpty():void {
		$sut = NodeListFactory::create();
		$iterable = $sut->keys();
		self::assertEmpty(iterator_to_array($iterable));
	}

	public function testKeys():void {
		$node1 = self::createMock(Node::class);
		$node2 = self::createMock(Node::class);
		$node3 = self::createMock(Node::class);
		$sut = NodeListFactory::create($node1, $node2, $node3);
		$array = iterator_to_array($sut->keys());
		self::assertCount(3, $array);
		self::assertSame(0, $array[0]);
		self::assertSame(1, $array[1]);
		self::assertSame(2, $array[2]);
	}

	public function testValuesEmpty():void {
		$sut = NodeListFactory::create();
		$iterable = $sut->values();
		self::assertEmpty(iterator_to_array($iterable));
	}

	public function testValues():void {
		$node1 = self::createMock(Node::class);
		$node2 = self::createMock(Node::class);
		$node3 = self::createMock(Node::class);
		$sut = NodeListFactory::create($node1, $node2, $node3);
		$array = iterator_to_array($sut->values());
		self::assertCount(3, $array);
		self::assertSame($node1, $array[0]);
		self::assertSame($node2, $array[1]);
		self::assertSame($node3, $array[2]);
	}

	public function testOffsetExists():void {
		$node1 = self::createMock(Node::class);
		$node2 = self::createMock(Node::class);
		$node3 = self::createMock(Node::class);
		$sut = NodeListFactory::create($node1, $node2, $node3);
		self::assertTrue(isset($sut[0]));
		self::assertTrue(isset($sut[1]));
		self::assertTrue(isset($sut[2]));
		self::assertFalse(isset($sut[3]));
	}

	public function testOffsetExistsLive():void {
		$nodeArray = [
			self::createMock(Node::class)
		];

		$sut = NodeListFactory::createLive(function() use(&$nodeArray) {
			return $nodeArray;
		});
		self::assertTrue(isset($sut[0]));
		$nodeArray = [];
		self::assertFalse(isset($sut[0]));

	}

	public function testOffsetSet():void {
		$sut = NodeListFactory::create();
		self::expectException(NodeListImmutableException::class);
		$sut[0] = self::createMock(Node::class);
	}

	public function testOffsetUnset():void {
		$sut = NodeListFactory::create();
		self::expectException(NodeListImmutableException::class);
		unset($sut[0]);
	}
}
