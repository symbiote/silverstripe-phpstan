<?php

namespace DataExtensionUnionDynamicMethodReturnTypesNamespace;

// SilverStripe
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DataExtension;

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
