<?php
namespace Gt\Dom;

use SplObjectStorage;
use Dom\Document as NativeDocument;
use Dom\Node as NativeNode;
use Dom\Element as NativeElement;
use Dom\HTMLElement as NativeHTMLElement;
use Gt\Dom\Node;

/** @property-read NativeDocument $nativeNode */
class Document extends Node {
	protected NodeRegistry $nodeRegistry;

	public Element $documentElement {
		get {
			return $this->nodeRegistry->get($this->nativeNode->documentElement);
		}
	}

	protected function __construct(\Dom\Document $nativeDocument) {
		$this->nodeRegistry = new NodeRegistry($this->wrap(...));
		parent::__construct(
			$nativeDocument,
			$this->nodeRegistry,
		);
	}

	public function createElement(string $localName):Element {
		return $this->nodeRegistry->get(
			$this->nativeNode->createElement($localName)
		);
	}

	private function wrap(NativeNode $native):Node {
		$className = match(get_class($native)) {
			NativeElement::class => Element::class,
			NativeHTMLElement::class => HTMLElement::class,
		};

		$refClass = new \ReflectionClass($className);
		$constructor = $refClass->getConstructor();
		$constructor->setAccessible(true);
		$node = $refClass->newInstanceWithoutConstructor();
		$constructor->invoke($node, $native, $this->nodeRegistry);

		return $node;
	}
}
