<?php

namespace DataObjectDynamicMethodReturnTypesNamespace;

use SilverStripe\CMS\Model\SiteTree;

class Foo
{
	public function doFoo()
	{
		$sitetree = new SiteTree();
		die;
	}
}
