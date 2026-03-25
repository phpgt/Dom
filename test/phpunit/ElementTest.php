<?php
namespace Gt\Dom\Test;

use Gt\Dom\Exception\InvalidAdjacentPositionException;
use Gt\Dom\Exception\InvalidCharacterException;
use Gt\Dom\HTMLDocument;
use Gt\Dom\Test\TestFactory\DocumentTestFactory;
use Gt\Dom\Test\TestFactory\NodeTestFactory;
use Gt\Dom\Text;
use Gt\Dom\XMLDocument;
use PHPUnit\Framework\TestCase;

class ElementTest extends TestCase {
	public function testIsEqualNodeDifferentTagName():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$other = $sut->ownerDocument->createElement("different");
		self::assertFalse($sut->isEqualNode($other));
	}

	public function testIsEqualNodeDifferentAttributeLength():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$other = $sut->cloneNode();
		$other->setAttribute("name", "unit-test");
		self::assertFalse($sut->isEqualNode($other));
	}

	public function testIsEqualNodeDifferentAttributeContent():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$other = $sut->cloneNode();
		$other->setAttribute("name", "unit-test");
		$sut->setAttribute("something", "other");
		self::assertFalse($sut->isEqualNode($other));
	}

	public function testIsEqualDifferentChildrenEqualContent():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$other = $sut->cloneNode();
		$child1 = $sut->ownerDocument->createElement("child");
		$child2 = $sut->ownerDocument->createElement("child");
		$sut->appendChild($child1);
		$other->appendChild($child2);
		self::assertTrue($sut->isEqualNode($other));
	}

	public function testIsEqualDifferentChildrenDifferentContent():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$other = $sut->cloneNode();
		$child1 = $sut->ownerDocument->createElement("child");
		$child1->innerHTML = "<p>Child 1</p>";
		$child2 = $sut->ownerDocument->createElement("child");
		$child2->innerHTML = "<p>Child 2</p>";
		$sut->appendChild($child1);
		$other->appendChild($child2);
		self::assertFalse($sut->isEqualNode($other));
	}

	public function testAttributesLive():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$attributes = $sut->attributes;
		self::assertCount(0, $attributes);
		$sut->setAttribute("name", "unti-test");
		self::assertCount(1, $attributes);
		$sut->setAttribute("framework", "PHPUnit");
		self::assertCount(2, $attributes);
		$sut->setAttribute("name", "unit-test");
		self::assertCount(2, $attributes);
	}

	public function testClassList():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$classList = $sut->classList;
		self::assertFalse($classList->contains("a-class"));
		$sut->className = "something another-thing a-class final-class";
		self::assertTrue($classList->contains("a-class"));
	}

	public function testClassListMutate():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$classList = $sut->classList;
		self::assertFalse($classList->contains("a-class"));
		$sut->className = "something another-thing a-class final-class";
		$classList->value = "totally different class-list";
		self::assertFalse($classList->contains("something"));
		self::assertFalse($classList->contains("another-thing"));
		self::assertFalse($classList->contains("a-class"));
		self::assertFalse($classList->contains("final-class"));
	}

	public function testClassListMutateUpdatesElement():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$classList = $sut->classList;
		self::assertFalse($classList->contains("a-class"));
		$sut->className = "something another-thing a-class final-class";
		$classList->value = "updated";

		self::assertFalse($classList->contains("something"));
		self::assertFalse($sut->classList->contains("something"));

		self::assertTrue($classList->contains("updated"));
		self::assertTrue($sut->classList->contains("updated"));

		self::assertEquals("updated", $sut->className);
	}

	public function testClassName():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$sut->className = "something another-thing a-class final-class";
		self::assertEquals(
			$sut->className,
			$sut->getAttribute("class")
		);
	}

	public function testId():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$sut->id = "something";
		self::assertEquals(
			$sut->id,
			$sut->getAttribute("id")
		);
	}

	public function testInnerHTML():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$sut->innerHTML = "<p>A paragraph</p> <div>A div</div>";
		$innerHTML0 = $sut->children[0]->innerHTML;
		self::assertEquals("A paragraph", $innerHTML0);
		$innerHTML1 = $sut->children[1]->innerHTML;
		self::assertEquals("A div", $innerHTML1);
	}

	public function testInnerHTML_unicode():void {
		$document = new HTMLDocument();
		// Note the special apostrophe.
		$message = "Let’s go on a digital journey together.";
		$sut = $document->createElement("example");
		$sut->innerHTML = $message;
		self::assertEquals($message, $sut->innerHTML);
	}

	public function testInnerHTML_emoji():void {
		$document = new HTMLDocument();
		$message = "I ❤️ my 🐈";
		$sut = $document->createElement("example");
		$sut->innerHTML = $message;
		self::assertEquals($message, $sut->innerHTML);
	}

	public function testInnerHTMLReset():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$sut->innerHTML = "<p>A paragraph</p>
		<div>A div</div>";
		$sut->innerHTML = "<example>An example</example><another-example>And another</another-example>";
		self::assertEquals("An example", $sut->children[0]->innerHTML);
		self::assertEquals("And another", $sut->children[1]->innerHTML);
	}

	public function testInnerText():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("span");
		$sut->innerText = "Hello, World!";
		self::assertSame($sut->innerText, $sut->innerHTML);
	}

	public function testInnerText_containsHTML():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("span");
		$textWithHTML = "Hello, <b>World</b>!";
		$sut->innerText = $textWithHTML;
		self::assertSame($textWithHTML, $sut->innerText);
		self::assertSame("Hello, &lt;b&gt;World&lt;/b&gt;!", $sut->innerHTML);
	}

	public function testTextContent():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("span");
		$document->body->appendChild($sut);

		$textWithHTML = "Hello, <b>World</b>!";
		$sut->textContent = $textWithHTML;
		self::assertNotSame($textWithHTML, $sut->innerHTML);
		self::assertSame($textWithHTML, $sut->innerText);
		self::assertSame("Hello, &lt;b&gt;World&lt;/b&gt;!", $sut->innerHTML);
	}

	public function testOuterHTML():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$sut->innerHTML = "<p>A paragraph</p>";
		self::assertEquals(
			"<example><p>A paragraph</p></example>",
			$sut->outerHTML
		);
	}

	public function testOuterHTMLNoParent():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$sut->outerHTML = "<not-example></not-example>";
