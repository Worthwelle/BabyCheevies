<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    function setUp()
    {
        parent::setUp();
        config(['app.url' => 'https://localhost']);
    }
}
