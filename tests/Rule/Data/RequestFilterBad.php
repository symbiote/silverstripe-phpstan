<?php

namespace Symbiote\SilverstripePHPStan\Tests\Rule\Data;

// SilverStripe
use SilverStripe\Control\RequestFilter;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;

class RequestFilterBad implements RequestFilter
{
    public function postRequest(HTTPRequest $request, HTTPResponse $response)
    {
    }

    public function preRequest(HTTPRequest $request)
    {
        $badResult = false;
        return $badResult;
    }
}
