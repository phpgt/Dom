<?php
namespace GT\Dom;

use DOMException;
use DOMNode;
use Gt\CssXPath\Translator;
use GT\Dom\Exception\DocumentHasMoreThanOneElementChildException;
use GT\Dom\Exception\NotFoundErrorException;
use GT\Dom\Exception\TextNodeCanNotBeRootNodeException;
use GT\Dom\Exception\WrongDocumentErrorException;
use ReturnTypeWillChange;

/**
 * @link https://dom.spec.whatwg.org/#parentnode
 * @link https://developer.mozilla.org/en-US/docs/Web/API/ParentNode
 *
 * Contains methods and properties that are common to all types of Node objects
 * that can have children. It's implemented by Element, Document, and
 * DocumentFragment objects.
 *
 * @property-read int $childElementCount The number of children of this
 * ParentNode which are elements.
 * @property-read HTMLCollection $children A live HTMLCollection containing all
 *  objects of type Element that are children of this ParentNode.
 * @property-read HTMLDocument|XMLDocument $document
 * @property-read HTMLDocument|XMLDocument $ownerDocument
 * @property-read ?Element $firstElementChild The Element that is the first
 *  child of this ParentNode.
 * @property-read ?Element $lastElementChild The Element that is the last
 *  child of this ParentNode.
 */
trait ParentNode {
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/ParentNode/children */
	protected function __prop_get_children():HTMLCollection {
		return HTMLCollectionFactory::create(function() {
			$elementArray = [];

			for($i = 0, $len = $this->childNodes->length; $i < $len; $i++) {
				$child = $this->childNodes->item($i);
				if(!$child instanceof Element) {
					continue;
				}

				array_push($elementArray, $child);
			}

			return NodeListFactory::create(...$elementArray);
		});
	}

	/**
	 *  The Element.append() method inserts a set of Node objects or string
	 * objects after the last child of the Element. String objects are
	 * inserted as equivalent Text nodes.
	 *
	 * Differences from Node.appendChild():
	 * + Element.append() allows you to also append string objects,
	 * 	whereas Node.appendChild() only accepts Node objects.
	 * + Element.append() has no return value, whereas Node.appendChild()
	 * 	returns the appended Node object.
	 * + Element.append() can append several nodes and strings, whereas
	 * 	Node.appendChild() can only append one node.
	 * @param Node|Element|Text|Comment|DocumentFragment|string...$nodes
	 */
	public function append(...$nodes):void {
// Without this clumsy iteration, PHP 8.1 throws "free(): double free detected in tcache 2"
		foreach($nodes as $node) {
// And without this clumsy if/else, PHP 8.3 throws "double free or corruption (!prev)"
			if(is_string($node)) {
				/** @phpstan-ignore-next-line libxml's DOMNode does not define append() */
				parent::append($node);
			}
			else {
				parent::appendChild($node);
			}
		}
	}

	/**
	 * The Element.prepend() method inserts a set of Node objects or string
	 * objects before the first child of the Element. String objects are
	 * inserted as equivalent Text nodes.
	 * @param Node|Element|Text|Comment|string...$nodes
	 */
	public function prepend(...$nodes):void {
		$fragment = $this->ownerDocument->createDocumentFragment();

		foreach($nodes as $node) {
			if(is_string($node)) {
				$node = $this->ownerDocument->createTextNode($node);
			}
			$fragment->appendChild($node);
		}

		$this->insertBefore($fragment, $this->firstChild);
	}

	/**
	 * Adds the specified childNode argument as the last child to the
	 * current node. If the argument referenced an existing node on the
	 * DOM tree, the node will be detached from its current position and
	 * attached at the new position.
	 *
	 * @param Node|Element|Text|Comment $aChild The node to append to the
	 * given parent node (commonly an element).
	 * @return Node|Element The returned value is the appended
	 * child (aChild), except when aChild is a DocumentFragment, in which
	 * case the empty DocumentFragment is returned.
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/Node/appendChild
	 */
	public function appendChild(Node|Element|Text|Comment|DOMNode $aChild):Node|Element|Text|Comment {
		if($this instanceof Document) {
			if($aChild instanceof Text) {
				throw new TextNodeCanNotBeRootNodeException("Cannot insert a Text as a child of a Document");
			}

			if($this->childElementCount > 0) {
				throw new DocumentHasMoreThanOneElementChildException("Cannot have more than one Element child of a Document");
			}
		}

		try {
			/** @var Element|Node|Comment $appended */
			$appended = parent::appendChild($aChild);
			return $appended;
		}
		/** @noinspection PhpRedundantCatchClauseInspection */
		catch(DOMException $exception) {
			throw new WrongDocumentErrorException();
		}
	}

	/**
	 * The ParentNode.replaceChildren() method replaces the existing
	 * children of a Node with a specified new set of children. These can
	 * be DOMString or Node objects.
	 *
	 * @param string|Node ...$nodesOrDOMStrings A set of Node or DOMString
	 * objects to replace the ParentNode's existing children with. If no
	 * replacement objects are specified, then the ParentNode is emptied of
	 * all child nodes.
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/ParentNode/replaceChildren
	 */
	public function replaceChildren(...$nodesOrDOMStrings):void {
		while($this->firstChild) {
			$this->removeChild($this->firstChild);
		}

		$this->append(...$nodesOrDOMStrings);
	}

