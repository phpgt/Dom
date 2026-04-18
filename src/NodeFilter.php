<?php
namespace GT\Dom;
/**
 * A NodeFilter interface represents an object used to filter the nodes in a
 * NodeIterator or TreeWalker. A NodeFilter knows nothing about the document or
 * traversing nodes; it only knows how to evaluate a single node against the
 * provided filter.
 *
 * Note: The browser doesn't provide any object implementing this interface. It
 * is the user who is expected to write one, tailoring the acceptNode() method
 * to its needs, and using it with some TreeWalker or NodeIterator objects.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/API/NodeFilter
 */
abstract class NodeFilter {
	const FILTER_ACCEPT = 1;
	const FILTER_REJECT = 2;
	const FILTER_SKIP = 3;

	const SHOW_ALL = -1;
	const SHOW_ELEMENT = 1;
	const SHOW_ATTRIBUTE = 2;
	const SHOW_TEXT = 4;
	const SHOW_PROCESSING_INSTRUCTION = 64;
	const SHOW_COMMENT = 128;
	const SHOW_DOCUMENT = 256;
	const SHOW_DOCUMENT_TYPE = 512;
	const SHOW_DOCUMENT_FRAGMENT = 1024;

	/**
	 * The NodeFilter.acceptNode() method returns an unsigned short that
	 * will be used to tell if a given Node must be accepted or not by the
	 * NodeIterator or TreeWalker iteration algorithm. This method is
	 * expected to be written by the user of a NodeFilter.
	 * Possible return values are:
	 * NodeFilter.FILTER_ACCEPT
	 * NodeFilter.FILTER_REJECT
	 * NodeFilter.FILTER_SKIP
	 *
	 * @param Node $node Is a Node being the object to check against the
	 * filter.
	 * @return int
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/NodeFilter/acceptNode
	 */
	abstract public function acceptNode(Node|Element $node):int;
}
