<?php
namespace Gt\Dom;

use DOMNameSpaceNode;
use DOMNode;

/**
 * The DOMDocument inheritance model has some strange quirks in the
 * specification and PHP implementation. This trait allows definition of
 * functions shared over all Node types.
 *
 * @property-read HTMLDocument|XMLDocument $ownerDocument
 * @property-read null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection $nextSibling Returns a Node representing the next node in the tree, or null if there isn't such node.
 * @property-read null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection $firstChild
 * @property-read null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection $lastChild
 * @property-read null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection $previousSibling Returns a Node representing the previous node in the tree, or null if there isn't such node.
 * @property-read bool $isConnected A boolean indicating whether the Node is connected (directly or indirectly) to the Document object.
 *
 * @method Node|Element cloneNode(bool $deep = false)
 */
trait RegisteredNodeClass {
	/**
	 * Returns a Boolean which indicates whether or not two nodes are of
	 * the same type and all their defining data points match.
	 *
	 * @param null|Node|Element|Document|DocumentType|Attr|ProcessingInstruction|DOMNode $otherNode
	 * The Node to compare equality with.
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/Node/isEqualNode
	 */
	// phpcs:ignore
	public function isEqualNode(
		null|Node|Element|Document|DocumentType|Attr|ProcessingInstruction|DOMNode $otherNode
	):bool {
		if($otherNode instanceof Document) {
			$otherNode = $otherNode->documentElement;
		}
		// For implementation specification, please see the W3C DOM Standard:
// @link https://dom.spec.whatwg.org/#concept-node-equals
		if($this->nodeType !== $otherNode->nodeType) {
			return false;
		}

		if($this->childNodes->length !== $otherNode->childNodes->length) {
			return false;
		}

		if($this instanceof DocumentType
		&& $otherNode instanceof DocumentType) {
			return $this->name === $otherNode->name
				&& $this->publicId === $otherNode->publicId
				&& $this->systemId === $otherNode->systemId;
		}

		if($this instanceof Element
		&& $otherNode instanceof Element) {
			$similar = $this->namespaceURI === $otherNode->namespaceURI
				&& $this->localName === $otherNode->localName
				&& count($this->attributes) === count($otherNode->attributes);
			if(!$similar) {
				return false;
			}

			for($i = 0, $len = count($this->attributes); $i < $len; $i++) {
				$attr = $this->attributes->item($i);
				$otherAttr = $otherNode->attributes->item($i);
				if(!$attr->isEqualNode($otherAttr)) {
					return false;
				}
			}

			for($i = 0, $len = $this->childNodes->length; $i < $len; $i++) {
				$child = $this->childNodes->item($i);
				$otherChild = $otherNode->childNodes->item($i);
				if(!$child->isEqualNode($otherChild)) {
					return false;
				}
			}

			return true;
		}

		if($this instanceof Attr
			&& $otherNode instanceof Attr) {
			return $this->namespaceURI === $otherNode->namespaceURI
				&& $this->localName === $otherNode->localName
				&& $this->value === $otherNode->value;
		}

		if($this instanceof ProcessingInstruction
		&& $otherNode instanceof ProcessingInstruction) {
			return $this->target === $otherNode->target
				&& $this->data === $otherNode->data;
		}

		/** @var Text|Comment $this */
		/** @var Text|Comment $otherNode */
		return $this->data === $otherNode->data;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/Node/isConnected */
	public function __prop_get_isConnected():bool {
		$context = $this;
		do {
			/** @var Element|Node|Document $context */
			if($context === $this->ownerDocument) {
				return true;
			}
		}
		while($context = $context->parentNode);

		return false;
	}

	/**
	 * Compares the position of the current node against another node in
	 * any other document.
	 *
	 * @param Node|Element $otherNode The other Node with which to compare
	 * the first node’s document position.
	 * @return int An integer value whose bits represent the otherNode's
	 * relationship to the calling node. More than one bit is set if
	 * multiple scenarios apply. For example, if otherNode is located
	 * earlier in the document and contains the node on which
	 * compareDocumentPosition() was called, then both the
	 * DOCUMENT_POSITION_CONTAINS and DOCUMENT_POSITION_PRECEDING bits would
	 * be set, producing a value of 10 (0x0A).
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/Node/compareDocumentPosition
	 */
	public function compareDocumentPosition(DOMNode|Node|Element $otherNode):int {
		$bits = 0b000000;
		if($otherNode->ownerDocument !== $this->ownerDocument) {
			$bits |= Node::DOCUMENT_POSITION_DISCONNECTED;
		}

		$thisNodePath = $this->getNodePath();
		$otherNodePath = $otherNode->getNodePath();
// A union of the two node paths are used to query the document, which will
// return a NodeList in document order.
		$unionPath = "$thisNodePath | $otherNodePath";
		$xpathResult = $this->ownerDocument->evaluate($unionPath);

		/** @var Node|Element|Document|DocumentType|Attr|ProcessingInstruction|DOMNode $node */
		foreach($xpathResult as $node) {
			if($node === $this) {
				$bits |= Node::DOCUMENT_POSITION_FOLLOWING;
				break;
			}
			if($node === $otherNode) {
				$bits |= Node::DOCUMENT_POSITION_PRECEDING;
				break;
			}
		}

		if($this->contains($otherNode)) {
			$bits |= Node::DOCUMENT_POSITION_CONTAINED_BY;
		}
		elseif($otherNode->contains($this)) {
			$bits |= Node::DOCUMENT_POSITION_CONTAINS;
		}

		return $bits;
	}

	/**
	 * Returns a Boolean value indicating whether or not a node is a
	 * descendant of the calling node.
	 *
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/Node/contains
	 */
	public function contains(
		Node|Element|Text|ProcessingInstruction|DocumentType|DocumentFragment
		|Document|Comment|CdataSection|Attr|DOMNode|DOMNameSpaceNode|null $otherNode
	):bool {
		$context = $otherNode;

		while($context = $context->parentNode) {
			if($context === $this) {
				return true;
			}
		}

		return false;
	}
}
