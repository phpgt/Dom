<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\Exception\ArrayAccessReadOnlyException;
use GT\Dom\HTMLDocument;
use GT\Dom\HTMLFormControlsCollection;

class HTMLFormElementTest extends HTMLElementTestCase {
	public function testElementsEmpty():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("form");
		$elements = $sut->elements;
		self::assertInstanceOf(HTMLFormControlsCollection::class, $elements);
		self::assertCount(0, $elements);
	}

	public function testElements():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("form");
		$elements = $sut->elements;
		$button = $sut->ownerDocument->createElement("button");
		$input = $sut->ownerDocument->createElement("input");
		$div = $sut->ownerDocument->createElement("div");
		$select = $sut->ownerDocument->createElement("select");
		$sut->append($button, $input, $div, $select);
		self::assertCount(3, $elements);
	}

	public function testLengthEmpty():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("form");
		self::assertEquals(0, $sut->length);
		self::assertCount(0, $sut);
	}

	public function testName():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("form");
		self::assertPropertyAttributeCorrelate($sut, "name");
	}

	public function testMethod():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("form");
		self::assertPropertyAttributeCorrelate($sut, "method");
	}

	public function testTarget():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("form");
		self::assertPropertyAttributeCorrelate($sut, "target");
	}

	public function testAction():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("form");
		self::assertPropertyAttributeCorrelate($sut, "action");
	}

	public function testEncoding():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("form");
		self::assertPropertyAttributeCorrelate($sut, "enctype", "encoding");
		$sut->enctype = "";
		self::assertPropertyAttributeCorrelate($sut, "enctype");
	}

	public function testEncodingEnctypeCorrelate():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("form");
		$sut->setAttribute("enctype", "test-encoding");
		self::assertEquals("test-encoding", $sut->encoding);
	}

	public function testAcceptCharset():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("form");
		self::assertPropertyAttributeCorrelate($sut, "accept-charset", "acceptCharset");
	}

	public function testAutocomplete():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("form");
		self::assertPropertyAttributeCorrelate($sut, "autocomplete");
	}

	public function testNoValidate():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("form");
		self::assertPropertyAttributeCorrelateBool($sut, "novalidate", "noValidate");
	}

	public function testArrayAccess():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("form");
		self::assertFalse(isset($sut["nothing"]));

		$input = $sut->ownerDocument->createElement("input");
		$input->name = "example";
		$textarea = $sut->ownerDocument->createElement("textarea");
		$textarea->name = "another-example";
		$sut->append($input, $textarea);
		self::assertSame($textarea, $sut["another-example"]);
	}

	public function testArrayAccessSet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("form");
		self::expectException(ArrayAccessReadOnlyException::class);
		$sut["nothing"] = "something";
	}

	public function testArrayAccessUnset():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("form");
		self::expectException(ArrayAccessReadOnlyException::class);
		unset($sut["nothing"]);
	}
}
