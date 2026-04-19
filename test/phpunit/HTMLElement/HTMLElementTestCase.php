<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\Element;
use PHPUnit\Framework\TestCase;

abstract class HTMLElementTestCase extends TestCase {
	protected function assertPropertyAttributeCorrelate(
		Element $element,
		string $attribute,
		?string $property = null
	):void {
		if(is_null($property)) {
			$property = $attribute;
		}

		self::assertEquals(
			"",
			$element->getAttribute($attribute)
		);
		self::assertEquals(
			$element->$property,
			$element->getAttribute($attribute)
		);

		$value = uniqid();
		$element->setAttribute($attribute, $value);
		self::assertEquals(
			$value,
			$element->getAttribute($attribute)
		);
		self::assertEquals(
			$element->$property,
			$element->getAttribute($attribute)
		);

		$value2 = uniqid();
		$element->$property = $value2;
		self::assertEquals(
			$value2,
			$element->getAttribute($attribute)
		);
		self::assertEquals(
			$element->$property,
			$element->getAttribute($attribute)
		);
	}

	protected static function assertPropertyAttributeCorrelateBool(
		Element $sut,
		string $attribute,
		?string $property = null
	):void {
		if(is_null($property)) {
			$property = $attribute;
		}

		$current = $sut->$property;
		self::assertIsBool($current);

		$sut->$property = !$current;
		self::assertSame(!$current, $sut->$property);

		if($current) {
			self::assertFalse($sut->hasAttribute($attribute));
		}
		else {
			self::assertTrue($sut->hasAttribute($attribute));
		}

		$sut->$property = $current;
		self::assertSame($current, $sut->$property);

		if($current) {
			self::assertTrue($sut->hasAttribute($attribute));
		}
		else {
			self::assertFalse($sut->hasAttribute($attribute));
		}
	}

	/**
	 * @param string $type "int" or "float", prefixed with "?" to indicate
	 * nullable, suffixed with ":" and then a default value if the non-set
	 * value is not null-ish.
	 */
	protected static function assertPropertyAttributeCorrelateNumber(
		Element $sut,
		string $type,
		string $attribute,
		?string $property = null
	):void {
		if(is_null($property)) {
			$property = $attribute;
		}

		$current = $sut->$property;

		$attributeValue = $sut->getAttribute("attribute");

		if(str_contains($type, ":")) {
			$default = substr($type, strpos($type, ":") + 1);
			self::assertEquals($current, $default);
		}
		else {
			self::assertEquals($current, $attributeValue);
		}

		if($type[0] !== "?") {
			self::assertNotNull($current);
		}

		for($i = 0; $i < 10; $i++) {
			$value = rand(1, 999999);
			if(str_contains($type, "float")) {
				$value /= (rand(9, 99) / 10);
			}

			$sut->setAttribute($attribute, (string)$value);
			$attributeValue = $sut->getAttribute($attribute);
			self::assertEquals($sut->$property, $attributeValue);

			if(!$type[0] != "?") {
				self::assertNotNull($attributeValue);
			}
		}

		for($i = 0; $i < 10; $i++) {
			$value = rand(1, 999999);
			if(str_contains($type, "float")) {
				$value /= (rand(9, 99) / 10);
			}

			$sut->$property = $value;
			$propertyValue = $sut->$property;
			self::assertEquals($sut->getAttribute($attribute), $propertyValue);

			if(!$type[0] != "?") {
				self::assertNotNull($propertyValue);
			}
		}
	}
}
