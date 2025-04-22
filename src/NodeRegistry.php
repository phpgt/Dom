<?php
namespace Gt\Dom;

use Closure;
use Dom\Node as NativeNode;
use SplObjectStorage;

class NodeRegistry {
	private SplObjectStorage $storage;

	public function __construct(private readonly Closure $wrapCallback) {
		$this->storage = new SplObjectStorage();
	}

	public function contains(NativeNode $native):bool {
		return $this->storage->contains($native);
	}

	public function get(NativeNode $native):null|Node|Element|HTMLElement {
		if(!$this->contains($native)) {
			$wrapped = call_user_func($this->wrapCallback, $native);
			$this->attach($native, $wrapped);
			return $wrapped;
		}

		return $this->storage->offsetGet($native);
	}

	public function attach(NativeNode $native, Node $node):void {
		$this->storage->attach($native, $node);
	}

	public function detach(NativeNode $native):void {
		$this->storage->detach($native);
	}
}
