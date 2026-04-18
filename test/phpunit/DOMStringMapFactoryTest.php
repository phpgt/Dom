<?php
namespace GT\Dom\Test;

use GT\Dom\DOMStringMapFactory;
use GT\Dom\Element;
use GT\Dom\HTMLDocument;
use PHPUnit\Framework\TestCase;

class DOMStringMapFactoryTest extends TestCase {
	public function testCreateDatasetWithoutDataAttributes():void {
		$attributeArray = [
			"id" => "example",
			"class" => "phpunit",
		];
		$document = new HTMLDocument();
		$element = $document->createElement("example-element");
		foreach($attributeArray as $key => $value) {
			$element->setAttribute($key, $value);
		}

		$domStringMap = DOMStringMapFactory::createDataset($element);
		self::assertEmpty($domStringMap);
	}

	public function testCreateDatasetWithDataAttribute():void {
		$attributeArray = [
			"id" => "example",
			"data-language" => "php",
			"class" => "phpunit",
		];
		$document = new HTMLDocument();
		$element = $document->createElement("example-element");
		foreach($attributeArray as $key => $value) {
			$element->setAttribute($key, $value);
		}
		$domStringMap = DOMStringMapFactory::createDataset($element);
		self::assertNotEmpty($domStringMap);
		self::assertEquals("php", $domStringMap->language);
	}

	public function testSetCorrectsCamelCase():void {
		$document = new HTMLDocument();
		$element = $document->createElement("example-element");
		$domStringMap = DOMStringMapFactory::createDataset($element);
		self::assertCount(0, $element->attributes);
		$domStringMap->thisIsCamelCase = "test";
		self::assertCount(1, $element->attributes);
		$attribute = $element->attributes[0];
		self::assertSame("data-this-is-camel-case", $attribute->name);
		self::assertSame("test", $attribute->value);
		self::assertSame("test", $element->dataset->thisIsCamelCase);
	}

	public function testCreateDatasetMutate():void {
		$attributeArray = [
			"id" => "example",
			"data-language" => "php",
			"class" => "phpunit",
		];
		$document = new HTMLDocument();
		$element = $document->createElement("example-element");
		foreach($attributeArray as $key => $value) {
			$element->setAttribute($key, $value);
		}
		$domStringMap = DOMStringMapFactory::createDataset($element);
		$domStringMap->set("language", "python");
		self::assertEquals("python", $domStringMap->get("language"));
	}

	public function testCreateDatasetAccessWithoutMutate():void {
		$attributeArray = [
			"id" => "example",
			"data-language" => "php",
			"class" => "phpunit",
		];
		$document = new HTMLDocument();
		$element = $document->createElement("example-element");
		foreach($attributeArray as $key => $value) {
			$element->setAttribute($key, $value);
		}

		$domStringMap = DOMStringMapFactory::createDataset($element);
		self::assertSame("php", $domStringMap->get("language"));
		$domStringMap->set("language", "python");
		self::assertSame("python", $domStringMap->get("language"));
	}

	public function testSettingSameValue():void {
		$attributeArray = [
			"id" => "example",
			"data-language" => "php",
			"class" => "phpunit",
		];
		$document = new HTMLDocument();
		$element = $document->createElement("example-element");
		foreach($attributeArray as $key => $value) {
			$element->setAttribute($key, $value);
		}

		$domStringMap = DOMStringMapFactory::createDataset($element);
		$domStringMap->set("language", "php");
		self::assertSame("php", $domStringMap->get("language"));
	}
}
