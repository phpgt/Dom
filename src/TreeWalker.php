<?php
namespace GT\Dom;

use Iterator;

/**
 * The TreeWalker object represents the nodes of a document subtree and a
 * position within them.
 *
 * A TreeWalker can be created using the Document.createTreeWalker() method.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/API/TreeWalker
 *
 * @property-read Node|Element $root Returns a Node representing the root node as specified when the TreeWalker was created.
 * @property-read int $whatToShow Returns an unsigned long being a bitmask made  of constants describing the types of Node that must be presented. Non-matching nodes are skipped, but their children may be included, if relevant. The possible values are NodeFilter.SHOW_* constants.
 * @property-read NodeFilter $filter Returns a NodeFilter used to select the relevant nodes.
 * @property-read Node|Element $currentNode Is the Node on which the TreeWalker is currently pointing at.
 *
 * @implements Iterator<int, Node|Element|Attr|Comment|Text|ProcessingInstruction>
 *
 * Many thanks to Timo Tijhof for their dom-TreeWalker-polyfill project which
 * helped as reference while implementing this functionality:
 * https://github.com/Krinkle/dom-TreeWalker-polyfill
 */
class TreeWalker implements Iterator {
	use Traversal;
}
