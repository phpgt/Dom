<?php
namespace GT\Dom\Test;

use GT\Dom\HTMLDocument;
use PHPUnit\Framework\TestCase;

class CharacterDataTest extends TestCase {
	public function testData():void {
		$sut = (new HTMLDocument())->createTextNode("");
		self::assertEquals("", $sut->data);
		$sut->data = "example";
		self::assertEquals("example", $sut->data);
	}

	public function testLength():void {
		$message = "example message";
		$sut = (new HTMLDocument())->createTextNode($message);
		self::assertEquals(strlen($message), $sut->length);
	}

	public function testAppendData():void {
		$message1 = "Hello ";
		$message2 = "DOM";
		$sut = (new HTMLDocument())->createTextNode($message1);
		$sut->appendData($message2);
		self::assertEquals(
			$message1 . $message2,
			$sut->data
		);
	}

	public function testDeleteData():void {
		$message = "abcdefghijklmnopqurstvwxyz";
		$sut = (new HTMLDocument())->createTextNode($message);
		$sut->deleteData(4, 6);
		self::assertEquals(
			substr($message, 0, 4)
			. substr($message, 4 + 6),
			$sut->data
		);
	}

	public function testInsertData():void {
		$message = "abcdefghijklmnopqurstvwxyz";
		$sut = (new HTMLDocument())->createTextNode($message);
		$sut->insertData(4, "INSERTED");
		self::assertEquals(
			"abcdINSERTEDefghijklmnopqurstvwxyz",
			$sut->data
		);
	}

	public function testReplaceData():void {
		$message = "abcdefghijklmnopqurstvwxyz";
		$sut = (new HTMLDocument())->createTextNode($message);
		$sut->replaceData(4, 6, "INSERTED");
		self::assertEquals(
			"abcdINSERTEDklmnopqurstvwxyz",
			$sut->data
		);
	}

	public function testSubstringData():void {
		$message = "abcdefghijklmnopqurstvwxyz";
		$sut = (new HTMLDocument())->createTextNode($message);
		self::assertEquals(
			"efghij",
			$sut->substringData(4, 6)
		);
	}
}
