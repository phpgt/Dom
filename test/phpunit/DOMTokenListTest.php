<?php
namespace GT\Dom\Test;

use GT\Dom\DOMTokenListFactory;
use PHPUnit\Framework\TestCase;

class DOMTokenListTest extends TestCase {
	public function testLength():void {
		$data = ["one", "two", "three"];
		$sut = DOMTokenListFactory::create(fn() => $data, fn() => null);
		self::assertEquals(
			count($data),
			$sut->length
		);
	}

	public function testCount():void {
		$data = ["one", "two", "three"];
		$sut = DOMTokenListFactory::create(fn() => $data, fn() => null);
		self::assertCount(
			count($data),
			$sut
		);
	}

	public function testValueAccessor():void {
		$data = ["one", "two", "three"];
		$sut = DOMTokenListFactory::create(fn() => $data, fn() => null);
		self::assertEquals(
			implode(" ", $data),
			$sut->value
		);
	}

	public function testValueMutator():void {
		$data = ["one", "two", "three"];
		$sut = DOMTokenListFactory::create(fn() => $data, fn() => null);
		$sut->value = "four five six";
		self::assertEquals(
			"four five six",
			$sut->value
		);
	}

	public function testItem():void {
		$data = ["one", "two", "three"];
		$sut = DOMTokenListFactory::create(fn() => $data, fn() => null);
		for($i = 0; $i < count($data); $i++) {
			self::assertEquals(
				$data[$i],
				$sut->item($i)
			);
		}
	}

	public function testContains():void {
		$data = ["one", "two", "three"];
		$sut = DOMTokenListFactory::create(fn() => $data, fn() => null);
		self::assertTrue($sut->contains("one"));
		self::assertTrue($sut->contains("two"));
		self::assertTrue($sut->contains("three"));
		self::assertFalse($sut->contains("four"));
	}

	public function testAdd():void {
		$data = ["one", "two", "three"];
		$sut = DOMTokenListFactory::create(
			function() use(&$data) {
				return $data;
			},
			function(...$new)use(&$data) {
				$data = $new;
			}
		);
		$sut->add("four");
		self::assertTrue($sut->contains("one"));
		self::assertTrue($sut->contains("two"));
		self::assertTrue($sut->contains("three"));
		self::assertTrue($sut->contains("four"));
	}

	public function testAddAgain():void {
		$data = ["one", "two", "three"];
		$sut = DOMTokenListFactory::create(
			function() use(&$data) {
				return $data;
			},
			function(...$new)use(&$data) {
				$data = $new;
			}
		);
		$sut->add("four");
		$sut->add("four");
		$sut->add("four");
		self::assertEquals("one two three four", $sut->value);
	}

	public function testAddMultiple():void {
		$data = ["one", "two", "three"];
		$sut = DOMTokenListFactory::create(
			function() use(&$data) {
				return $data;
			},
			function(...$new)use(&$data) {
				$data = $new;
			}
		);
		$sut->add("four", "five", "six");
		self::assertTrue($sut->contains("one"));
		self::assertTrue($sut->contains("two"));
		self::assertTrue($sut->contains("three"));
		self::assertTrue($sut->contains("four"));
		self::assertTrue($sut->contains("five"));
		self::assertTrue($sut->contains("six"));
	}

	public function testRemove():void {
		$data = ["one", "two", "three"];
		$sut = DOMTokenListFactory::create(fn() => $data, fn() => null);
		$sut->remove("two");
		$sut->remove("five");
		self::assertTrue($sut->contains("one"));
		self::assertFalse($sut->contains("two"));
		self::assertTrue($sut->contains("three"));
		self::assertFalse($sut->contains("five"));
	}

	public function testRemoveMultiple():void {
		$data = ["one", "two", "three"];
		$sut = DOMTokenListFactory::create(fn() => $data, fn() => null);
		$sut->remove("two", "three");
		self::assertTrue($sut->contains("one"));
		self::assertFalse($sut->contains("two"));
		self::assertFalse($sut->contains("three"));
	}

	public function testReplace():void {
		$data = ["one", "two", "three"];
		$sut = DOMTokenListFactory::create(fn() => $data, fn() => null);

		self::assertTrue(
			$sut->replace("two", "updated")
		);
		self::assertFalse(
			$sut->replace("not-there", "updated")
		);
		self::assertTrue($sut->contains("one"));
		self::assertFalse($sut->contains("two"));
		self::assertTrue($sut->contains("three"));
		self::assertTrue($sut->contains("updated"));
	}

