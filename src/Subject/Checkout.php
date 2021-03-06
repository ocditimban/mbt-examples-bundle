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
use Facebook\WebDriver\Exception\UnexpectedTagNameException;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverSelect;
use Symfony\Component\Panther\Client;
use Tienvx\Bundle\MbtBundle\Subject\AbstractSubject;

class Checkout extends AbstractSubject
{
    use ElementHelper;

    /**
     * @var int
     */
    protected $productId = 47;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $url = 'http://example.com';

    /**
     * @var bool
     */
    protected $loggedIn = false;

    /**
     * @var bool
     */
    protected $guestCheckout = false;

    /**
     * @var bool
     */
    protected $registerAccount = false;

    public static function support(): string
    {
        return 'checkout';
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

    public function loggedIn()
    {
        return $this->loggedIn;
    }

    public function doingGuestCheckout()
    {
        return $this->guestCheckout;
    }

    public function doingRegisterAccount()
    {
        return $this->registerAccount;
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function login()
    {
        if (!$this->testingModel) {
            // Email
            $by = WebDriverBy::id('input-email');
            $this->client->wait(1)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated($by)
            );
            $element = $this->client->findElement($by);
            $element->sendKeys('test@example.com');
            // Password
            $by = WebDriverBy::id('input-password');
            $this->client->wait(1)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated($by)
            );
            $element = $this->client->findElement($by);
            $element->sendKeys('1234');
            // Submit
            $by = WebDriverBy::id('button-login');
            $this->client->wait(1)->until(
                WebDriverExpectedCondition::elementToBeClickable($by)
            );
            $element = $this->client->findElement($by);
            $element->click();

            $by = WebDriverBy::cssSelector('#collapse-payment-address .panel-body :first-child');
            $this->waitUntilVisibilityOfElementLocated($by);
        }

        $this->loggedIn = true;
    }

    /**
     * @throws TimeOutException
     */
    public function guestCheckout()
    {
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::xpath("//input[@name='account' and @value='guest']"))->click();
            $this->client->findElement(WebDriverBy::id('button-account'))->click();

