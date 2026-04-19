<?php
namespace GT\Dom;

use Iterator;

/**
 * The NodeIterator interface represents an iterator over the members of a list
 * of the nodes in a subtree of the DOM. The nodes will be returned in document
 * order.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/API/NodeIterator
 *
 * @property-read Node $root Returns a Node representing the root node as specified when the NodeIterator was created.
 * @property-read int $whatToShow Returns an unsigned long being a bitmask made of constants describing the types of Node that must to be presented. Non-matching nodes are skipped, but their children may be included, if relevant. Value is a combination of any NodeFilter.SHOW_* constnat.
 * @property-read NodeFilter $filter Returns a NodeFilter used to select the relevant nodes.
 *
 * @implements Iterator<int, Node|Element|Attr|Comment|Text|ProcessingInstruction>
 */
class NodeIterator implements Iterator {
	use Traversal;
}