	public function testToggle():void {
		$data = ["one", "two", "three"];
		$sut = DOMTokenListFactory::create(
			function() use(&$data) {
				return $data;
			},
			function(...$new)use(&$data) {
				$data = $new;
			}
		);

		self::assertTrue(
			$sut->toggle("four")
		);
		self::assertFalse(
			$sut->toggle("two")
		);
		self::assertTrue($sut->contains("one"));
		self::assertFalse($sut->contains("two"));
		self::assertTrue($sut->contains("three"));
		self::assertTrue($sut->contains("four"));
	}

	public function testToggleForce():void {
		$data = ["one", "two", "three"];
		$sut = DOMTokenListFactory::create(fn() => $data, fn() => null);

		self::assertTrue(
			$sut->toggle("one", true)
		);
		self::assertTrue($sut->contains("one"));

		self::assertFalse(
			$sut->toggle("four", false)
		);
		self::assertFalse($sut->contains("four"));
	}

	public function testEntries():void {
		$data = ["one", "two", "three"];
		$sut = DOMTokenListFactory::create(fn() => $data, fn() => null);
		foreach($sut->entries() as $key => $value) {
			self::assertEquals($data[$key], $value);
		}

		/** @noinspection PhpUndefinedVariableInspection */
		self::assertGreaterThan(0, $key);
	}

	public function testForEach():void {
		$data = ["one", "two", "three"];
		$received = [];

		$sut = DOMTokenListFactory::create(fn() => $data, fn() => null);
		$sut->forEach(function(string $token) use(&$received) {
			array_push($received, $token);
		});

		self::assertSame($data, $received);
	}

	public function testKeys():void {
		$data = ["one", "two", "three"];
		$received = [];
		$sut = DOMTokenListFactory::create(fn() => $data, fn() => null);
		foreach($sut->keys() as $key) {
			array_push($received, $key);
		}
		self::assertSame([0, 1, 2], $received);
	}

	public function testValues():void {
		$data = ["one", "two", "three"];
		$received = [];
		$sut = DOMTokenListFactory::create(fn() => $data, fn() => null);
		foreach($sut->values() as $value) {
			array_push($received, $value);
		}
		self::assertSame($data, $received);
	}

	public function testAdd_shouldCallMutator():void {
		$mutatorCalls = 0;
		$data = ["one", "two", "three"];
		$accessor = function() use($data) {
			return $data;
		};
		$mutator = function(string...$tokens) use(&$data, &$mutatorCalls) {
			$data = $tokens;
			$mutatorCalls++;
		};

		$sut = DOMTokenListFactory::create($accessor, $mutator);
		$sut->add("four");
		$sut->add("five");
		$sut->add("six");
		self::assertSame(3, $mutatorCalls);
	}

	public function testRemove_shouldCallMutator():void {
		$mutatorCalls = 0;
		$data = ["one", "two", "three"];
		$accessor = function() use($data) {
			return $data;
		};
		$mutator = function(string...$tokens) use(&$data, &$mutatorCalls) {
			$data = $tokens;
			$mutatorCalls++;
		};

		$sut = DOMTokenListFactory::create($accessor, $mutator);
		$sut->remove("three");
		$sut->add("two");
		self::assertSame(2, $mutatorCalls);
	}

	public function testReplace_shouldCallMutator():void {
		$mutatorCalls = 0;
		$data = ["one", "two", "three"];
		$accessor = function() use($data) {
			return $data;
		};
		$mutator = function(string...$tokens) use(&$data, &$mutatorCalls) {
			$data = $tokens;
			$mutatorCalls++;
		};

		$sut = DOMTokenListFactory::create($accessor, $mutator);
		$sut->replace("three", "THREE");
		$sut->replace("one", "ONE");
		self::assertSame(2, $mutatorCalls);
	}

	public function testToggle_shouldCallMutator():void {
		$mutatorCalls = 0;
		$data = ["one", "two", "three"];
		$accessor = function() use($data) {
			return $data;
		};
		$mutator = function(string...$tokens) use(&$data, &$mutatorCalls) {
			$data = $tokens;
			$mutatorCalls++;
		};

		$sut = DOMTokenListFactory::create($accessor, $mutator);
		$sut->toggle("three");
		$sut->toggle("one");
		$sut->toggle("three");
		self::assertSame(3, $mutatorCalls);
	}
}
