<?php
namespace Gt\Dom;

use Dom\Element as NativeElement;

/**
 * @property-read NativeElement $nativeNode
 */
class Element extends Node {
	public string $tagName {
		get {
			return $this->nativeNode->tagName;
		}
	}

	public string $innerHTML {
		get {
			return $this->nativeNode->innerHTML;
		}
	}
}
