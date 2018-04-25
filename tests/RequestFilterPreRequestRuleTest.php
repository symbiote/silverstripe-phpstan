<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan\Tests;

use SilbinaryWolf\SilverstripePHPStan\RequestFilterPreRequestRule;

use PHPStan\Rules\Rule;

class RequestFilterPreRequestRuleTest extends \PHPStan\Testing\RuleTestCase
{

    protected function getRule(): Rule
    {
        return new RequestFilterPreRequestRule($this->createBroker());
    }

    public function testRequestFilterGood(): void
    {
        $this->analyse([__DIR__ . '/RequestFilterGood.php'], []);
    }

    public function testRequestFilterBad(): void
    {
        $this->analyse([__DIR__ . '/RequestFilterBad.php'], [
            [
                'RequestFilter::preRequest() should not return false as this will cause an uncaught "Invalid Request" exception to be thrown by the SilverStripe framework. (returning "null" will not cause this problem)',
                22,
            ],
        ]);
    }

    public function analyse(array $files, array $expectedErrors)
    {
        foreach ($files as $file) {
            include_once($file);
        }
        parent::analyse($files, $expectedErrors);
    }
}