            $by = WebDriverBy::cssSelector('#collapse-payment-address .panel-body :first-child');
            $this->waitUntilVisibilityOfElementLocated($by);
        }

        $this->guestCheckout = true;
    }

    /**
     * @throws TimeOutException
     */
    public function registerAccount()
    {
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::xpath("//input[@name='account' and @value='register']"))->click();
            $this->client->findElement(WebDriverBy::id('button-account'))->click();

            $by = WebDriverBy::cssSelector('#collapse-payment-address .panel-body :first-child');
            $this->waitUntilVisibilityOfElementLocated($by);
        }

        $this->registerAccount = true;
    }

    /**
     * @return bool
     *
     * @throws Exception
     */
    public function hasExistingBillingAddress()
    {
        if (!$this->testingModel) {
            $by = WebDriverBy::xpath("//input[@name='payment_address' and @value='existing']");
            try {
                $this->client->wait(1)->until(
                    WebDriverExpectedCondition::visibilityOfElementLocated($by)
                );

                return true;
            } catch (NoSuchElementException $e) {
                return false;
            } catch (TimeOutException $e) {
                return false;
            }
        }
        // TODO This is not correct
        return false;
    }

    /**
     * @throws TimeOutException
     */
    public function useExistingBillingAddress()
    {
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::xpath("//input[@name='payment_address' and @value='existing']"))->click();
            $this->client->findElement(WebDriverBy::id('button-payment-address'))->click();

            $by = WebDriverBy::cssSelector('#collapse-shipping-address .panel-body :first-child');
            $this->waitUntilVisibilityOfElementLocated($by);
        }
    }

    /**
     * @throws UnexpectedTagNameException
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function useNewBillingAddress()
    {
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::xpath("//input[@name='payment_address' and @value='new']"))->click();

            $this->client->findElement(WebDriverBy::id('input-payment-firstname'))->sendKeys('First');
            $this->client->findElement(WebDriverBy::id('input-payment-lastname'))->sendKeys('Last');
            $this->client->findElement(WebDriverBy::id('input-payment-address-1'))->sendKeys('Here');
            $this->client->findElement(WebDriverBy::id('input-payment-city'))->sendKeys('There');
            // Postcode, country, region/state are pre-filled, but clear postcode is enough
            $this->client->findElement(WebDriverBy::id('input-payment-postcode'))->clear()->sendKeys('1234');
            $regionElement = $this->client->findElement(WebDriverBy::id('input-payment-zone'));
            $region = new WebDriverSelect($regionElement);
            $region->selectByValue('3513');

            $this->client->findElement(WebDriverBy::id('button-payment-address'))->click();

            $by = WebDriverBy::cssSelector('#collapse-shipping-address .panel-body :first-child');
            $this->waitUntilVisibilityOfElementLocated($by);
        }
    }

    /**
     * @throws UnexpectedTagNameException
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function addBillingAddress()
    {
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::id('input-payment-firstname'))->sendKeys('First');
            $this->client->findElement(WebDriverBy::id('input-payment-lastname'))->sendKeys('Last');
            $this->client->findElement(WebDriverBy::id('input-payment-address-1'))->sendKeys('Here');
            $this->client->findElement(WebDriverBy::id('input-payment-city'))->sendKeys('There');
            // Postcode, country, region/state are pre-filled, but clear postcode is enough
            $this->client->findElement(WebDriverBy::id('input-payment-postcode'))->clear()->sendKeys('1234');
            $regionElement = $this->client->findElement(WebDriverBy::id('input-payment-zone'));
            $region = new WebDriverSelect($regionElement);
            $region->selectByValue('3513');

            $this->client->findElement(WebDriverBy::id('button-payment-address'))->click();

            $by = WebDriverBy::cssSelector('#collapse-shipping-address .panel-body :first-child');
            $this->waitUntilVisibilityOfElementLocated($by);
        }
    }

    /**
     * @throws TimeOutException
     */
    public function useExistingDeliveryAddress()
    {
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::xpath("//input[@name='shipping_address' and @value='existing']"))->click();
            $this->client->findElement(WebDriverBy::id('button-shipping-address'))->click();

            $by = WebDriverBy::cssSelector('#collapse-shipping-method .panel-body :first-child');
            $this->waitUntilVisibilityOfElementLocated($by);
        }
    }

    /**
     * @throws UnexpectedTagNameException
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function useNewDeliveryAddress()
    {
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::xpath("//input[@name='shipping_address' and @value='new']"))->click();

            $this->client->findElement(WebDriverBy::id('input-shipping-firstname'))->sendKeys('First');
            $this->client->findElement(WebDriverBy::id('input-shipping-lastname'))->sendKeys('Last');
            $this->client->findElement(WebDriverBy::id('input-shipping-address-1'))->sendKeys('Here');
            $this->client->findElement(WebDriverBy::id('input-shipping-city'))->sendKeys('There');
            // Postcode, country, region/state are pre-filled, but clear postcode is enough
            $this->client->findElement(WebDriverBy::id('input-shipping-postcode'))->clear()->sendKeys('1234');
            $regionElement = $this->client->findElement(WebDriverBy::id('input-shipping-zone'));
            $region = new WebDriverSelect($regionElement);
            $region->selectByValue('3513');

            $this->client->findElement(WebDriverBy::id('button-shipping-address'))->click();

            $by = WebDriverBy::cssSelector('#collapse-shipping-method .panel-body :first-child');
            $this->waitUntilVisibilityOfElementLocated($by);
        }
    }

    /**
     * @throws UnexpectedTagNameException
     * @throws NoSuchElementException
     * @throws TimeOutException
     * @throws Exception
     */
    public function addDeliveryAddress()
    {
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::id('input-shipping-firstname'))->sendKeys('First');
            $this->client->findElement(WebDriverBy::id('input-shipping-lastname'))->sendKeys('Last');
            $this->client->findElement(WebDriverBy::id('input-shipping-address-1'))->sendKeys('Here');
            $this->client->findElement(WebDriverBy::id('input-shipping-city'))->sendKeys('There');
            // Postcode, country, region/state are pre-filled, but clear postcode is enough
            $this->client->findElement(WebDriverBy::id('input-shipping-postcode'))->clear()->sendKeys('1234');
            $regionElement = $this->client->findElement(WebDriverBy::id('input-shipping-zone'));
            $region = new WebDriverSelect($regionElement);
            $region->selectByValue('3513');

            if ($this->hasExistingDeliveryAddress()) {
                $this->client->findElement(WebDriverBy::id('button-shipping-address'))->click();
            } else {
                try {
                    $this->client->findElement(WebDriverBy::id('button-guest-shipping'))->click();
                } catch (NoSuchElementException $e) {
                    $this->client->findElement(WebDriverBy::cssSelector("#collapse-shipping-address input[type='button']"))->click();
                }
            }

            $by = WebDriverBy::cssSelector('#collapse-shipping-method .panel-body :first-child');
            $this->waitUntilVisibilityOfElementLocated($by);
        }
    }

    /**
     * @return bool
     *
     * @throws Exception
     */
    public function hasExistingDeliveryAddress()
    {
        if (!$this->testingModel) {
            $by = WebDriverBy::xpath("//input[@name='shipping_address' and @value='existing']");
            try {
                $this->client->wait(1)->until(
                    WebDriverExpectedCondition::visibilityOfElementLocated($by)
                );

                return true;
            } catch (NoSuchElementException $e) {
                return false;
            } catch (TimeOutException $e) {
                return false;
            }
        }
        // TODO This is not correct
        return false;
    }

    /**
     * @throws TimeOutException
     */
    public function addDeliveryMethod()
    {
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::id('button-shipping-method'))->click();

            $by = WebDriverBy::cssSelector('#collapse-payment-method .panel-body :first-child');
            $this->waitUntilVisibilityOfElementLocated($by);
        }
    }

    /**
     * @throws TimeOutException
     */
    public function addPaymentMethod()
    {
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::xpath("//input[@name='agree']"))->click();
            $this->client->findElement(WebDriverBy::id('button-payment-method'))->click();

            $by = WebDriverBy::cssSelector('#collapse-checkout-confirm .panel-body :first-child');
            $this->waitUntilVisibilityOfElementLocated($by);
        }
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function confirmOrder()
    {
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::id('button-confirm'))->click();
            try {
                $this->client->wait(1)->until(
                    WebDriverExpectedCondition::urlContains($this->url.'/index.php?route=checkout/success')
                );
            } catch (TimeOutException $e) {
                throw new TimeOutException(sprintf('Current url %s does not contain %s ', $this->client->getCurrentURL(), $this->url.'/index.php?route=checkout/success'));
            }
        }
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function continueShopping()
    {
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::linkText('Continue'))->click();
            try {
                $this->client->wait(1)->until(
                    WebDriverExpectedCondition::urlContains($this->url.'/index.php?route=common/home')
                );
            } catch (TimeOutException $e) {
                throw new TimeOutException(sprintf('Current url %s does not contain %s ', $this->client->getCurrentURL(), $this->url.'/index.php?route=common/home'));
            }
        }
    }

    public function fillPersonalDetails()
    {
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::id('input-payment-firstname'))->sendKeys('First');
            $this->client->findElement(WebDriverBy::id('input-payment-lastname'))->sendKeys('Last');
            $this->client->findElement(WebDriverBy::id('input-payment-email'))->sendKeys(uniqid().'@example.com');
            $this->client->findElement(WebDriverBy::id('input-payment-telephone'))->sendKeys('0123456789');
        }
    }

    public function fillPassword()
    {
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::id('input-payment-password'))->sendKeys('1234');
            $this->client->findElement(WebDriverBy::id('input-payment-confirm'))->sendKeys('1234');
        }
    }

    /**
     * @throws UnexpectedTagNameException
     * @throws NoSuchElementException
     */
    public function fillBillingAddress()
    {
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::id('input-payment-address-1'))->sendKeys('Here');
            $this->client->findElement(WebDriverBy::id('input-payment-city'))->sendKeys('There');
            $this->client->findElement(WebDriverBy::id('input-payment-postcode'))->sendKeys('1234');
            $regionElement = $this->client->findElement(WebDriverBy::id('input-payment-zone'));
            $region = new WebDriverSelect($regionElement);
            $region->selectByValue('3513');
        }
    }

    /**
     * @throws TimeOutException
     */
    public function guestCheckoutAndAddBillingAddress()
    {
        if (!$this->testingModel) {
            // Delivery and billing addresses are not the same.
            $this->client->findElement(WebDriverBy::xpath("//input[@name='shipping_address' and @value='1']"))->click();
            $this->client->findElement(WebDriverBy::id('button-guest'))->click();

            $by = WebDriverBy::cssSelector('#collapse-shipping-address .panel-body :first-child');
            $this->waitUntilVisibilityOfElementLocated($by);
        }

        $this->guestCheckout = false;
    }

    /**
     * @throws Exception
     */
    public function registerAndAddBillingAddress()
    {
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::xpath("//input[@name='agree']"))->click();
            $this->client->findElement(WebDriverBy::xpath("//input[@name='shipping_address' and @value='1']"))->click();
            $this->client->findElement(WebDriverBy::id('button-register'))->click();

            $by = WebDriverBy::cssSelector('#collapse-shipping-address .panel-body :first-child');
            $this->waitUntilVisibilityOfElementLocated($by);
        }

        $this->registerAccount = false;
        $this->loggedIn = true;
        if (!$this->testingModel) {
            throw new Exception('Still able to do register account, guest checkout or login when logged in!');
        }
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function addProductAndCheckoutNotLoggedIn()
    {
        $this->goToProduct($this->productId);
        $this->addToCart();
        $this->goToCheckout();
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function addProductAndCheckoutLoggedIn()
    {
        $this->goToProduct($this->productId);
        $this->addToCart();
        $this->goToCheckout();
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

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    private function addToCart()
    {
        if (!$this->testingModel) {
            $by = WebDriverBy::id('button-cart');
            $this->client->wait(1)->until(
                WebDriverExpectedCondition::elementToBeClickable($by)
            );
            $element = $this->client->findElement($by);
            $element->click();
            $this->client->wait(1)->until(
                WebDriverExpectedCondition::elementTextContains(WebDriverBy::className('alert'), 'Success')
            );
        }
    }

    /**
     * @throws TimeOutException
     */
    private function goToCheckout()
    {
        if (!$this->testingModel) {
            $this->client->get($this->url.'/index.php?route=checkout/checkout');

            $by = WebDriverBy::cssSelector('.panel-body  :first-child');
            $this->waitUntilVisibilityOfElementLocated($by);
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

    /**
     * @param WebDriverBy $by
     *
     * @throws TimeOutException
     */
    public function waitUntilVisibilityOfElementLocated(WebDriverBy $by)
    {
        try {
            $this->client->wait(1)->until(
                WebDriverExpectedCondition::visibilityOfElementLocated($by)
            );
        } catch (NoSuchElementException $e) {
            // It's okay, we are waiting for element to be loaded by ajax and appear in the page.
        }
    }

    private function goToHome()
    {
        if (!$this->testingModel) {
            $this->client->get($this->url.'/index.php?route=common/home');
        }
    }

    public function getScreenshotUrl($bugId, $index)
    {
        return sprintf('http://localhost/mbt-api/bug-screenshot/%d/%d', $bugId, $index);
    }
}
