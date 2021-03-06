<?php

namespace Tienvx\Bundle\MbtExamplesBundle\Subject;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverPlatform;
use Symfony\Component\Process\Process;
use Tienvx\Bundle\MbtExamplesBundle\Helper\ElementHelper;
use Exception;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Symfony\Component\Panther\Client;
use Tienvx\Bundle\MbtBundle\Annotation\DataProvider;
use Tienvx\Bundle\MbtBundle\Subject\AbstractSubject;

class ShoppingCart extends AbstractSubject
{
    use ElementHelper;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $url = 'http://example.com';

    /**
     * @var array
     */
    protected $cart;

    /**
     * @var string Current viewing product
     */
    protected $product;

    /**
     * @var string Current viewing category
     */
    protected $category;

    /**
     * @var array
     */
    protected $featuredProducts = [
        '43', // 'MacBook',
        '40', // 'iPhone',
        '42', // 'Apple Cinema 30',
        '30', // 'Canon EOS 5D',
    ];

    /**
     * @var array
     */
    protected $categories = [
        '20', // 'Desktops',
        '20_27', // 'Mac',
        '18', // 'Laptops & Notebooks',
        '25', // 'Components',
        '25_28', // 'Monitors',
        '57', // 'Tablets',
        '17', // 'Software',
        '24', // 'Phones & PDAs',
        '33', // 'Cameras',
        '34', // 'MP3 Players',
    ];

    /**
     * @var array
     */
    protected $productsInCategory = [
        '20' => [
            '42', // 'Apple Cinema 30',
            '30', // 'Canon EOS 5D',
            '47', // 'HP LP3065',
            '28', // 'HTC Touch HD',
            '40', // 'iPhone',
            '48', // 'iPod Classic',
            '43', // 'MacBook',
            '44', // 'MacBook Air',
            '29', // 'Palm Treo Pro',
            '35', // 'Product 8',
            '33', // 'Samsung SyncMaster 941BW',
            '46', // 'Sony VAIO',
        ],
        '20_27' => [
            '41', // 'iMac',
        ],
        '18' => [
            '47', // 'HP LP3065',
            '43', // 'MacBook',
            '44', // 'MacBook Air',
            '45', // 'MacBook Pro',
            '46', // 'Sony VAIO',
        ],
        '25' => [],
        '25_28' => [
            '42', // 'Apple Cinema 30',
            '33', // 'Samsung SyncMaster 941BW'
        ],
        '57' => [
            '49', // 'Samsung Galaxy Tab 10.1',
        ],
        '17' => [],
        '24' => [
            '28', // 'HTC Touch HD',
            '40', // 'iPhone',
            '29', // 'Palm Treo Pro',
        ],
        '33' => [
            '30', // 'Canon EOS 5D',
            '31', // 'Nikon D300'
        ],
        '34' => [
            '48', // 'iPod Classic',
            '36', // 'iPod Nano',
            '34', // 'iPod Shuffle',
            '32', // 'iPod Touch',
        ],
    ];

    /**
     * @var array
     */
    protected $outOfStock = [
        '49', // 'Samsung Galaxy Tab 10.1',
    ];

    /**
     * @var array
     */
    protected $needOptions = [
        '42', // 'Apple Cinema 30',
        '30', // 'Canon EOS 5D',
        '35', // 'Product 8',
    ];

    public function __construct()
    {
        $this->cart = [];
        $this->category = null;
        $this->product = null;
    }

    public static function support(): string
    {
        return 'shopping_cart';
    }

