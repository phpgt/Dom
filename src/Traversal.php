<?php
namespace Gt\Dom;

use Gt\PropFunc\MagicProp;

trait Traversal {
	use MagicProp;

	private Node|Element|Text|Attr|ProcessingInstruction|Comment|Document
	|DocumentType|DocumentFragment $pRoot;
	private int $pWhatToShow;
	private Node|Element|Text|Attr|ProcessingInstruction|Comment|Document
	|DocumentType|DocumentFragment $pCurrentNode;
	private NodeFilter $pFilter;
	private int $iteratorIndex;
	private null|Node|Element|Text|Attr|ProcessingInstruction|Comment
	|Document|DocumentType|DocumentFragment $validity;

	protected function __construct(
		Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection$root,
		int $whatToShow = NodeFilter::SHOW_ALL,
		NodeFilter|callable|null $filter = null
	) {
		$this->pRoot = $root;
		$this->pWhatToShow = $whatToShow;
		$this->pCurrentNode = $root;

		$this->iteratorIndex = 0;
		$this->validity = null;

		if(!$filter) {
			$filter = fn(Node|Element|Text|Attr|ProcessingInstruction
			|Comment|Document|DocumentType|DocumentFragment $node) => $this->filterFunction($node);
		}

		if(is_callable($filter)) {
			$filter = new class($filter, fn(
				Node|Element|Text|Attr|ProcessingInstruction
				|Comment|Document|DocumentType|DocumentFragment $node
			) => $this->filterFunction($node)) extends NodeFilter {
				/** @var callable */
				private $callback;
				/** @var callable */
				private $defaultCallback;

				public function __construct(
					callable $callback,
					callable $defaultCallback
				) {
					$this->callback = $callback;
					$this->defaultCallback = $defaultCallback;
				}

				public function acceptNode(
					Node|Element|Text|Attr|ProcessingInstruction
					|Comment|Document|DocumentType|DocumentFragment $node
				):int {
					$showFilter = call_user_func(
						$this->defaultCallback,
						$node
					);
					if($showFilter === NodeFilter::FILTER_ACCEPT) {
						return call_user_func(
							$this->callback,
							$node
						);
					}
					else {
						return $showFilter;
					}
				}
			};
		}
		/** @var NodeFilter $filter */
		$this->pFilter = $filter;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/TreeWalker/root */
	protected function __prop_get_root(
	):Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection {
		return $this->pRoot;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/TreeWalker/whatToShow */
	protected function __prop_get_whatToShow():int {
		return $this->pWhatToShow;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/TreeWalker/filter */
	protected function __prop_get_filter():NodeFilter {
		return $this->pFilter;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/TreeWalker/currentNode */
	protected function __prop_get_currentNode(
	):Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection {
		return $this->pCurrentNode;
	}

	/**
	 * The TreeWalker.parentNode() method moves the current Node to the
	 * first visible ancestor node in the document order, and returns the
	 * found node. If no such node exists, or if it is above the
	 * TreeWalker's root node, returns null and the current node is not
	 * changed.
	 *
	 * @return null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/TreeWalker/parentNode
	 */
	public function parentNode(
	):null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection {
		$node = $this->pCurrentNode;

		while($node && $node !== $this->pRoot) {
			/** @var ?Element $node */
			$node = $node->parentNode;

			/** @phpstan-ignore-next-line */
			if($node && $this->filter->acceptNode($node) === NodeFilter::FILTER_ACCEPT) {
				/** @phpstan-ignore-next-line */
				$this->pCurrentNode = $this->hintNullableNodeType($node);
				return $this->pCurrentNode;
			}
		}

		return null;
	}

	/**
	 * The TreeWalker.firstChild() method moves the current Node to the
	 * first visible child of the current node, and returns the found child.
	 * It also moves the current node to this child. If no such child
	 * exists, returns null and the current node is not changed.
	 *
	 * @return null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/TreeWalker/firstChild
	 */
	public function firstChild(
	):null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection {
		return $this->traverseChildren("first");
	}

	/**
	 * The TreeWalker.lastChild() method moves the current Node to the last
	 * visible child of the current node, and returns the found child. It
	 * also moves the current node to this child. If no such child exists,
	 * returns null and the current node is not changed.
	 *
	 * @return null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/TreeWalker/lastChild
	 */
	public function lastChild(
	):null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection {
		return $this->traverseChildren("last");
	}

	/**
	 * The TreeWalker.previousSibling() method moves the current Node to
	 * its previous sibling, if any, and returns the found sibling. If
	 * there is no such node, return null and the current node is not
	 * changed.
	 *
	 * @return null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/TreeWalker/previousSibling
	 */
	public function previousSibling(
	):null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection {
		return $this->traverseSiblings("previous");
	}

	/**
	 * The TreeWalker.nextSibling() method moves the current Node to its
	 * next sibling, if any, and returns the found sibling. If there is no
	 * such node, return null and the current node is not changed.
	 *
	 * @return null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/TreeWalker/nextSibling
	 */
	public function nextSibling(
	):null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection {
		return $this->traverseSiblings("next");
	}

	/**
	 * The TreeWalker.previousNode() method moves the current Node to the
	 * previous visible node in the document order, and returns the found
	 * node. It also moves the current node to this one. If no such node
	 * exists,or if it is before that the root node defined at the object
	 * construction, returns null and the current node is not changed.
	 *
	 * @return null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/TreeWalker/previousNode
	 */
	public function previousNode(
	):null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection {
		/** @var null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection$node */
		$node = $this->pCurrentNode;

		while($node !== $this->pRoot) {
			$sibling = $node->previousSibling;

			while(!is_null($sibling)) {
				/** @var null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection$node */
				$node = $sibling;
				/** @phpstan-ignore-next-line */
				$result = $this->filter->acceptNode($node);

				while($result !== NodeFilter::FILTER_REJECT
				&& !is_null($node->lastChild)) {
					$node = $node->lastChild;
					/** @phpstan-ignore-next-line */
					$result = $this->filter->acceptNode($node);
				}
				if($result === NodeFilter::FILTER_ACCEPT) {
					/** @phpstan-ignore-next-line  */
					$this->pCurrentNode = $this->hintNullableNodeType($node);
					return $this->pCurrentNode;
				}

				$sibling = $node->previousSibling;
			}

// The referenced polyfill for this implementation includes an extra check to
// the parentNode here, but as far as I can tell, this logic never be hit.
// See: https://github.com/Krinkle/dom-TreeWalker-polyfill/blob/master/src/TreeWalker-polyfill.js#L336-L338

			/** @var null|Element|Node|Text $node */
			$node = $node->parentNode;

			/** @phpstan-ignore-next-line */
			if($this->filter->acceptNode($node) === NodeFilter::FILTER_ACCEPT) {
				/** @phpstan-ignore-next-line  */
				$this->pCurrentNode = $this->hintNullableNodeType($node);
				return $this->pCurrentNode;
			}
		}

		return null;
	}

	/**
	 * The TreeWalker.nextNode() method moves the current Node to the next
	 * visible node in the document order, and returns the found node. It
	 * also moves the current node to this one. If no such node exists,
	 * returns null and the current node is not changed.
	 *
	 * @return null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/TreeWalker/nextNode
	 */
	public function nextNode(
	):null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection {
		if($node = $this->getNextNode($this->pCurrentNode)) {
			/** @var Node $node */
			$this->pCurrentNode = $node;
			return $node;
		}

		return null;
	}

	private function getNextNode(
		Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection $node
	):null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection {
		$result = NodeFilter::FILTER_ACCEPT;

		while(true) {
			do {
				/** @var Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection $node */
				if($node->firstChild) {
					$node = $node->firstChild;
				}
				else {
					continue;
				}

				if (get_class($node) === CdataSection::class) {
					$result = NodeFilter::FILTER_REJECT;
				}
				else {
					$result = $this->filter->acceptNode($node);
					if ($result === NodeFilter::FILTER_ACCEPT) {
						return $node;
					}
				}
			}
			while($result === NodeFilter::FILTER_REJECT
			&& !is_null($node->firstChild));

			$following = $this->nextSkippingChildren($node, $this->pRoot);
			if(!is_null($following)) {
				$node = $following;
			}
			else {
				break;
			}

			$result = $this->filter->acceptNode($node);
			if($result === NodeFilter::FILTER_ACCEPT) {
				return $node;
			}
		}

		return null;
	}

	public function current(
	):Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection {
		return $this->pCurrentNode;
	}

	public function next():void {
		$this->iteratorIndex++;
		$this->nextNode();
	}

	public function key():int {
		return $this->iteratorIndex;
	}

	public function valid():bool {
		$valid = $this->validity !== $this->pCurrentNode;
		$this->validity = $this->pCurrentNode;
		return $valid;
	}

	public function rewind():void {
		$this->iteratorIndex = 0;
		$this->pCurrentNode = $this->pRoot;
	}

	private function traverseChildren(
		string $direction
	):null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection {
		$node = $this->matchChild($this->pCurrentNode, $direction);
		if(!$node) {
			return null;
		}
		return $this->recurseTraverseChildren($node, $direction);
	}

	private function recurseTraverseChildren(
		Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection$node,
		string $direction
	):null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection {
		$overrideNode = null;

		while($node) {
			$result = $this->filter->acceptNode($node);
			if($result === NodeFilter::FILTER_ACCEPT) {
				$this->pCurrentNode = $node;
				return $node;
			}
			if($result === NodeFilter::FILTER_SKIP) {
// Skip this element, but not its children.
				$overrideNode = $node->nextSibling;
				$child = $this->matchChild($node, $direction);
				while($child) {
					$child = $this->recurseTraverseChildren($child, $direction);
				}
			}

			$node = $overrideNode ?? $node->nextSibling;
			$overrideNode = null;
		}

		return $node;
	}

	private function traverseSiblings(
		string $direction
	):null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection {
		$node = $this->pCurrentNode;

		if($node === $this->pRoot) {
			return null;
		}

		$sibling = $this->matchSibling(
			$node,
			$direction
		);
		while($sibling) {
			$node = $sibling;
			$result = $this->filter->acceptNode($node);
			if($result === NodeFilter::FILTER_ACCEPT) {
				$this->pCurrentNode = $node;
				break;
			}
		}

		return $node;
	}

	private function matchChild(
		Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection$node,
		string $direction
	):null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection {
		$result = match($direction) {
			"first" => $node->firstChild,
			"last", "next", "previous" => $node->lastChild,
			default => null,
		};
		return $this->hintNullableNodeType($result);
	}

	private function matchSibling(
		Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection$node,
		string $direction
	):null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection {
		$result = match($direction) {
			"next" => $node->nextSibling,
			"previous" => $node->previousSibling,
			default => null,
		};

		return $this->hintNullableNodeType($result);
	}

	private function nextSkippingChildren(
		Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection $node,
		Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection$stayWithin
	):null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection {
		if($node === $stayWithin) {
			return null;
		}
		if(!is_null($node->nextSibling)) {
			return $node->nextSibling;
		}

		while(!is_null($node->parentNode)) {
			/** @var Element $node */
			$node = $node->parentNode;
			if($node === $stayWithin) {
				break;
			}
			if(!is_null($node->nextSibling)) {
				/** @phpstan-ignore-next-line  */
				return $this->hintNodeType($node->nextSibling);
			}
		}

		return null;
	}

	// phpcs:ignore
	private function filterFunction(
		Node|Element|Attr|Text|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment $node
	):int {
		$show = $this->pWhatToShow;
		if($show === NodeFilter::SHOW_ALL) {
			return NodeFilter::FILTER_ACCEPT;
		}

		$matches = 0;
		if($show & NodeFilter::SHOW_ELEMENT) {
			$matches += $node instanceof Element;
		}
		if($show & NodeFilter::SHOW_ATTRIBUTE) {
			$matches += $node instanceof Attr;
		}
		if($show & NodeFilter::SHOW_TEXT) {
			$matches += $node instanceof Text;
		}
		if($show & NodeFilter::SHOW_PROCESSING_INSTRUCTION) {
			$matches += $node instanceof ProcessingInstruction;
		}
		if($show & NodeFilter::SHOW_COMMENT) {
			$matches += $node instanceof Comment;
		}
		if($show & NodeFilter::SHOW_DOCUMENT) {
			$matches += $node instanceof Document;
		}
		if($show & NodeFilter::SHOW_DOCUMENT_TYPE) {
			$matches += $node instanceof DocumentType;
		}
		if($show & NodeFilter::SHOW_DOCUMENT_FRAGMENT) {
			$matches += $node instanceof DocumentFragment;
		}

		return $matches > 0
			? NodeFilter::FILTER_ACCEPT
			: NodeFilter::FILTER_REJECT;
	}

	/**
	 * Due to libxml's inferred type system suggesting the use of native
	 * DOMDocument types, this function is introduced to allow PHP to force
	 * correct types at runtime. It also helps PHPStan understand the
	 * correct types that are in use.
	 */
	private function hintNodeType(
		Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection$input
	):Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection {
		return $input;
	}

	/**
	 * Due to libxml's inferred type system suggesting the use of native
	 * DOMDocument types, this function is introduced to allow PHP to force
	 * correct types at runtime. It also helps PHPStan understand the
	 * correct types that are in use.
	 */
	private function hintNullableNodeType(
		null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection$input
	):null|Node|Element|Text|Attr|ProcessingInstruction|Comment|Document|DocumentType|DocumentFragment|CdataSection {
		return $input;
	}
}
