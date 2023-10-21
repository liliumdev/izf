<?php

namespace Tests;

use ReflectionClass;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpLocalTraits();
    }

    /**
     * Automatically sets up all With* traits in the Tests namespace.
     */
    protected function setUpLocalTraits(): void
    {
        collect(array_flip(class_uses_recursive(static::class)))
            ->transform(function ($className) {
                return new ReflectionClass($className);
            })
            ->filter(function ($class) {
                return $class->getNamespaceName() === 'Tests\\Concerns' &&
                    substr($class->getShortName(), 0, 4) === 'With';
            })
            ->each(function ($class): void {
                $this->{"setUp{$class->getShortName()}"}();
            });
    }
}
