<?php
namespace GT\Dom;

use ArrayAccess;
use Countable;
use GT\Dom\Exception\HTMLCollectionImmutableException;
use Gt\PropFunc\MagicProp;
use Iterator;

/**
 * The HTMLCollection interface represents a generic collection (array-like
 * object similar to arguments) of elements (in document order) and offers
 * methods and properties for selecting from the list.
 *
 * Note: This interface is called HTMLCollection for historical reasons (before
 * the modern DOM, collections implementing this interface could only have HTML
 * elements as their items).
 *
 * An HTMLCollection in the HTML DOM is live; it is automatically updated when
 * the underlying document is changed.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLCollection
 * @see HTMLCollectionFactory
 *
 * @property-read int $length
 * @implements ArrayAccess<int, Element>
 * @implements Iterator<int, Element>
 */
class HTMLCollection implements ArrayAccess, Countable, Iterator {
	use MagicProp;

	/** @var callable */
	private $callback;
	private int $iteratorIndex;

	protected function __construct(callable $callback) {
		$this->callback = $callback;
		$this->iteratorIndex = 0;
	}

	/** @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLCollection/length */
	public function __prop_get_length():int {
		return $this->count();
	}

// ArrayAccess functions:

	/** @param int $offset */
	public function offsetExists(mixed $offset):bool {
		return !is_null($this->item($offset));
	}

	/** @param int $offset */
	public function offsetGet(mixed $offset):?Element {
		return $this->item($offset);
	}

	public function offsetSet(mixed $offset, mixed $value):void {
		throw new HTMLCollectionImmutableException();
	}

	public function offsetUnset(mixed $offset):void {
		throw new HTMLCollectionImmutableException();
	}
// End of ArrayAccess functions.

// Countable functions:
	public function count():int {
		$nodeList = call_user_func($this->callback);
		return count($nodeList);
	}
// End of Countable functions.

// Iterator functions:
	public function rewind():void {
		$this->iteratorIndex = 0;
	}

	public function valid():bool {
		return $this->offsetExists($this->iteratorIndex);
	}

	public function key():int {
		return $this->iteratorIndex;
	}

	public function current():?Element {
		return $this->offsetGet($this->iteratorIndex);
	}

	public function next():void {
		$this->iteratorIndex++;
	}
// End of Iterator functions.

	/**
	 * The HTMLCollection method item() returns the node located at the
	 * specified offset into the collection.
	 *
	 * @param int $index The position of the Node to be returned. Elements
	 * appear in an HTMLCollection in the same order in which they appear
	 * in the document's source.
	 * @return ?Element The Element at the specified index, or null if index
	 * is less than zero or greater than or equal to the length property.
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLCollection/item
	 */
	public function item(int $index):?Element {
		/** @var NodeList $nodeList */
		$nodeList = call_user_func($this->callback);
		$item = $nodeList->item($index);

		if($item instanceof Element) {
			return $item;
		}

		return null;
	}

	/**
	 * Returns the specific node whose ID or, as a fallback, name matches
	 * the string specified by name. Matching by name is only done as a last
	 * resort, only in HTML, and only if the referenced element supports the
	 * name attribute. Returns null if no node exists by the given name.
	 *
	 * An alternative to accessing collection[name] (which instead returns
	 * undefined when name does not exist). This is mostly useful for
	 * non-JavaScript DOM implementations.
	 *
	 * @param string $nameOrId
	 * @return Element|RadioNodeList|null
	 * @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLCollection/namedItem
	 */
	public function namedItem(string $nameOrId):Element|RadioNodeList|null {
		/** @var array<string, array<Element>> $matches */
		$matches = [
			"id" => [],
			"name" => [],
		];

		foreach(array_keys($matches) as $attribute) {
			foreach($this as $element) {
				if($element->getAttribute($attribute) === $nameOrId) {
					array_push($matches[$attribute], $element);
				}
			}
		}

		if(isset($matches["id"][0])) {
			return $matches["id"][0];
		}

		$count = count($matches["name"]);
		if($count === 0) {
			return null;
		}
		elseif($count === 1) {
			return $matches["name"][0];
		}
		else {
			return NodeListFactory::createRadioNodeList(
				fn() => $matches["name"]
			);
		}
	}
}
