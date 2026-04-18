<?php
namespace GT\Dom\Test\TestFactory;

use GT\Dom\Document;
use GT\Dom\DocumentFragment;
use GT\Dom\Element;
use GT\Dom\HTMLDocument;

class NodeTestFactory {
	public static function createNode(
		string $tagName,
		?Document $document = null
	):Element {
		if(!$document) {
			$document = new HTMLDocument();
		}

		return $document->createElement($tagName);
	}

	public static function createHTMLElement(
		string $tagName,
		?HTMLDocument $document = null
	):Element {
		if(!$document) {
			$document = new HTMLDocument();
		}

		return $document->createElement($tagName);
	}

	public static function createFragment(
		?HTMLDocument $document = null
	):DocumentFragment {
		if(!$document) {
			$document = HTMLDocumentFactory::create("");
		}

		return $document->createDocumentFragment();
	}

	public static function createTextNode(
		string $content = "",
		?HTMLDocument $document = null
	) {
		if(!$document) {
			$document = HTMLDocumentFactory::create("");
		}

		return $document->createTextNode($content);
	}
}
