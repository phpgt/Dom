<?php
namespace GT\Dom;

use DOMDocumentFragment;
use DOMXPath;

class DocumentFragment extends DOMDocumentFragment {
	use RegisteredNodeClass;

	/**
	 * Returns the first Element node within the DocumentFragment, in
	 * document order, that matches the specified ID. Functionally
	 * equivalent to Document.getElementById().
	 *
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/DocumentFragment/getElementById
	 */
	public function getElementById(string $id):null|Node|Element {
		$xpath = new DOMXPath($this->ownerDocument);
		$result = $xpath->evaluate("*[@id='$id']", $this);
		return $result?->item(0);
	}
}
