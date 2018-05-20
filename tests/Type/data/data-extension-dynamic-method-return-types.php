<?php

namespace DataExtensionDynamicMethodReturnTypesNamespace;

// SilverStripe
use DataObject;
use DataExtension;

class Foo extends DataObject
{
}

class FooTwo extends DataObject
{
}

class FooDataExtension extends DataExtension
{
	public function doFoo()
	{
		$owner = $this->getOwner();
		die;
	}
}
