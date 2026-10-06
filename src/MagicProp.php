<?php
namespace GT\Dom;

use GT\PropFunc\MagicProp as PropertyAccess;

/** Preserve the accessor names used by DOM classes and their subclasses. */
trait MagicProp {
	use PropertyAccess {
		getMagicPropMethod as private getDefaultMagicPropMethod;
	}

	private function getMagicPropMethod(
		string $name,
		string $action = "get"
	):string {
		$legacyMethod = "__prop_{$action}_{$name}";
		if(method_exists($this, $legacyMethod)) {
			return $legacyMethod;
		}

		return $this->getDefaultMagicPropMethod($name, $action);
	}
}