    public function setUp()
    {
        if (!$this->testingModel) {
            $caps = new DesiredCapabilities();
            $caps->setCapability(WebDriverCapabilityType::BROWSER_NAME, WebDriverBrowserType::CHROME);
            $caps->setCapability('platformName', WebDriverPlatform::LINUX);
            $caps->setCapability('browserVersion', '73.0.3683.86');
            $options = new ChromeOptions();
            $options->addArguments(['--headless', '--window-size=1200,1100', '--disable-gpu']);
            $caps->setCapability(ChromeOptions::CAPABILITY, $options);
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
     * @throws Exception
     * @DataProvider(method="getRandomCategory")
     */
    public function viewAnyCategoryFromHome()
    {
        if (empty($this->data['category'])) {
            throw new Exception('Can not view category from home: category is not selected');
        }
        $category = $this->data['category'];
        $this->category = $category;
        $this->product = null;
        if (!$this->testingModel) {
            $this->goToCategory($category);
        }
    }

    /**
     * @throws Exception
     * @DataProvider(method="getRandomCategory")
     */
    public function viewOtherCategory()
    {
        if (empty($this->data['category'])) {
            throw new Exception('Can not view category from other category: category is not selected');
        }
        $category = $this->data['category'];
        $this->category = $category;
        $this->product = null;
        if (!$this->testingModel) {
            $this->goToCategory($category);
        }
    }

    /**
     * @throws Exception
     * @DataProvider(method="getRandomCategory")
     */
    public function viewAnyCategoryFromProduct()
    {
        if (empty($this->data['category'])) {
            throw new Exception('Can not view category from product: category is not selected');
        }
        $category = $this->data['category'];
        $this->category = $category;
        $this->product = null;
        if (!$this->testingModel) {
            $this->goToCategory($category);
        }
    }

    /**
     * @throws Exception
     * @DataProvider(method="getRandomCategory")
     */
    public function viewAnyCategoryFromCart()
    {
        if (empty($this->data['category'])) {
            throw new Exception('Can not view category from cart: category is not selected');
        }
        $category = $this->data['category'];
        $this->category = $category;
        $this->product = null;
        if (!$this->testingModel) {
            $this->goToCategory($category);
        }
    }

    /**
     * @throws Exception
     * @DataProvider(method="getRandomProductFromHome")
     */
    public function viewProductFromHome()
    {
        if (empty($this->data['product'])) {
            throw new Exception('Can not view product from home: product is not selected');
        }
        $product = $this->data['product'];
        $this->product = $product;
        $this->category = null;
        $this->goToProduct($product);
    }

    /**
     * @throws Exception
     * @DataProvider(method="getRandomProductFromCart")
     */
    public function viewProductFromCart()
    {
        if (empty($this->data['product'])) {
            throw new Exception('Can not view product from cart: product is not selected');
        }
        $product = $this->data['product'];
        $this->product = $product;
        $this->category = null;
        $this->goToProduct($product);
    }

    /**
     * @throws Exception
     * @DataProvider(method="getRandomProductFromCategory")
     */
    public function viewProductFromCategory()
    {
        if (empty($this->data['product'])) {
            throw new Exception('Can not view product from category: product is not selected');
        }
        $product = $this->data['product'];
        $this->product = $product;
        $this->category = null;
        $this->goToProduct($product);
    }

    /**
     * @throws Exception
     */
    public function categoryHasSelectedProduct()
    {
        if (empty($this->productsInCategory[$this->category])) {
            return false;
        } else {
            if (empty($this->data['product'])) {
                throw new Exception('Can not check if category has selected product or not: product is not selected');
            }
            $product = $this->data['product'];

            return in_array($product, $this->productsInCategory[$this->category]);
        }
    }

    public function productHasBeenSelected()
    {
        return !empty($this->data['product']);
    }

    public function categoryHasBeenSelected()
    {
        return !empty($this->data['category']);
    }

    public function viewCartFromHome()
    {
        $this->category = null;
        $this->product = null;
        $this->goToCart();
    }

    public function viewCartFromCategory()
    {
        $this->category = null;
        $this->product = null;
        $this->goToCart();
    }

    public function viewCartFromProduct()
    {
        $this->category = null;
        $this->product = null;
        $this->goToCart();
    }

    public function viewCartFromCheckout()
    {
        $this->category = null;
        $this->product = null;
        $this->goToCart();
    }

    public function checkoutFromHome()
    {
        $this->category = null;
        $this->product = null;
        $this->goToCheckout();
    }

    public function checkoutFromCategory()
    {
        $this->category = null;
        $this->product = null;
        $this->goToCheckout();
    }

    public function checkoutFromProduct()
    {
        $this->category = null;
        $this->product = null;
        $this->goToCheckout();
    }

    public function checkoutFromCart()
    {
        $this->category = null;
        $this->product = null;
        $this->goToCheckout();
    }

    public function backToHomeFromCategory()
    {
        $this->category = null;
        $this->product = null;
        $this->goToHome();
    }

    public function backToHomeFromProduct()
    {
        $this->category = null;
        $this->product = null;
        $this->goToHome();
    }

    public function backToHomeFromCart()
    {
        $this->category = null;
        $this->product = null;
        $this->goToHome();
    }

    public function backToHomeFromCheckout()
    {
        $this->category = null;
        $this->product = null;
        $this->goToHome();
    }

    /**
     * @throws Exception
     */
    public function cartHasSelectedProduct()
    {
        if (empty($this->cart)) {
            return false;
        } else {
            if (empty($this->data['product'])) {
                throw new Exception('Can not check if cart has selected product or not: product is not selected');
            }
            $product = $this->data['product'];

            if (!$this->testingModel) {
                return $this->hasElement($this->client, WebDriverBy::cssSelector("#checkout-cart button[onclick=\"cart.remove('$product');\"]"));
            }

            return !empty($this->cart[$product]);
        }
    }

    /**
     * @throws Exception
     * @DataProvider(method="getRandomProductFromHome")
     */
    public function addFromHome()
    {
        if (empty($this->data['product'])) {
            throw new Exception('Can not add product from home: product is not selected');
        }
        $product = $this->data['product'];
        if (!$this->testingModel) {
            if (in_array($product, $this->needOptions)) {
                throw new Exception('You need to specify options for this product! Can not add product');
            }
        }
        if (!isset($this->cart[$product])) {
            $this->cart[$product] = 1;
        } else {
            ++$this->cart[$product];
        }
    }

    /**
     * @throws Exception
     * @DataProvider(method="getRandomProductFromCategory")
     */
    public function addFromCategory()
    {
        if (empty($this->data['product'])) {
            throw new Exception('Can not add product from category: product is not selected');
        }
        $product = $this->data['product'];
        if (!$this->testingModel) {
            if (in_array($product, $this->needOptions)) {
                throw new Exception('You need to specify options for this product! Can not add product');
            }
        }
        if (!isset($this->cart[$product])) {
            $this->cart[$product] = 1;
        } else {
            ++$this->cart[$product];
        }
        if (!$this->testingModel) {
            $by = WebDriverBy::cssSelector("button[onclick*=\"cart.add('$product'\"]");
            $this->client->wait(1)->until(
                WebDriverExpectedCondition::elementToBeClickable($by)
            );
            $element = $this->client->findElement($by);
            $element->click();
        }
    }

    /**
     * @throws Exception
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function addFromProduct()
    {
        if (!$this->testingModel) {
            if (in_array($this->product, $this->needOptions)) {
                throw new Exception('You need to specify options for this product! Can not add product');
            }
        }
        if (!isset($this->cart[$this->product])) {
            $this->cart[$this->product] = 1;
        } else {
            ++$this->cart[$this->product];
        }
        if (!$this->testingModel) {
            $by = WebDriverBy::id('button-cart');
            $this->client->wait(1)->until(
                WebDriverExpectedCondition::elementToBeClickable($by)
            );
            $element = $this->client->findElement($by);
            $element->click();
        }
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
            $this->client->findElement(WebDriverBy::cssSelector("button[onclick=\"cart.remove('$product');\"]"))->click();
        }
    }

    /**
     * @throws Exception
     * @DataProvider(method="getRandomProductFromCart")
     */
    public function update()
    {
        if (empty($this->data['product'])) {
            throw new Exception('Can not update product in cart: product is not selected');
        }
        $product = $this->data['product'];
        $quantity = rand(1, 99);
        $this->cart[$product] = $quantity;
        if (!$this->testingModel) {
            // TODO id is not product id
            $this->client->findElement(WebDriverBy::cssSelector("input[name=\"quantity[$product]\"]"))->sendKeys($quantity);
            $this->client->findElement(WebDriverBy::cssSelector("input[name=\"quantity[$product]\"]+.input-group-btn>button[data-original-title=\"Update\"]"))->click();
        }
    }

    public function home()
    {
    }

    public function category()
    {
    }

    public function product()
    {
    }

    public function cart()
    {
    }

    /**
     * @throws Exception
     */
    public function checkout()
    {
        if (!$this->testingModel) {
            foreach ($this->cart as $product => $quantity) {
                if (in_array($product, $this->outOfStock)) {
                    throw new Exception('You added an out-of-stock product into cart! Can not checkout');
                }
            }
        }
    }

    public function getRandomProductFromHome()
    {
        if (empty($this->featuredProducts)) {
            return null;
        }
        $product = $this->featuredProducts[array_rand($this->featuredProducts)];

        return ['product' => $product];
    }

    public function getRandomCategory()
    {
        if (empty($this->categories)) {
            return null;
        }
        $category = $this->categories[array_rand($this->categories)];

        return ['category' => $category];
    }

    public function getRandomProductFromCart()
    {
        if (empty($this->cart)) {
            return null;
        }
        $product = array_rand($this->cart);

        return ['product' => $product];
    }

    public function getRandomProductFromCategory()
    {
        if (!isset($this->productsInCategory[$this->category])) {
            return null;
        }
        $products = $this->productsInCategory[$this->category];
        if (empty($products)) {
            return null;
        }
        $product = $products[array_rand($products)];

        return ['product' => $product];
    }

    public function hasCoupon()
    {
        return true;
    }

    public function hasGiftCertificate()
    {
        return true;
    }

    private function goToCategory($id)
    {
        if (!$this->testingModel) {
            $this->client->get($this->url."/index.php?route=product/category&path=$id");
        }
    }

    /**
     * @param $id
     *
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    private function goToProduct($id)
    {
        if (!$this->testingModel) {
            $this->client->get($this->url."/index.php?route=product/product&product_id=$id");
            $this->client->waitFor('#product-product', 1);
        }
    }

    private function goToCart()
    {
        if (!$this->testingModel) {
            $this->client->get($this->url.'/index.php?route=checkout/cart');
        }
    }

    private function goToCheckout()
    {
        if (!$this->testingModel) {
            $this->client->get($this->url.'/index.php?route=checkout/checkout');
        }
    }

    private function goToHome()
    {
        if (!$this->testingModel) {
            $this->client->get($this->url.'/index.php?route=common/home');
        }
    }

    public function captureScreenshot($bugId, $index)
    {
        $this->client->takeScreenshot('/tmp/screenshot.png');

        $process = Process::fromShellCommandline('pngquant --quality=60-90 - < /tmp/screenshot.png');
        $process->run();

        $image = $process->getOutput();
        $this->filesystem->put("{$bugId}/{$index}.png", $image);

        unlink('/tmp/screenshot.png');
    }

    public function getScreenshotUrl($bugId, $index)
    {
        return sprintf('http://localhost/mbt-api/bug-screenshot/%d/%d', $bugId, $index);
    }
}