// The original reference should not change.
		self::assertEquals("<example></example>", $sut->outerHTML);
	}

	public function testOuterHTMLSet():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$document->body->appendChild($sut);
		self::assertCount(
			1,
			$document->getElementsByTagName("example")
		);

		$innerHTML = "<p>A paragraph</p>";
		$sut->outerHTML = "<changed-outer>$innerHTML</changed-outer>";
		self::assertCount(
			0,
			$document->getElementsByTagName("example")
		);
		self::assertCount(
			1,
			$document->getElementsByTagName("changed-outer")
		);
		self::assertEquals(
			"<changed-outer><p>A paragraph</p></changed-outer>",
			$document->getElementsByTagName("changed-outer")->item(0)->outerHTML
		);
	}

	public function testOuterHTMLSetMultiple():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$document->body->appendChild($sut);
		self::assertCount(1, $document->getElementsByTagName("example"));
		$sut->outerHTML = "<first-outer>Example1</first-outer><second-outer>Example2</second-outer>";
		self::assertCount(0, $document->getElementsByTagName("example"));
		self::assertCount(1, $document->getElementsByTagName("first-outer"));
		self::assertCount(1, $document->getElementsByTagName("second-outer"));
	}

	public function testOuterHTMLParent():void {
		$document = new HTMLDocument();
		$html = "<changed-tag>Some content</changed-tag>";
		$sut = $document->createElement("example");
		$document->body->appendChild($sut);
		$sut->outerHTML = $html;
		self::assertCount(0, $document->getElementsByTagName("example"));
		self::assertCount(1, $document->getElementsByTagName("changed-tag"));
	}

	public function testChildren():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$sut->innerHTML = "<p>A paragraph</p>
		<div>A div</div>";
		self::assertCount(2, $sut->children);
		self::assertEquals("A paragraph", $sut->children[0]->innerHTML);
		self::assertEquals("A div", $sut->children[1]->innerHTML);
	}

	public function testPrefix():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertEquals("", $sut->prefix);
	}

	public function testTagName():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertEquals("example", $sut->tagName);
		$sut = $document->createElement("Example");
		self::assertEquals("example", $sut->tagName);
	}

	public function testTagNameInvalid():void {
		$document = new HTMLDocument();
		self::expectException(InvalidCharacterException::class);
		$document->createElement("This can't be done");
	}

	public function testClosestNoMatch():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertNull($sut->closest("nothing"));
	}

	public function testClosestSelf():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$document->body->appendChild($sut);
		self::assertSame($sut, $sut->closest("example"));
	}

	public function testClosestParent():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$context = $document->body;
		for($i = 0; $i < 10; $i++) {
			$parent = $document->createElement("example-$i");
			$context->appendChild($parent);
			$context = $parent;
		}
		$context->appendChild($sut);

		$element = $document->getElementsByTagName("example-3")->item(0);
		$closest = $sut->closest("example-3");
		self::assertSame(
			$element,
			$closest
		);
	}

	public function testClosestWithAnotherMatchingAncestor():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$tagNames = ["this-example", "that-example"];
		$context = $document->body;
		for($i = 0; $i < 10; $i++) {
			$tagName = $i % 2 ? $tagNames[0] : $tagNames[1];
			$parent = $document->createElement($tagName);
			$context->appendChild($parent);
			$context = $parent;
		}
		$context->appendChild($sut);

		$thatElements = $document->getElementsByTagName("that-example");
		$closest = $sut->closest("that-example");
		self::assertSame(
			$thatElements->item($thatElements->length - 1),
			$closest
		);
	}

	public function testGetAttribute():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertNull($sut->getAttribute("attr"));
		$sut->setAttribute("attr", "content");
		self::assertEquals("content", $sut->getAttribute("attr"));
	}

	public function testGetAttributeNamesNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertCount(0, $sut->getAttributeNames());
	}

	public function testGetAttributeNames():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$sut->setAttribute("name", "test");
		$sut->setAttribute("framework", "phpunit");
		$attributeNames = $sut->getAttributeNames();
		self::assertCount(2, $attributeNames);
		self::assertContains("name", $attributeNames);
		self::assertContains("framework", $attributeNames);
	}

	public function testGetAttributeNS():void {
		$xmlDoc = new XMLDocument(DocumentTestFactory::XML_SHAPE);
		$sut = $xmlDoc->getElementById("target");
		$ns = "http://www.example.com/2014/test";
		self::assertEquals(
			"Foo value",
			$sut->getAttributeNS($ns, "foo"));
	}

	public function testGetAttributeNSNone():void {
		$xmlDoc = new XMLDocument(DocumentTestFactory::XML_SHAPE);
		$sut = $xmlDoc->getElementById("target");
		$ns = "http://www.example.com/2014/test";
		self::assertNull($sut->getAttributeNS($ns, "nothing"));
	}

	public function testGetElementsByClassName():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$child1 = $sut->cloneNode();
		$child2 = $sut->cloneNode();
		$child3 = $sut->cloneNode();

		$child1->className = "one child-of-sut";
		$child2->className = "two child-of-sut another-class";
		$child3->className = "three child-of-sut another-class";

		$sut->append($child1, $child2, $child3);
		self::assertCount(1, $sut->getElementsByClassName("one"));
		self::assertCount(1, $sut->getElementsByClassName("two"));
		self::assertCount(1, $sut->getElementsByClassName("three"));
		self::assertCount(3, $sut->getElementsByClassName("child-of-sut"));
		self::assertCount(2, $sut->getElementsByClassName("another-class"));
	}

	public function testGetElementsByClassNameIsLive():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$child1 = $sut->cloneNode();

		$child1->className = "one child-of-sut";
		$sut->append($child1);
		self::assertCount(1, $sut->getElementsByClassName("one"));
		$child1->className = "not-one child-of-sut";
		self::assertCount(0, $sut->getElementsByClassName("one"));
		self::assertCount(0, $sut->getElementsByClassName("one child-of-sut"));
	}

	public function testGetElementsByTagName():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$child1 = $sut->cloneNode();
		$sut->append($child1);
		self::assertSame(
			$child1,
			$sut->getElementsByTagName("example")->item(0)
		);
	}

	public function testGetElementsByTagNameNS():void {
		$xmlDoc = new XMLDocument(DocumentTestFactory::XML_SHAPE);
		$sut = $xmlDoc->documentElement;
		$ns = "http://www.example.com/2014/test";
		self::assertCount(
			1,
			$sut->getElementsByTagNameNS($ns, "rect")
		);
		self::assertCount(
			0,
			$sut->getElementsByTagNameNS("another-namespace", "rect")
		);
	}

	public function testHasAttributes():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertFalse($sut->hasAttributes());
		$sut->setAttribute("test", "123");
		self::assertTrue($sut->hasAttributes());
	}

	public function testInsertAdjacentElementAfterBegin():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$pad = $sut->ownerDocument->createElement("pad");
		$sut->append($pad);

		$toInsert = $sut->cloneNode();
		$inserted = $sut->insertAdjacentElement(
			"afterbegin",
			$toInsert
		);
		self::assertSame($sut, $inserted->parentNode);
		self::assertSame($pad, $inserted->nextSibling);
	}

	public function testInsertAdjacentElementBeforeEnd():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$pad = $sut->ownerDocument->createElement("pad");
		$sut->append($pad);

		$toInsert = $sut->cloneNode();
		$inserted = $sut->insertAdjacentElement(
			"beforeend",
			$toInsert
		);
		self::assertSame($sut, $inserted->parentNode);
		self::assertSame($pad, $inserted->previousSibling);
	}

	public function testInsertAdjacentElementBeforeBeginNotConnected():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$toInsert = $sut->cloneNode();
		$inserted = $sut->insertAdjacentElement(
			"beforebegin",
			$toInsert
		);
		self::assertNull($inserted);
	}

	public function testInsertAdjacentElementBeforeBegin():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$root = $document->createElement("root");
		$document->body->appendChild($root);
		$pad = $sut->ownerDocument->createElement("pad");
		$root->appendChild($pad);
		$root->appendChild($sut);

		$toInsert = $sut->cloneNode();
		$inserted = $sut->insertAdjacentElement(
			"beforebegin",
			$toInsert
		);
		self::assertSame($pad, $inserted->previousSibling);
	}

	public function testInsertAdjacentElementAfterEndNotConnected():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$toInsert = $sut->cloneNode();
		$inserted = $sut->insertAdjacentElement(
			"afterend",
			$toInsert
		);
		self::assertNull($inserted);
	}

	public function testInsertAdjacentElementAfterEnd():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$root = $document->createElement("root");
		$document->body->appendChild($root);
		$pad = $document->createElement("pad");
		$root->appendChild($sut);
		$root->appendChild($pad);

		$toInsert = $sut->cloneNode();
		$inserted = $sut->insertAdjacentElement(
			"afterend",
			$toInsert
		);
		self::assertSame($pad, $inserted->nextSibling);
	}

	public function testInsertAdjacentElementInvalidPosition():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$toInsert = $sut->cloneNode();
		self::expectException(InvalidAdjacentPositionException::class);
		$sut->insertAdjacentElement(
			"nowhere",
			$toInsert
		);
	}

	public function testInsertAdjacentHTML():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$pad = $sut->ownerDocument->createElement("pad");
		$sut->appendChild($pad);
		$sut->insertAdjacentHTML(
			"afterbegin",
			"<inserted>Testing</inserted>"
		);
		self::assertCount(2, $sut->childNodes);
		self::assertSame(
			$pad,
			$sut->getElementsByTagName("inserted")->item(0)->nextSibling
		);
	}

	public function testInsertAdjacentText():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$pad = $sut->ownerDocument->createElement("pad");
		$sut->appendChild($pad);
		$sut->insertAdjacentText(
			"afterbegin",
			"This is a text node!"
		);
		self::assertCount(2, $sut->childNodes);
		self::assertSame(
			$pad,
			$sut->childNodes->item(0)->nextSibling
		);
		self::assertInstanceOf(Text::class, $sut->firstChild);
	}

	public function testMatches():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$document->body->appendChild($sut);
		self::assertTrue($sut->matches("example"));
		self::assertFalse($sut->matches("not-example"));
	}

	public function testSetAttributeNS():void {
		$xmlDoc = new XMLDocument(DocumentTestFactory::XML_SHAPE);
		$sut = $xmlDoc->getElementById("target");
		$ns = "http://www.example.com/2014/test";
		$sut->setAttributeNS($ns, "foo", "Updated value");
		self::assertEquals(
			"Updated value",
			$sut->getAttributeNS($ns, "foo"));
	}

	public function testToggleAttribute():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertFalse($sut->hasAttribute("required"));
		$requiredPresent = $sut->toggleAttribute("required");
		self::assertTrue($sut->hasAttribute("required"));
		self::assertTrue($requiredPresent);
		$requiredPresent = $sut->toggleAttribute("required");
		self::assertFalse($sut->hasAttribute("required"));
		self::assertFalse($requiredPresent);
	}

	public function testToggleAttributeForced():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertTrue(
			$sut->toggleAttribute("required", true)
		);
		self::assertTrue($sut->hasAttribute("required"));
		self::assertTrue(
			$sut->toggleAttribute("required", true)
		);
		self::assertTrue($sut->hasAttribute("required"));

		self::assertFalse(
			$sut->toggleAttribute("required", false)
		);
		self::assertFalse($sut->hasAttribute("required"));
		self::assertFalse(
			$sut->toggleAttribute("required", false)
		);
		self::assertFalse($sut->hasAttribute("required"));
	}

	public function testRemoveAttributeNS():void {
		$xmlDoc = new XMLDocument(DocumentTestFactory::XML_SHAPE);
		$sut = $xmlDoc->getElementById("target");
		$ns = "http://www.example.com/2014/test";
		$sut->removeAttributeNS($ns, "foo");
		self::assertFalse(
			$sut->hasAttributeNS($ns, "foo"));
	}

	public function testConstruct_copyrightTrademark():void {
		$copyright = "©";
		$trademark = "™";
		$text = "Copyright $copyright PHP.Gt, DOM$trademark";
		$sut = new HTMLDocument("<!doctype html><h1>PHP.Gt/Dom Unit Test</h1>");
		$h1 = $sut->querySelector("h1");
		$h1->innerHTML = $text;
		self::assertStringContainsString($copyright, $h1->innerHTML);
		self::assertStringContainsString($trademark, $h1->innerHTML);
	}

	public function testValue_empty():void {
		$document = new HTMLDocument("<!doctype html><input name='test' />");
		$sut = $document->querySelector("input[name='test']");
		self::assertEmpty($sut->value);
	}

	public function testValue_notEmptyFromHTML():void {
		$document = new HTMLDocument("<!doctype html><input name='test' value='abcdef' />");
		$sut = $document->querySelector("input[name='test']");
		self::assertNotEmpty($sut->value);
	}

	public function testValue_notEmptyFromProperty():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("input");
		$sut->value = "abcdef";
		self::assertNotEmpty($sut->value);
	}
}
