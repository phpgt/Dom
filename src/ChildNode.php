<?php
namespace GT\Dom;

/**
 * The ChildNode mixin contains methods and properties that are common to all
 * types of Node objects that can have a parent. It's implemented by Element,
 * DocumentType, and CharacterData objects.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/API/ChildNode
 *
 * @property-read null|Element $parentElement
 * @property-read null|Node|Element $parentNode
 */
trait ChildNode {
	/** @link https://developer.mozilla.org/en-US/docs/Web/API/Node/parentElement */
	protected function __prop_get_parentElement():null|Element {
		$element = $this->parentNode;
		if($element instanceof Element) {
			return $element;
		}

		return null;
	}

	/**
	 * Removes the object from the tree it belongs to.
	 *
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/ChildNode/remove
	 */
	public function remove():void {
		if($parentNode = $this->parentNode) {
			$parentNode->removeChild($this);
		}
	}

	/**
	 * The ChildNode.before() method inserts a set of Node or DOMString
	 * objects in the children list of this ChildNode's parent, just before
	 * this ChildNode. DOMString objects are inserted as equivalent Text
	 * nodes.
	 *
	 * @param Element|Node|string...$nodes A set of Node or DOMString
	 * objects to insert.
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/ChildNode/before
	 */
	public function before(...$nodes):void {
		$parent = $this->parentNode;
		if(!$parent) {
			return;
		}

		foreach($nodes as $node) {
			if(is_string($node)) {
				$node = $this->ownerDocument->createTextNode($node);
			}
			$parent->insertBefore($node, $this);
		}
	}

	/**
	 * The ChildNode.after() method inserts a set of Node or DOMString
	 * objects in the children list of this ChildNode's parent, just after
	 * this ChildNode. DOMString objects are inserted as equivalent Text
	 * nodes.
	 *
	 * @param Element|Node|string...$nodes
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/ChildNode/after
	 */
	public function after(...$nodes):void {
		$parent = $this->parentNode;
		$nextSibling = $this->nextSibling;
		if(!$parent) {
			return;
		}

		foreach($nodes as $node) {
			if(is_string($node)) {
				$node = $this->ownerDocument->createTextNode($node);
			}
			$parent->insertBefore($node, $nextSibling);
		}
	}

	/**
	 * The ChildNode.replaceWith() method replaces this ChildNode in the
	 * children list of its parent with a set of Node or DOMString objects.
	 * DOMString objects are inserted as equivalent Text nodes.
	 *
	 * @param Node|Element|string...$nodes
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/ChildNode/replaceWith
	 */
	public function replaceWith(...$nodes):void {
		$parent = $this->parentNode;
		if(!$parent) {
			return;
		}

		$fragment = $this->ownerDocument->createDocumentFragment();
		foreach($nodes as $node) {
			if(is_string($node)) {
				$node = $this->ownerDocument->createTextNode($node);
			}
			$fragment->appendChild($node);
		}

		$parent->replaceChild($fragment, $this);
	}
}
