<?php

namespace PHPStan;

// SilverStripe
use RequestFilter;
use SS_HTTPRequest;
use SS_HTTPResponse;
use DataModel;
use Session;

class RequestFilterBad implements RequestFilter
{
    public function postRequest(SS_HTTPRequest $request, SS_HTTPResponse $response, DataModel $model)
    {
    }

    public function preRequest(SS_HTTPRequest $request, Session $session, DataModel $model)
    {
        $badResult = false;
        return $badResult;
    }
}
