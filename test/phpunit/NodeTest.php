<?php
namespace GT\Dom\Test;

use GT\Dom\Exception\NotFoundErrorException;
use GT\Dom\HTMLDocument;
use GT\Dom\Node;
use GT\Dom\Test\TestFactory\NodeTestFactory;
use GT\Dom\Text;
use GT\Dom\XMLDocument;
use PHPUnit\Framework\TestCase;

class NodeTest extends TestCase {
	public function testCanNotConstruct():void {
		self::expectException(\Error::class);
		self::expectExceptionMessageMatches("/Call to private GT.Dom.Node[0-9]*::__construct()/");
		$className = Node::class;
		/** @phpstan-ignore-next-line */
		new $className();
	}

	public function testBaseURIClientSideOnly():void {
		$document = new XMLDocument();
		$sut = $document->createElement("example");

		if(version_compare(PHP_VERSION, "8.4", ">=")) {
			self::assertEquals(getcwd() . "/", $sut->baseURI);
		}
		else {
			self::assertNull($sut->baseURI);
		}
	}

	public function testChildNodesEmpty():void {
		$sut = (new HTMLDocument())->createElement("example");
		self::assertEmpty($sut->childNodes);
		self::assertCount(0, $sut->childNodes);
	}

	public function testChildNodes():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$child = $sut->ownerDocument->createElement("example-child");
		$sut->appendChild($child);
		self::assertCount(1, $sut->childNodes);
	}

	public function testChildNodesManyLive():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");

		$nodeList = $sut->childNodes;
		self::assertCount(0, $nodeList);

		for($i = 0; $i < 100; $i++) {
			$child = $sut->ownerDocument->createElement(
				uniqid("child-")
			);
			$sut->appendChild($child);
		}

		self::assertCount(100, $nodeList);
	}

	public function testFirstChildNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertNull($sut->firstChild);
	}

	public function testFirstChild():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$c1 = $sut->ownerDocument->createElement("child-one");
		$c2 = $sut->ownerDocument->createElement("child-two");
		$c3 = $sut->ownerDocument->createElement("child-three");
		$sut->append($c1, $c2, $c3);
		self::assertSame($c1, $sut->firstChild);
	}

	public function testLastChildNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertNull($sut->lastChild);
	}

	public function testLastChild():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$c1 = $sut->ownerDocument->createElement("child-one");
		$c2 = $sut->ownerDocument->createElement("child-two");
		$c3 = $sut->ownerDocument->createElement("child-three");
		$sut->append($c1, $c2, $c3);
		self::assertSame($c3, $sut->lastChild);
	}

	public function testFirstChildAfterInsertBefore():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$c1 = $sut->ownerDocument->createElement("child-one");
		$c2 = $sut->ownerDocument->createElement("child-two");
		$c3 = $sut->ownerDocument->createElement("child-three");
		$sut->append($c1, $c2);
		$sut->insertBefore($c3, $c1);
		self::assertSame($c3, $sut->firstChild);
	}

	public function testIsConnected():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertFalse($sut->isConnected);
		$sut->ownerDocument->append($sut);
		self::assertTrue($sut->isConnected);
	}

	public function testIsConnectedNo():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertFalse($sut->isConnected);
		$sut->ownerDocument->append($sut);
		$sut->ownerDocument->removeChild($sut);
		self::assertFalse($sut->isConnected);
	}

	public function testNextSiblingNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertNull($sut->nextSibling);
	}

	public function testNextSibling():void {
		$document = new HTMLDocument();
		$parent = $document->createElement("parent");
		$c1 = $document->createElement("child");
		$sut = $document->createElement("child");
		$c2 = $document->createElement("child");

		$parent->append($c1, $sut, $c2);
		self::assertSame($c2, $sut->nextSibling);
	}

	public function testPreviousSiblingNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertNull($sut->previousSibling);
	}

	public function testPreviousSibling():void {
		$document = new HTMLDocument();
		$parent = $document->createElement("parent");
		$c1 = $document->createElement("child");
		$sut = $document->createElement("child");
		$c2 = $document->createElement("child");

		$parent->append($c1, $sut, $c2);
		self::assertSame($c1, $sut->previousSibling);
	}

	public function testNodeName():void {
// TODO: Test other types of nodes (Attr, CDATASection, Comment, Document,
// DocumentFragment, DocumentType, Notation, ProcessingInstruction, Text) as
// each node type has a different expectation for this value.
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertEquals("example", $sut->nodeName);
	}

	public function testNodeType():void {
// TODO: Test the other types of nodes, make sure they return the expected
// values in accordance to the Node::TYPE* constants.
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertEquals(
			Node::TYPE_ELEMENT,
			$sut->nodeType
		);
	}

	public function testNodeValueGetNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertSame("", $sut->nodeValue);
	}

	public function testNodeValueGetEmptyWithChildTextContent():void {
		$message = "This is a test message.";
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$sut->innerHTML = $message;
		$textNode = $sut->childNodes[0];
		self::assertInstanceOf(Text::class, $textNode);
		self::assertEquals($message, $textNode->nodeValue);
	}

	public function testNode_textContent_ignoreProcessingInstruction():void {
		$message1 = "123";
		$message2 = "321";
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$processingInstruction = $sut->ownerDocument->createProcessingInstruction("example-target", "example-data");
		$sut->append($message1, $processingInstruction, $message2);

		self::assertEquals("123321", $sut->textContent);
	}

	public function testOwnerDocument():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertSame($document, $sut->ownerDocument);
	}

	public function testParentElementNone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertNull($sut->parentElement);
	}

	public function testParentElement():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$parent = NodeTestFactory::createNode("parent", $sut->ownerDocument);
		$parent->appendChild($sut);
		self::assertSame($parent, $sut->parentElement);
	}

	public function testParentElementNode():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$fragment = $sut->ownerDocument->createDocumentFragment();
		$fragment->appendChild($sut);
		$parent = NodeTestFactory::createNode("parent", $sut->ownerDocument);
