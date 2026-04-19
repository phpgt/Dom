<?php
namespace GT\Dom;

class TreeWalkerFactory extends TreeWalker {
	public static function create(
		Node|Element $root,
		int $whatToShow = NodeFilter::SHOW_ALL,
		NodeFilter|callable|null $filter = null
	):TreeWalker {
		$class = TreeWalker::class;
		return new $class($root, $whatToShow, $filter);
	}
}
