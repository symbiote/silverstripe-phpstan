<?php

namespace DataObjectDynamicMethodReturnTypesNamespace;

use SiteTree;

class Foo
{
	public function doFoo()
	{
		$sitetree = new SiteTree();
		die;
	}
}
