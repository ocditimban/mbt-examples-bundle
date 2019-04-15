<?php

namespace Tienvx\Bundle\MbtExamplesBundle\Subject;

use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Exception;
use Symfony\Component\Panther\Client;
use Tienvx\Bundle\MbtBundle\Annotation\DataProvider;
use Tienvx\Bundle\MbtBundle\Subject\AbstractSubject;

class MobileHome extends AbstractSubject
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var bool
     */
    protected $cartOpen = false;

    /**
     * @var string
     */
    protected $url = 'http://example.com';

    /**
     * @var array
     */
    protected $cart;

    /**
     * @var array
     */
    protected $products = [
        '43', // 'MacBook',
        '40', // 'iPhone',
        '42', // 'Apple Cinema 30',
        '30', // 'Canon EOS 5D',
    ];

    public function __construct()
    {
        $this->cart = [];
    }

    public static function support(): string
    {
        return 'mobile_home';
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function setUp()
    {
        if (!$this->testingModel) {
            // # http://localhost:4444/grid/console
            $caps = new DesiredCapabilities();
            $caps->setCapability(WebDriverCapabilityType::BROWSER_NAME, WebDriverBrowserType::CHROME);
            $caps->setCapability('platformName', 'Android');
            $caps->setCapability('deviceName', 'Samsung Galaxy S7 Edge');
            $caps->setCapability('browserVersion', '9.0');
            $this->client = Client::createSeleniumClient('http://hub:4444/wd/hub', $caps);
        }
        $this->goToHome();
    }

    public function tearDown()
    {
        if (!$this->testingModel) {
            $this->client->quit();
        }
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    private function goToHome()
    {
        if (!$this->testingModel) {
            $this->client->get($this->url);
            $this->client->waitFor('#common-home', 30);
        }
    }

    /**
     * @throws Exception
     * @DataProvider(method="getRandomProductFromHomeCanBeAddDirectly")
     */
    public function add()
    {
        if (empty($this->data['product'])) {
            throw new Exception('Can not add product to cart: product is not selected');
        }
        $product = $this->data['product'];
        if (!isset($this->cart[$product])) {
            $this->cart[$product] = 1;
        } else {
            ++$this->cart[$product];
        }
        if (!$this->testingModel) {
            $by = WebDriverBy::cssSelector("button[onclick*=\"cart.add('$product'\"]");
            $this->client->wait(3)->until(
                WebDriverExpectedCondition::elementToBeClickable($by)
            );
            $element = $this->client->findElement($by);
            $element->click();
        }
    }

    /**
     * @throws Exception
     * @DataProvider(method="getRandomProductFromHome")
     */
    public function wish()
    {
        if (empty($this->data['product'])) {
            throw new Exception('Can not add product to wish list: product is not selected');
        }
        $product = $this->data['product'];
        if (!$this->testingModel) {
            $by = WebDriverBy::cssSelector("button[onclick*=\"wishlist.add('$product'\"]");
            $this->client->wait(3)->until(
                WebDriverExpectedCondition::elementToBeClickable($by)
            );
            $element = $this->client->findElement($by);
            $element->click();
            $this->closeAlerts();
        }
    }

    /**
     * @throws Exception
     * @DataProvider(method="getRandomProductFromHome")
     */
    public function compare()
    {
        if (empty($this->data['product'])) {
            throw new Exception('Can not add product to product comparision: product is not selected');
        }
        $product = $this->data['product'];
        if (!$this->testingModel) {
            $by = WebDriverBy::cssSelector("button[onclick*=\"compare.add('$product'\"]");
            $this->client->wait(3)->until(
                WebDriverExpectedCondition::elementToBeClickable($by)
            );
            $element = $this->client->findElement($by);
            $element->click();
            $this->closeAlerts();
        }
    }

    /**
     * @throws Exception
     */
    public function openCart()
    {
        if (!$this->testingModel) {
            $by = WebDriverBy::cssSelector('#cart button');
            $this->client->wait(3)->until(
                WebDriverExpectedCondition::elementToBeClickable($by)
            );
            $element = $this->client->findElement($by);
            $element->click();
            $this->closeAlerts();
        }
        $this->cartOpen = true;
    }

    /**
     * @throws Exception
     */
    public function closeCart()
    {
        if (!$this->testingModel) {
            $by = WebDriverBy::cssSelector('#cart button');
            $this->client->wait(3)->until(
                WebDriverExpectedCondition::elementToBeClickable($by)
            );
            $element = $this->client->findElement($by);
            $element->click();
        }
        $this->cartOpen = false;
    }

    /**
     * @throws Exception
     * @DataProvider(method="getRandomProductFromCart")
     */
    public function remove()
    {
        if (empty($this->data['product'])) {
            throw new Exception('Can not remove product from cart: product is not selected');
        }
        $product = $this->data['product'];
        unset($this->cart[$product]);
        if (!$this->testingModel) {
            // TODO id is not product id
            $by = WebDriverBy::cssSelector("button[onclick*=\"cart.remove('$product'\"]");
            $this->client->wait(3)->until(
                WebDriverExpectedCondition::elementToBeClickable($by)
            );
            $element = $this->client->findElement($by);
            $element->click();
        }
    }

    public function productHasBeenSelected()
    {
        return !empty($this->data['product']);
    }

    public function cartHasProducts()
    {
        return !empty($this->cart);
    }

    public function cartIsOpen()
    {
        return $this->cartOpen;
    }

    public function getRandomProductFromHome()
    {
        $product = $this->products[array_rand($this->products)];

        return ['product' => $product];
    }

    public function getRandomProductFromHomeCanBeAddDirectly()
    {
        do {
            $product = $this->products[array_rand($this->products)];
        } while (42 == $product);

        return ['product' => $product];
    }

    public function getRandomProductFromCart()
    {
        if (empty($this->cart)) {
            return null;
        }
        $product = array_rand($this->cart);

        return ['product' => $product];
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function closeAlerts()
    {
        if (!$this->testingModel) {
            $this->client->waitFor('.alert', 3);
            /** @var WebDriverElement[] $elements */
            $elements = $this->client->findElements(WebDriverBy::cssSelector('.alert > .close'));
            foreach ($elements as $element) {
                $element->click();
            }
        }
    }

    public function getScreenshotUrl($bugId, $index)
    {
        return sprintf('http://localhost/mbt-api/bug-screenshot/%d/%d', $bugId, $index);
    }
}