// When the fragment is the parentNode, it is not an "Element" so this should be null...
		self::assertNull($sut->parentElement);
		$parent->appendChild($fragment);
// ... but when the fragment is added, the parent is not the Element node.
		self::assertSame($parent, $sut->parentElement);
	}

	public function testCloneNode():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$sut->innerHTML = "<p><a href='https://phpunit.de'>PHPUnit is amazing!</a>";
		$clone = $sut->cloneNode();
		self::assertNotSame($sut, $clone);
		self::assertEquals("example", $clone->tagName);
		self::assertCount(0, $clone->childNodes);
	}

	public function testCloneNodeDeep():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$sut->innerHTML = "<p><a href='https://phpunit.de'>PHPUnit is amazing!</a>";
		/** @var Element $clone */
		$clone = $sut->cloneNode(true);
		self::assertNotSame($sut, $clone);
		self::assertStringContainsString(
			"PHPUnit is amazing!",
			$clone->innerHTML
		);
		self::assertSame($sut->innerHTML, $clone->innerHTML);
	}

	public function testCompareDocumentPositionNowhere():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$otherDocument = new HTMLDocument();
		$anotherNodeInAnotherDoc = $otherDocument->createElement("other");
		self::assertSame(
			Node::DOCUMENT_POSITION_DISCONNECTED,
			$sut->compareDocumentPosition($anotherNodeInAnotherDoc)
		);
	}

	public function testCompareDocumentPositionPreceding():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$root = $document->createElement("root");
		$compareTo = $document->createElement("compare-to");

		$document->body->appendChild($root);
		$root->appendChild($sut);
		$root->appendChild($compareTo);

		self::assertGreaterThan(
			0,
			$sut->compareDocumentPosition($compareTo)
			& Node::DOCUMENT_POSITION_FOLLOWING
		);
		self::assertGreaterThan(
			0,
			$compareTo->compareDocumentPosition($sut)
			& Node::DOCUMENT_POSITION_PRECEDING
		);
		self::assertEquals(
			0,
			$sut->compareDocumentPosition($compareTo)
			& Node::DOCUMENT_POSITION_DISCONNECTED
		);
		self::assertEquals(
			0,
			$compareTo->compareDocumentPosition($sut)
			& Node::DOCUMENT_POSITION_DISCONNECTED
		);
	}

	public function testCompareDocumentPositionFollowing():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$root = $document->createElement("root");
		$compareTo = $document->createElement("compare-to");

		$document->body->appendChild($root);
		$root->appendChild($compareTo);
		$root->appendChild($sut);

		self::assertGreaterThan(
			0,
			$sut->compareDocumentPosition($compareTo)
			& Node::DOCUMENT_POSITION_PRECEDING
		);
		self::assertGreaterThan(
			0,
			$compareTo->compareDocumentPosition($sut)
			& Node::DOCUMENT_POSITION_FOLLOWING
		);
		self::assertEquals(
			0,
			$sut->compareDocumentPosition($compareTo)
			& Node::DOCUMENT_POSITION_DISCONNECTED
		);
		self::assertEquals(
			0,
			$compareTo->compareDocumentPosition($sut)
			& Node::DOCUMENT_POSITION_DISCONNECTED
		);
	}

	public function testCompareDocumentPositionContainedBy():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$root = $document->createElement("root");
		$compareTo = $document->createElement("compare-to");

		$document->body->appendChild($root);
		$root->appendChild($sut);
		$sut->appendChild($compareTo);

		self::assertGreaterThan(
			0,
			$sut->compareDocumentPosition($compareTo)
			& Node::DOCUMENT_POSITION_CONTAINED_BY
		);
		self::assertEquals(
			0,
			$sut->compareDocumentPosition($compareTo)
			& Node::DOCUMENT_POSITION_CONTAINS
		);
	}

	public function testCompareDocumentPositionContainedByFlipped():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$root = $document->createElement("root");
		$compareTo = $document->createElement("compare-to");

		$document->body->appendChild($root);
		$root->appendChild($compareTo);
		$compareTo->appendChild($sut);

		self::assertGreaterThan(
			0,
			$sut->compareDocumentPosition($compareTo)
			& Node::DOCUMENT_POSITION_CONTAINS
		);
		self::assertEquals(
			0,
			$sut->compareDocumentPosition($compareTo)
			& Node::DOCUMENT_POSITION_CONTAINED_BY
		);
	}

	public function testContainsOtherDocumentNode():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$otherNode = $document->createElement("example");
		self::assertFalse($sut->contains($otherNode));
	}

	public function testContainsNegativeWhenNotChild():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$otherNode = $document->createElement("example");
		self::assertFalse($sut->contains($otherNode));
	}

	public function testContainsPositiveWhenChild():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("parent");
		$otherNode = $document->createElement("child");
		$sut->appendChild($otherNode);
		self::assertTrue($sut->contains($otherNode));
	}

	public function testHasChildNodesEmpty():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertFalse($sut->hasChildNodes());
	}

	public function testHasChildNodes():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$child = $document->createElement("example");
		$sut->appendChild($child);
		self::assertTrue($sut->hasChildNodes());
		self::assertFalse($child->hasChildNodes());
	}

	public function testInsertBefore():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$one = $document->createElement("one");
		$two = $document->createElement("two");
		$three = $document->createElement("three");
		$four = $document->createElement("four");
		$sut->append($one, $two, $four);
		$sut->insertBefore($three, $four);

		$tagsFound = "";
		foreach($sut->childNodes as $child) {
			$tagsFound .= $child->nodeName;
		}

		self::assertEquals("one" . "two" . "three" . "four", $tagsFound);
	}

	public function testInsertBeforeNullRef():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$one = NodeTestFactory::createNode("one", $sut->ownerDocument);
		$two = NodeTestFactory::createNode("two", $sut->ownerDocument);
		$sut->insertBefore($one, null);
		$sut->insertBefore($two, null);

		$tagsFound = "";
		foreach($sut->childNodes as $child) {
			$tagsFound .= $child->nodeName;
		}

		self::assertEquals("one" . "two", $tagsFound);
	}

	public function testIsDefaultNamespace():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertFalse($sut->isDefaultNamespace("not-in-namespace"));
	}

	public function testIsEqualNodeClone():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$clone = $sut->cloneNode(true);
		self::assertTrue($clone->isEqualNode($sut));
	}

	public function testIsEqualNode():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$inserted = $document->body->appendChild($sut);
		self::assertTrue($sut->isEqualNode($inserted));
	}

	public function testIsEqualNodeDifferent():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$other = $sut->ownerDocument->createElement("example");
		$other->innerHTML = "different";
		self::assertFalse($sut->isEqualNode($other));
	}

	public function testIsEqualNodeDifferentType():void {
// TODO: Test equality of different node types.
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$attr = $sut->ownerDocument->createAttribute("example");
		self::assertFalse($sut->isEqualNode($attr));
	}

	public function testIsSameNodeDifferentDocument():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$other = NodeTestFactory::createNode("example");
		self::assertFalse($sut->isSameNode($other));
	}

	public function testIsSameNodeSameDocumentDifferentNode():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$other = NodeTestFactory::createNode("example", $sut->ownerDocument);
		self::assertFalse($sut->isSameNode($other));
	}

	public function testIsSameNode():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$same = $document->body->appendChild($sut);
		self::assertTrue($sut->isSameNode($same));
	}

	public function testLookupPrefix():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertNull($sut->lookupPrefix("nothing"));
	}

	public function testLookupNamespaceURI():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertNull($sut->lookupNamespaceURI("test"));
	}

	public function testNormalize():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$clone = $sut->cloneNode(true);
		$sut->normalize();
		self::assertEquals($sut->outerHTML, $clone->outerHTML);
	}

	public function testNormalizeWhitespaceOnly():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$sut->appendChild($sut->ownerDocument->createTextNode("Part 1 "));
		$sut->appendChild($sut->ownerDocument->createTextNode("Part 2 "));
		$sut->appendChild($sut->ownerDocument->createTextNode("Part 3 "));
		self::assertCount(3, $sut->childNodes);
		$sut->normalize();
		self::assertCount(1, $sut->childNodes);
		self::assertEquals("Part 1 Part 2 Part 3 ", $sut->innerHTML);
	}

	public function testNormalizeManyChildren():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$sut->appendChild($sut->ownerDocument->createElement("test"));
		$sut->appendChild($sut->ownerDocument->createTextNode("Part 1 "));
		$sut->appendChild($sut->ownerDocument->createTextNode("Part 2 "));
		$sut->appendChild($sut->ownerDocument->createTextNode("Part 3 "));
		$sut->appendChild($sut->ownerDocument->createElement("test"));
		$sut->appendChild($sut->ownerDocument->createElement("test"));
		self::assertCount(6, $sut->childNodes);
		$sut->normalize();
		self::assertCount(4, $sut->childNodes);
		self::assertEquals("<test></test>Part 1 Part 2 Part 3 <test></test><test></test>", $sut->innerHTML);
	}

	public function testRemoveChildNotChild():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::expectException(NotFoundErrorException::class);
		$sut->ownerDocument->removeChild($sut);
	}

	public function testRemoveChild():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$child = $sut->ownerDocument->createElement("child");
		$sut->appendChild($child);
		self::assertCount(1, $sut->childNodes);
		$sut->removeChild($child);
		self::assertCount(0, $sut->childNodes);
	}

	public function testReplaceChildNotChild():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$new = $document->createElement("new");
		$old = $document->createElement("old");
		self::expectException(NotFoundErrorException::class);
		self::expectExceptionMessage("Child to be replaced is not a child of this node");
		$sut->replaceChild($new, $old);
	}

	public function testReplaceChild():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$new = $sut->ownerDocument->createElement("new");
		$old = $sut->ownerDocument->createElement("old");
		$sut->appendChild($old);
		self::assertEquals("<old></old>", $sut->innerHTML);
		$sut->replaceChild($new, $old);
		self::assertEquals("<new></new>", $sut->innerHTML);
	}

	public function testTextContentEmpty():void {
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		self::assertEquals("", $sut->textContent);
	}

	public function testTextContentSingleTextNode():void {
		$message = "Here is some content!";
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$sut->innerHTML = "<p>$message</p>";
		self::assertEquals($message, $sut->textContent);
	}

	public function testTextContentMultipleTextNode():void {
		$message1 = "ONE";
		$message2 = "TWO";
		$message3 = "THREE";
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$sut->innerHTML = "<ul><li>$message1</li><li>$message2</li><li>$message3</li></ul>";
		self::assertEquals("ONE" . "TWO" . "THREE", $sut->textContent);
	}

	public function testTextContentSetFromEmpty():void {
		$message = "A test message";
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$sut->textContent = $message;
		self::assertEquals($message, $sut->textContent);
		self::assertCount(1, $sut->childNodes);
	}

	public function testTextContentSetFromNonEmpty():void {
		$message = "A test message";
		$document = new HTMLDocument();
		$sut = $document->createElement("example");
		$sut->innerHTML = "<h1>This node has existing content!</h1>";
		$sut->textContent = $message;
		self::assertEquals($message, $sut->textContent);
		self::assertCount(1, $sut->childNodes);
	}
}
