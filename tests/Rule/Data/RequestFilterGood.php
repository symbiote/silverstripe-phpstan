<?php

namespace SilbinaryWolf\SilverstripePHPStan\Tests\Rule\Data;

// SilverStripe
use SilverStripe\Control\RequestFilter;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;

class RequestFilterGood implements RequestFilter
{
    public function postRequest(HTTPRequest $request, HTTPResponse $response)
    {
        if (true) {
            return 0;
        }
        if (true) {
            return null;
        }
        $obj = new \stdClass;
        return $obj;
    }

    public function preRequest(HTTPRequest $request)
    {
        if (true) {
            return 0;
        }
        if (true) {
            return null;
        }
        $obj = new \stdClass;
        return $obj;
    }
}
