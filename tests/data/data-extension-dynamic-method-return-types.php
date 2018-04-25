<?php

namespace DataObjectDynamicMethodReturnTypesNamespace;

use DataExtension;

class FooDataExtension Extends DataExtension
{
	public function doFoo()
	{
		$owner = $this->getOwner();
		die;
	}
}
