<?php
namespace GT\Dom;

class NodeListFactory extends NodeList {
	public static function create(Node|Element|Attr|Text...$nodeList):NodeList {
		return new NodeList(...$nodeList);
	}

	public static function createLive(callable $callback):NodeList {
		return new NodeList($callback);
	}

	public static function createRadioNodeList(callable $callback):RadioNodeList {
		return new RadioNodeList($callback);
	}
}
