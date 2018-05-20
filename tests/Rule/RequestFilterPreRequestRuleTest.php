<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan\Tests\Rule;

use SilbinaryWolf\SilverstripePHPStan\Rule\RequestFilterPreRequestRule;
use SilbinaryWolf\SilverstripePHPStan\Tests\ResolverTest;
use PHPStan\Rules\Rule;

class RequestFilterPreRequestRuleTest extends \PHPStan\Testing\RuleTestCase
{

    protected function getRule(): Rule
    {
        return new RequestFilterPreRequestRule();
    }

    public function testRequestFilterGood(): void
    {
        $this->analyse([__DIR__ . '/Data/RequestFilterGood.php'], []);
    }

    public function testRequestFilterBad(): void
    {
        $this->analyse([__DIR__ . '/Data/RequestFilterBad.php'], [
            [
                'RequestFilter::preRequest() should not return false as this will cause an uncaught "Invalid Request" exception to be thrown by the SilverStripe framework. (returning "null" will not cause this problem)',
                21,
            ],
        ]);
    }
}
