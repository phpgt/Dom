<?php
namespace GT\Dom;

use Countable;

#[\AllowDynamicProperties]
class DOMStringMap implements Countable {
	/** @var callable */
	private $getterCallback;
	/** @var callable */
	private $setterCallback;

	/**
	 * @param callable $getterCallback Returns an associative array of
	 * key-value-pairs, no parameters.
	 * @param callable $setterCallback Takes an associative array of
	 * key-value-pairs as the only parameter.
	 */
	public function __construct(
		callable $getterCallback,
		callable $setterCallback
	) {
		$this->getterCallback = $getterCallback;
		$this->setterCallback = $setterCallback;
	}

	public function __get(string $name):?string {
		$name = $this->correctCamelCase($name);
		$keyValuePairs = call_user_func($this->getterCallback);
		return $keyValuePairs[$name] ?? null;
	}

	public function __set(string $name, string $value):void {
		$name = $this->correctCamelCase($name);
		$keyValuePairs = call_user_func($this->getterCallback);
		$keyValuePairs[$name] = $value;
		call_user_func($this->setterCallback, $keyValuePairs);
	}

	public function get(string $name):?string {
		return $this->__get($name);
	}

	public function set(string $name, string $value):void {
		$this->__set($name, $value);
	}

	public function count():int {
		$keyValuePairs = call_user_func($this->getterCallback);
		return count($keyValuePairs);
	}

	private function correctCamelCase(string $name):string {
		preg_match_all(
			'/((?:^|[A-Z])[0-9a-z\-]+)/',
			$name,
			$matches
		);

		$wordArray = [];
		foreach($matches[0] as $word) {
			array_push($wordArray, strtolower($word));
		}
		return implode("-", $wordArray);
	}
}
