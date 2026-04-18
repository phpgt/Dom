<?php
namespace GT\Dom;

class XPathResultFactory extends XPathResult {
	public static function create(
		string $query,
		Document $document,
		Node|Element $context,
	):XPathResult {
		return new XPathResult($query, $document, $context);
	}
}
