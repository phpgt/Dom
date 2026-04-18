<?php
namespace GT\Dom;

use ArrayAccess;
use Countable;
use GT\Dom\Exception\NodeListImmutableException;
use Iterator;
use Gt\PropFunc\MagicProp;
use Traversable;

/**
 * NodeList objects are collections of nodes, usually returned by properties
 * such as Node.childNodes and methods such as document.querySelectorAll().
 *
 * Although NodeList is not an Array, it is possible to iterate over it with
 * forEach().
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/API/NodeList
 *
 * @property-read int $length The number of nodes in the NodeList.
 *
 * @implements ArrayAccess<int, Node|Element>
 * @implements Iterator<int, Node|Element>
 */
class NodeList implements ArrayAccess, Countable, Iterator {
	use MagicProp;

	/** @var array<Node|Element> */
	private array $nodeList;
	/** @var callable():NodeList */
	private $callback;
	private int $iteratorKey;

	/**
	 * A NodeList can, confusingly, be both "live" OR "static" using the
	 * same class. To differentiate, PHP.Gt sets EITHER a $nodeList
	 * OR a $callback property. When a $nodeList is set, the list is deemed
	 * "static". When a $callback is set, the list is deemed "live" and
	 * behaves similarly to HTMLCollection (which is ALWAYS live).
	 * @see HTMLCollection
	 */
	public function __construct(Node|Element|Attr|Text|callable...$representation) {
		if(isset($representation[0]) && is_callable($representation[0])) {
			$this->callback = $representation[0];
		}
		else {
			$this->nodeList = $representation;
		}

		$this->iteratorKey = 0;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/NodeList/length */
	protected function __prop_get_length():int {
		return $this->count();
	}

// ArrayAccess functions:
	public function offsetExists(mixed $offset):bool {
		if(isset($this->nodeList)) {
			return isset($this->nodeList[$offset]);
		}

		/** @var NodeList $nodeArray */
		$nodeArray = call_user_func($this->callback);
		return isset($nodeArray[$offset]);
	}

	public function offsetGet(mixed $offset):null|Node|Element|Attr|Text {
		if(isset($this->nodeList)) {
			return $this->nodeList[$offset] ?? null;
		}

		/** @var NodeList $nodeList */
		$nodeList = call_user_func($this->callback);
		return $nodeList[$offset];
	}

	public function offsetSet(mixed $offset, mixed $value):void {
		throw new NodeListImmutableException();
	}

	public function offsetUnset(mixed $offset):void {
		throw new NodeListImmutableException();
	}
// End ArrayAccess functions.

// Countable functions:
	public function count():int {
		if(isset($this->nodeList)) {
			return count($this->nodeList);
		}

		/** @var NodeList $nodeArray */
		$nodeArray = call_user_func($this->callback);
		return count($nodeArray);
	}
// End Countable functions.

// Iterator functions:
	public function rewind():void {
		$this->iteratorKey = 0;
	}

	public function valid():bool {
		if(isset($this->nodeList)) {
			return isset($this->nodeList[$this->iteratorKey]);
		}

		/** @var NodeList|array<int, Node> $nodeList */
		$nodeList = call_user_func($this->callback);

		if(is_array($nodeList)) {
			$nodeList = NodeListFactory::create(...$nodeList);
		}

		while($nodeList->key() < $this->iteratorKey) {
			$nodeList->next();
		}

		return $nodeList->valid();
	}

	public function key():int {
		return $this->iteratorKey;
	}

	public function current():null|Node|Element|Attr|Text {
// TODO: Duplicated code with NodeList::valid() - refactor.
		if(isset($this->nodeList)) {
			return $this->nodeList[$this->iteratorKey];
		}

		/** @var NodeList|array<int, Node> $nodeList */
		$nodeList = call_user_func($this->callback);

		if(is_array($nodeList)) {
			$nodeList = NodeListFactory::create(...$nodeList);
		}

		while($nodeList->key() < $this->iteratorKey) {
			$nodeList->next();
		}

		return $nodeList->current();
	}

	public function next():void {
		$this->iteratorKey++;
	}
// End Iterator functions.

	/**
	 * Returns a node from a NodeList by index. This method doesn't throw
	 * exceptions as long as you provide arguments. A value of null is
	 * returned if the index is out of range, and a TypeError is thrown if
	 * no argument is provided.
	 *
	 * @param int $index
	 * @return null|Node
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/NodeList/item
	 */
	public function item(int $index):null|Node|Element|Attr|Text {
		if(isset($this->nodeList)) {
			if(isset($this->nodeList[$index])) {
				return $this->nodeList[$index];
			}

			return null;
		}

		/** @var NodeList $nodeArray */
		$nodeArray = call_user_func($this->callback);
		return $nodeArray[$index] ?? null;
	}

	/**
	 * The NodeList.entries() method returns an iterator allowing to go
	 * through all key/value pairs contained in this object. The values are
	 * Node objects.
	 *
	 * @return Traversable<int, Node>
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/NodeList/entries
	 */
	public function entries():Traversable {
		foreach($this as $key => $node) {
			yield $key => $node;
		}
	}

	/**
	 * The forEach() method of the NodeList interface calls the callback
	 * given in parameter once for each value pair in the list, in
	 * insertion order.
	 *
	 * @param callable $callback
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/NodeList/forEach
	 */
	public function forEach(callable $callback):void {
		foreach($this as $node) {
			call_user_func($callback, $node);
		}
	}

	/**
	 * The NodeList.keys() method returns an iterator allowing to go
	 * through all keys contained in this object. The keys are unsigned
	 * integer.
	 *
	 * @return Traversable<int>
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/NodeList/keys
	 */
	public function keys():Traversable {
		$this->rewind();
		while($this->valid()) {
			yield $this->key();
			$this->next();
		}
	}

	/**
	 * The NodeList.values() method returns an iterator allowing to go
	 * through all values contained in this object. The values are Node
	 * objects.
	 *
	 * @return Traversable<Node>
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/NodeList/values
	 */
	public function values():Traversable {
		foreach($this as $node) {
			yield $node;
		}
	}
}
