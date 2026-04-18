<?php
namespace GT\Dom;

use GT\Dom\Exception\MimeTypeNotSupportedException;

class DOMParser {
	public function __construct() {
	}

	public function parseFromString(
		string $content,
		string $mimeType,
	):HTMLDocument|XMLDocument {
		preg_match(
			"/\w+\/(\w+\+)?(?P<SUBTYPE>\w+)/",
			$mimeType,
			$mimeMatches
		);

		$class = match($mimeMatches["SUBTYPE"]) {
			"html" => HTMLDocument::class,
			"xml" => XMLDocument::class,
			default => null,
		};
		if(!$class) {
			throw new MimeTypeNotSupportedException($mimeType);
		}
		/** @var HTMLDocument|XMLDocument $object */
		$object = new $class($content);
		return $object;
	}
}
