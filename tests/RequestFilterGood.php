<?php

namespace PHPStan;

// SilverStripe
use RequestFilter;
use SS_HTTPRequest;
use SS_HTTPResponse;
use DataModel;
use Session;

class RequestFilterGood implements RequestFilter
{
    public function postRequest(SS_HTTPRequest $request, SS_HTTPResponse $response, DataModel $model)
    {
        if (true) {
            return 0;
        }
        if (true) {
            return null;
        }
        $obj = new stdClass;
        return $obj;
    }

    public function preRequest(SS_HTTPRequest $request, Session $session, DataModel $model)
    {
        if (true) {
            return 0;
        }
        if (true) {
            return null;
        }
        $obj = new stdClass;
        return $obj;
    }
}
