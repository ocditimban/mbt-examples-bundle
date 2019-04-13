<?php

namespace Tienvx\Bundle\MbtExamplesBundle\Tests;

use Tienvx\Bundle\MbtBundle\Subject\SubjectManager;
use Tienvx\Bundle\MbtExamplesBundle\Subject\ApiCart;
use Tienvx\Bundle\MbtExamplesBundle\Subject\Checkout;
use Tienvx\Bundle\MbtExamplesBundle\Subject\Product;
use Tienvx\Bundle\MbtExamplesBundle\Subject\ShoppingCart;

class SubjectTest extends TestCase
{
    /**
     * @var SubjectManager
     */
    protected $subjectManager;

    /**
     * @throws \Exception
     */
    protected function setUp()
    {
        parent::setUp();

        $this->subjectManager = self::$container->get(SubjectManager::class);
    }

    public function testSubjects()
    {
        $this->assertEquals(Checkout::class, $this->subjectManager->getSubject('checkout'));
        $this->assertEquals(Product::class, $this->subjectManager->getSubject('product'));
        $this->assertEquals(ShoppingCart::class, $this->subjectManager->getSubject('shopping_cart'));
        $this->assertEquals(ApiCart::class, $this->subjectManager->getSubject('api_cart'));
    }
}
