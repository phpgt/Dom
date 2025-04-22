<?php
namespace Gt\Dom;

use Dom\HTMLDocument as NativeHTMLDocument;
use Gt\Dom\Document;
use Stringable;

/** @property-read NativeHTMLDocument $nativeNode */
class HTMLDocument extends Document implements Stringable {
	const string DOCTYPE = "<!doctype html>";

	public ?HTMLElement $body {
		get {
			return $this->nodeRegistry->get($this->nativeNode->body);
		}
	}

	public function __construct(string $html = "") {
		$nativeNode = NativeHTMLDocument::createFromString($html);
		parent::__construct($nativeNode);
	}

	public function createElement(string $localName):HTMLElement {
		return $this->nodeRegistry->get($this->nativeNode->createElement($localName));
	}

	public function __toString():string {
		return $this->saveHtml();
	}

	public function saveHtml():string {
		return $this->nativeNode->saveHtml();
	}
}
