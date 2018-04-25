<?php

namespace DataExtensionDynamicMethodReturnTypesNamespace;

// SilverStripe
use DataObject;
use DataExtension;

class Foo Extends DataObject
{
}

class FooTwo Extends DataObject
{
}

class FooDataExtension Extends DataExtension
{
	public function doFoo()
	{
		$owner = $this->getOwner();
		die;
	}
}
