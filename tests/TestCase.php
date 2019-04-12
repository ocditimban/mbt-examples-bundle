<?php

namespace Tienvx\Bundle\MbtExamplesBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class TestCase extends KernelTestCase
{
    /**
     * @var Application
     */
    protected $application;

    protected function setUp()
    {
        $this->application = $this->getApplication();
    }

    protected function getApplication()
    {
        $kernel = static::bootKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);

        return $application;
    }
}
