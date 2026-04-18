<?php
namespace GT\Dom\Test\TestFactory;

use GT\Dom\Facade\HTMLDocumentFactory;
use GT\Dom\HTMLDocument;
use GT\Dom\HTMLElement\HTMLElement;

class HTMLElementTestFactory {
	public static function create(
		string $tagName = "div",
		?HTMLDocument $document = null
	):HTMLElement {
		if(!$document) {
			$document = HTMLDocumentFactory::create("<!doctype html>");
		}

		/** @var HTMLElement $htmlElement */
		$htmlElement = $document->createElement($tagName);
		return $htmlElement;
	}
}
