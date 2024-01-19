<?php

namespace App\Tests;

use App\Service\CensuratorService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CensuratorTest extends KernelTestCase
{

    public function testPurify(): void
    {
        $kernel = self::bootKernel();
        $container = static::getContainer();
        /**
         * @var CensuratorService $censuratorService
         */
        $censuratorService = $container->get(CensuratorService::class);
        $input = "contraire";
        $actual = $censuratorService->purify($input);
        $excpected = "contraire";

        $this->assertEquals($excpected,$actual);
    }
}
