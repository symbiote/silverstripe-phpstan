<?php

namespace DataListDynamicMethodReturnTypesNamespace;

use SilverStripe\CMS\Model\SiteTree;

class Foo
{
	public function doFoo()
	{
		$siteTreeDataList = SiteTree::get();
		die;
	}
}
