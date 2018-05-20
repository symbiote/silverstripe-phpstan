<?php

namespace DataExtensionUnionDynamicMethodReturnTypesNamespace;

// SilverStripe
use DataObject;
use DataExtension;

class Foo extends DataObject
{
}

class FooTwo extends DataObject
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
