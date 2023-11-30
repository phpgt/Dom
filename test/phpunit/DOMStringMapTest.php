<?php /** @noinspection PhpUndefinedFieldInspection */
namespace Gt\Dom\Test;

use Gt\Dom\DOMStringMap;
use PHPUnit\Framework\TestCase;

class DOMStringMapTest extends TestCase {
	public function testGetterSetter():void {
		$keyValuePairs = [];
		$getter = function() use (&$keyValuePairs) {
			return $keyValuePairs;
		};
		$setter = function(array $kvp) use (&$keyValuePairs) {
			$keyValuePairs = $kvp;
		};
		$sut = new DOMStringMap($getter, $setter);
		$sut->example = "example123";
		self::assertNotNull($sut->example);
		self::assertSame($keyValuePairs["example"], $sut->example);
	}

	public function testGetterCamelCaseConversion():void {
		$keyValuePairs = [
			"this-is-camel-case" => uniqid("example-"),
		];
		$getter = function() use (&$keyValuePairs) {
			return $keyValuePairs;
		};
		$setter = function(array $kvp) use (&$keyValuePairs) {
			$keyValuePairs = $kvp;
		};
		$sut = new DOMStringMap($getter, $setter);

		self::assertSame($keyValuePairs["this-is-camel-case"], $sut->thisIsCamelCase);
	}

	public function testSetterCamelCaseConversion():void {
		$keyValuePairs = [];
		$getter = function() use (&$keyValuePairs) {
			return $keyValuePairs;
		};
		$setter = function(array $kvp) use (&$keyValuePairs) {
			$keyValuePairs = $kvp;
		};
		$sut = new DOMStringMap($getter, $setter);
		$sut->thisIsCamelCase = "example123";
		self::assertSame("example123", $sut->get("thisIsCamelCase"));
		self::assertSame("example123", $sut->get("this-is-camel-case"));
	}

	public function testSetter_withNumbers():void {
		$keyValuePairs = [];
		$getter = function() use (&$keyValuePairs) {
			return $keyValuePairs;
		};
		$setter = function(array $kvp) use (&$keyValuePairs) {
			$keyValuePairs = $kvp;
		};
		$sut = new DOMStringMap($getter, $setter);
		$sut->example1 = "one";
		$sut->example2 = "two";
		$sut->example3 = "three";

		self::assertSame("one", $sut->example1);
		self::assertSame("two", $sut->example2);
		self::assertSame("three", $sut->example3);
	}
}
