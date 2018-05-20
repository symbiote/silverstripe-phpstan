<?php

namespace DataListDynamicMethodReturnTypesNamespace;

use SiteTree;

class Foo
{
	public function doFoo()
	{
		$siteTreeDataList = SiteTree::get();
		die;
	}
}