	/**
	 * The Document method querySelector() returns the first Element within
	 * the document that matches the specified selector, or group of
	 * selectors. If no matches are found, null is returned.
	 *
	 * @param string $selectors A DOMString containing one or more selectors
	 * to match against. This string must be a valid compound selector list
	 * supported by the browser; if it's not, a SyntaxError exception is
	 * thrown. See Locating DOM elements using selectors for more
	 * information about using selectors to identify elements. Multiple
	 * selectors may be specified by separating them using commas.
	 * @return ?Element The first Element that matches at least one of the
	 * specified selectors or null if no such element is found.
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/ParentNode/querySelector
	 */
	public function querySelector(string $selectors):?Element {
		/** @var Element[] $all */
		$all = $this->querySelectorAll($selectors);
// TODO: Is there a case for optimisation here?
// Test with a document of thousands of nodes to compare efficiency.
		return $all[0] ?? null;
	}

	/**
	 * The Document method querySelectorAll() returns a static (not live)
	 * NodeList representing a list of the document's elements that match
	 * the specified group of selectors.
	 *
	 * @param string $selectors A DOMString containing one or more selectors
	 * to match against. This string must be a valid CSS selector string; if
	 * it's not, a SyntaxError exception is thrown. See Locating DOM
	 * elements using selectors for more information about using selectors
	 * to identify elements. Multiple selectors may be specified by
	 * separating them using commas.
	 * @return NodeList A non-live NodeList containing one Element
	 * object for each descendant node that matches at least one of the
	 * specified selectors.
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/ParentNode/querySelectorAll
	 */
	public function querySelectorAll(string $selectors):NodeList {
		$context = $this;
		$prefix = ".//";
		if($this instanceof Document) {
			$context = $this->firstElementChild;
			$prefix = "//";
		}

		$document = ($this instanceof Document) ? $this : $this->ownerDocument;

		$translator = new Translator($selectors, $prefix);
		$xpathResult = $document->evaluate(
			$translator,
			$context
		);
		$nodeArray = iterator_to_array($xpathResult);
		return NodeListFactory::create(...$nodeArray);
	}

	/**
	 * The Element method getElementsByClassName() returns a live
	 * HTMLCollection which contains every descendant element which has the
	 * specified class name or names.
	 *
	 * The method getElementsByClassName() on the Document interface works
	 * essentially the same way, except it acts on the entire document,
	 * starting at the document root.
	 *
	 * @param string $names A DOMString containing one or more class names to match on, separated by whitespace.
	 * @return HTMLCollection An HTMLCollection providing a live-updating list of every element which is a member of every class in names.
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/Element/getElementsByClassName
	 */
	public function getElementsByClassName(string $names):HTMLCollection {
		$querySelector = "";
		foreach(explode(" ", $names) as $name) {
			if(strlen($querySelector) > 0) {
				$querySelector .= " ";
			}

			$querySelector .= ".$name";
		}

		return HTMLCollectionFactory::create(
			fn() => $this->querySelectorAll($querySelector)
		);
	}

	/**
	 * The getElementsByName() method of the Document object returns a
	 * NodeList Collection of elements with a given name in the document.
	 *
	 * @param string $name the value of the name attribute of the
	 * element(s).
	 * @return NodeList a live NodeList Collection, meaning it automatically
	 * updates as new elements with the same name are added to/removed from
	 * the document.
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/Document/getElementsByName
	 */
	public function getElementsByName(string $name):NodeList {
		$querySelector = "[name=$name]";
		return NodeListFactory::createLive(
			fn() => $this->querySelectorAll($querySelector)
		);
	}

	/**
	 * The getElementsByTagName method of Document interface returns an
	 * HTMLCollection of elements with the given tag name. The complete
	 * document is searched, including the root node. The returned
	 * HTMLCollection is live, meaning that it updates itself automatically
	 * to stay in sync with the DOM tree without having to call
	 * document.getElementsByTagName() again.
	 * @return HTMLCollection<Element>
	 * @phpstan-ignore-next-line
	 */
	#[ReturnTypeWillChange]
	public function getElementsByTagName(string $qualifiedName):HTMLCollection {
		return HTMLCollectionFactory::create(fn() => $this->querySelectorAll($qualifiedName));
	}

	/**
	 * The removeChild() method of the Node interface removes a child node
	 * from the DOM and returns the removed node.
	 */
	public function removeChild(
		Node|Element|Text|Comment|DOMNode|ProcessingInstruction $child
	):Node|Element|Text|Comment|CdataSection|ProcessingInstruction {
		try {
			/** @var Node|Element|Text|Comment $removed */
			$removed = parent::removeChild($child);
			return $removed;
		}
		/** @noinspection PhpRedundantCatchClauseInspection */
		catch(DOMException) {
			throw new NotFoundErrorException("Child to be removed is not a child of this node");
		}
	}

	/**
	 * The replaceChild() method of the Node element replaces a child node
	 * within the given (parent) node.
	 * @return Node|Element|Text|Comment the replaced node
	 */
	public function replaceChild(
		Node|Element|DOMNode $node,
		Node|Element|DOMNode $child
	):Node|Element|Text|Comment {
		try {
			/** @var Node|Element|Text|Comment|false $replaced */
			$replaced = parent::replaceChild($node, $child);
			if(!$replaced) {
				throw new DOMException();
			}
			return $replaced;
		}
		catch(DOMException) {
			throw new NotFoundErrorException("Child to be replaced is not a child of this node");
		}
	}
}
