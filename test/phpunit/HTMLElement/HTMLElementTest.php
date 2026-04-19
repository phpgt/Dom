<?php
namespace GT\Dom\Test\HTMLElement;

use GT\Dom\ClientSide\CSSStyleDeclaration;
use GT\Dom\Exception\ClientSideOnlyFunctionalityException;
use GT\Dom\Exception\EnumeratedValueException;
use GT\Dom\Exception\IncorrectHTMLElementUsageException;
use GT\Dom\HTMLDocument;
use PHPUnit\Framework\TestCase;

class HTMLElementTest extends HTMLElementTestCase {
	public function testAccessKeyNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		self::assertEquals("", $sut->accessKey);
	}

	public function testAccessKeySetGet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$sut->accessKey = "a";
		self::assertEquals("a", $sut->accessKey);
		self::assertEquals(
			"a",
			$sut->getAttribute("accesskey")
		);
	}

	public function testAccessKeyLabelThrows():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		$test = $sut->accessKeyLabel;
	}

	public function testContentEditableInherit():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		self::assertEquals("inherit", $sut->contentEditable);
	}

	public function testContentEditable():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$sut->contentEditable = "true";
		self::assertEquals("true", $sut->contentEditable);
		$sut->contentEditable = "false";
		self::assertEquals("false", $sut->contentEditable);
		$sut->contentEditable = "inherit";
		self::assertEquals("inherit", $sut->contentEditable);
	}

	public function testContentEditableEnum():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		self::expectException(EnumeratedValueException::class);
		$sut->contentEditable = "truthy";
	}

	public function testIsContentEditableDefault():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		self::assertFalse($sut->isContentEditable);
	}

	public function testIsContentEditableTrueFalse():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$sut->contentEditable = "true";
		self::assertTrue($sut->isContentEditable);
		$sut->contentEditable = "false";
		self::assertFalse($sut->isContentEditable);
	}

	public function testIsContentEditableInheritNoParent():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$sut->contentEditable = "inherit";
		self::assertFalse($sut->isContentEditable);
	}

	public function testIsContentEditableInheritParentWithFalse():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$sut->contentEditable = "inherit";
		$falseContentEditable = $sut->ownerDocument->createElement("div");
		$falseContentEditable->contentEditable = "false";
		$falseContentEditable->appendChild($sut);
		self::assertFalse($sut->isContentEditable);
	}

	public function testIsContentEditableInheritParentWithTrue():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$sut->contentEditable = "inherit";
		$falseContentEditable = $sut->ownerDocument->createElement("div");
		$falseContentEditable->contentEditable = "true";
		$falseContentEditable->appendChild($sut);
		self::assertTrue($sut->isContentEditable);
	}

	public function testIsContentEditableInheritAncestor():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$sut->contentEditable = "inherit";
		$falseContentEditable = $sut->ownerDocument->createElement("div");
		$falseContentEditable->contentEditable = "true";
		$child1 = $sut->ownerDocument->createElement("div");
		$child2 = $sut->ownerDocument->createElement("div");
		$falseContentEditable->appendChild($child1);
		$child1->appendChild($child2);
		$child2->appendChild($sut);
		self::assertTrue($sut->isContentEditable);
	}

	public function testDirNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		self::assertEquals("", $sut->dir);
	}

	public function testDirGetSet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$sut->dir = "ltr";
		self::assertEquals("ltr", $sut->dir);
	}

	public function testDraggableNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		self::assertFalse($sut->draggable);
	}

	public function testDraggable():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$sut->draggable = true;
		self::assertTrue($sut->draggable);
		self::assertEquals("true", $sut->getAttribute("draggable"));
		$sut->draggable = false;
		self::assertFalse($sut->draggable);
		self::assertEquals("false", $sut->getAttribute("draggable"));
	}

	public function testEnterKeyHintNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		self::assertEquals("", $sut->enterKeyHint);
	}

	public function testEnterKeyHint():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$sut->enterKeyHint = "go";
		self::assertEquals("go", $sut->enterKeyHint);
		self::assertEquals("go", $sut->getAttribute("enterkeyhint"));
		$sut->enterKeyHint = "next";
		self::assertEquals("next", $sut->enterKeyHint);
		self::assertEquals("next", $sut->getAttribute("enterkeyhint"));
	}

	public function testEnterKeyHintBadEnum():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		self::expectException(EnumeratedValueException::class);
		$sut->enterKeyHint = "lalala";
	}

	public function testHiddenDefault():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		self::assertFalse($sut->hidden);
	}

	public function testHidden():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$sut->hidden = true;
		self::assertTrue($sut->hidden);
		self::assertTrue($sut->hasAttribute("hidden"));
		self::assertEquals("", $sut->getAttribute("hidden"));
		$sut->hidden = false;
		self::assertFalse($sut->hidden);
		self::assertFalse($sut->hasAttribute("hidden"));
	}

	public function testInertDefault():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		self::assertFalse($sut->inert);
	}

	public function testInert():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$sut->inert = true;
		self::assertTrue($sut->inert);
		self::assertTrue($sut->hasAttribute("inert"));
		self::assertEquals("", $sut->getAttribute("inert"));
		$sut->inert = false;
		self::assertFalse($sut->inert);
		self::assertFalse($sut->hasAttribute("inert"));
	}

	public function testInnerTextEmpty():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		self::assertEquals("", $sut->innerText);
	}

	public function testInnerText():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$sut->innerText = "Hello, PHPUnit!";
		self::assertEquals("Hello, PHPUnit!", $sut->innerText);
	}

	public function testInnerTextRemovesAllChildren():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$sut->append(
			$sut->ownerDocument->createElement("child"),
			$sut->ownerDocument->createElement("child"),
			$sut->ownerDocument->createElement("child")
		);
		$sut->innerText = "Hello, PHPUnit!";
		self::assertCount(1, $sut->childNodes);
	}

	public function testInnerTextDoesNotShowHidden():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$child1 = $sut->ownerDocument->createElement("child");
		$child1->textContent = "Child 1";
		$child2 = $sut->ownerDocument->createElement("child");
		$child2->textContent = "Child 2";
		$child3 = $sut->ownerDocument->createElement("child");
		$child3->textContent = "Child 3";
		$sut->append($child1, $child2, $child3);

		$child2->hidden = true;
		self::assertEquals("Child 1Child 3", $sut->innerText);
	}

	public function testLangDefault():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		self::assertEquals("", $sut->lang);
	}

	public function testLang():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$sut->lang = "en";
		self::assertEquals("en", $sut->lang);
		self::assertEquals("en", $sut->getAttribute("lang"));
	}

	public function testTabIndexDefault():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		self::assertEquals(-1, $sut->tabIndex);
	}

	public function testTabIndex():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$sut->tabIndex = 1;
		self::assertEquals(1, $sut->tabIndex);
		self::assertEquals("1", $sut->getAttribute("tabindex"));
		$sut->tabIndex = 10;
		self::assertEquals("10", $sut->getAttribute("tabindex"));
		self::assertEquals(10, $sut->tabIndex);
	}

	public function testTitleDefault():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		self::assertEquals("", $sut->title);
	}

	public function testTitle():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$sut->title = "Example";
		self::assertEquals("Example", $sut->title);
		self::assertEquals("Example", $sut->getAttribute("title"));
	}

	public function testStyleGet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		self::expectException(ClientSideOnlyFunctionalityException::class);
		/** @noinspection PhpUnusedLocalVariableInspection */
		$value = $sut->style;
	}

	public function testStyleSet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$style = self::createMock(CSSStyleDeclaration::class);
		self::expectException(ClientSideOnlyFunctionalityException::class);
		$sut->style = $style;
	}

	public function testDatasetEmpty():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		self::assertEmpty($sut->dataset);
		self::assertCount(0, $sut->dataset);
		self::assertNull($sut->dataset->get("nothing"));
	}

	public function testDatasetSetsDataAttribute():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		$sut->dataset->set("example", "something");
		self::assertEquals("something", $sut->getAttribute("data-example"));
	}

	public function testPropertyNotAvailable():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("div");
		self::expectException(IncorrectHTMLElementUsageException::class);
		$sut->disabled = true;
	}

	public function testToString():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example-element");
		$sut->textContent = uniqid();
		self::assertSame("", (string)$sut);
	}

	public function testValue_textarea():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("textarea");
		$sut->value = "example123";
		self::assertSame("<textarea>example123</textarea>", $sut->outerHTML);
		self::assertFalse($sut->hasAttribute("value"));

		$sut->innerHTML = "changed value";
		self::assertSame("changed value", $sut->value);
	}
}
