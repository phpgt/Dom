<?php
namespace GT\Dom;

use DOMText;
use GT\Dom\Exception\IndexSizeException;
use Gt\PropFunc\MagicProp;

/**
 * The Text interface represents the textual content of Element or Attr.
 *
 * If an element has no markup within its content, it has a single child
 * implementing Text that contains the element's text. However, if the element
 * contains markup, it is parsed into information items and Text nodes that
 * form its children.
 *
 * New documents have a single Text node for each block of text. Over time,
 * more Text nodes may be created as the document's content changes. The
 * Node.normalize() method merges adjacent Text objects back into a single node
 * for each block of text.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/API/Text
 *
 * @property-read bool $isElementContentWhitespace Returns a Boolean flag indicating whether or not the text node contains only whitespace.
 * @property-read string $wholeText Returns a DOMString containing the text of all Text nodes logically adjacent to this Node, concatenated in document order.
 */
class Text extends DOMText {
	use MagicProp;
	use RegisteredNodeClass;
	use ChildNode;

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/Text/isElementContentWhitespace */
	protected function __prop_get_isElementContentWhitespace():bool {
		return strlen(trim($this->textContent)) === 0;
	}

	/**
	 * The Text.splitText() method breaks the Text node into two nodes at
	 * the specified offset, keeping both nodes in the tree as siblings.
	 *
	 * After the split, the current node contains all the content up to the
	 * specified offset point, and a newly created node of the same type
	 * contains the remaining text. The newly created node is returned to
	 * the caller. If the original node had a parent, the new node is
	 * inserted as the next sibling of the original node. If the offset is
	 * equal to the length of the original node, the newly created node has
	 * no data.
	 *
	 * Separated text nodes can be concatenated using the Node.normalize()
	 * method.
	 *
	 * @param int $offset The index immediately before which to break the
	 * text node.
	 * @return Text Returns a newly created Text node that contains the
	 * text after the specified offset point.
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/Text/splitText
	 */
	public function splitText(int $offset):Text {
		if($offset > strlen($this->textContent)
			|| $offset < 0) {
			throw new IndexSizeException("Index or size is negative or greater than the allowed amount");
		}
		$substr = substr($this->data, $offset);
		$this->data = substr($this->data, 0, $offset);
		/** @var Text $newNode */
		$newNode = $this->ownerDocument->createTextNode($substr);

		if($this->parentNode) {
			/** @var Text $newNode */
			$newNode = $this->parentNode->insertBefore($newNode, $this->nextSibling);
		}

		return $newNode;
	}
}
