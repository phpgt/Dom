<?php
namespace Gt\Dom;

trait ElementNode {
	/**
	 * Returns a Boolean which indicates whether or not two nodes are of
	 * the same type and all their defining data points match.
	 *
	 * @param Node $otherNode The Node to compare equality with.
	 * @return bool
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/Node/isEqualNode
	 * @noinspection PhpParameterNameChangedDuringInheritanceInspection
	 */
	public function isEqualNode(Node|Element|Document $otherNode):bool {
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
				&& $this->attributes->length === $otherNode->attributes->length;
			if(!$similar) {
				return false;
			}

			for($i = 0, $len = $this->attributes->length; $i < $len; $i++) {
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

		if(isset($this->data)) {
			/** @var Text|Comment $this */
			/** @var Text|Comment $otherNode */
			return $this->data === $otherNode->data;
		}

		return false;
	}
}
