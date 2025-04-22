<?php
namespace Gt\Dom;

use Dom\Node as NativeNode;

class Node {
	protected Document $ownerDocument {
		get {
			return $this->nodeRegistry->get($this->nativeNode->ownerDocument);
		}
	}

	protected function __construct(
		protected NativeNode $nativeNode,
		protected NodeRegistry $nodeRegistry,
	) {
	}

	public function appendChild(HTMLElement $node):Node {
		return $this->nodeRegistry->get($this->nativeNode->appendChild($node->nativeNode));
	}
}
