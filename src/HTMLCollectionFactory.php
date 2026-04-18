<?php
namespace GT\Dom;

class HTMLCollectionFactory extends HTMLCollection {
	public static function create(callable $callback):HTMLCollection {
		return new HTMLCollection($callback);
	}

	public static function createHTMLOptionsCollection(
		callable $callback
	):HTMLOptionsCollection {
		return new HTMLOptionsCollection($callback);
	}

	public static function createHTMLFormControlsCollection(
		callable $callback
	):HTMLFormControlsCollection {
		return new HTMLFormControlsCollection($callback);
	}
}
