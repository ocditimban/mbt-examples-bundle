<?php

namespace Tienvx\Bundle\MbtExamplesBundle\Subject;

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
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverSelect;
use Symfony\Component\Panther\Client;
use Tienvx\Bundle\MbtBundle\Annotation\DataProvider;
use Tienvx\Bundle\MbtBundle\Subject\AbstractSubject;

class Product extends AbstractSubject
{
    use ElementHelper;

    /**
     * @var int
     */
    protected $productId = 42;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $url = 'http://example.com';

    public static function support(): string
    {
        return 'product';
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function setUp()
    {
        if (!$this->testingModel) {
            $caps = new DesiredCapabilities();
            $caps->setCapability(WebDriverCapabilityType::BROWSER_NAME, WebDriverBrowserType::FIREFOX);
            $caps->setCapability('platformName', WebDriverPlatform::LINUX);
            $caps->setCapability('browserVersion', '66.0.1');
            $caps->setCapability(
                'moz:firefoxOptions',
                ['args' => ['-headless']]
            );
            $this->client = Client::createSeleniumClient('http://hub:4444/wd/hub', $caps);
        }
        $this->goToProduct($this->productId);
    }

    public function tearDown()
    {
        if (!$this->testingModel) {
            $this->client->quit();
        }
    }

    /**
     * @DataProvider(method="getRandomRadio")
     *
     * @throws Exception
     */
    public function selectRadio()
    {
        if (empty($this->data['radio'])) {
            throw new Exception('Can not select radio: random option is not chosen');
        }
        $radio = $this->data['radio'];
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::xpath("//input[@name='option[218]' and @value='{$radio}']"))->click();
        }
    }

    public function getRandomRadio()
    {
        return ['radio' => rand(5, 7)];
    }

    /**
     * @DataProvider(method="getRandomCheckbox")
     *
     * @throws Exception
     */
    public function selectCheckbox()
    {
        if (empty($this->data['checkbox'])) {
            throw new Exception('Can not select checkbox: random option is not chosen');
        }
        $checkbox = $this->data['checkbox'];
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::xpath("//input[@name='option[223][]' and @value='{$checkbox}']"))->click();
        }
    }

    public function getRandomCheckbox()
    {
        // Can update code to select more than 1 checkbox
        return ['checkbox' => rand(8, 11)];
    }

    public function fillText()
    {
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::id('input-option208'))->sendKeys('Test text');
        }
    }

    /**
     * @DataProvider(method="getRandomSelect")
     *
     * @throws Exception
     * @throws NoSuchElementException
     * @throws UnexpectedTagNameException
     */
    public function selectSelect()
    {
        if (empty($this->data['select'])) {
            throw new Exception('Can not select dropdown: random option is not chosen');
        }
        $checkbox = $this->data['select'];
        if (!$this->testingModel) {
            $regionElement = $this->client->findElement(WebDriverBy::id('input-option217'));
            $region = new WebDriverSelect($regionElement);
            $region->selectByValue($checkbox);
        }
    }

    public function getRandomSelect()
    {
        return ['select' => rand(1, 4)];
    }

    public function fillTextarea()
    {
        if (!$this->testingModel) {
            $this->client->findElement(WebDriverBy::id('input-option209'))->sendKeys('Test textarea');
        }
    }

    /**
     * @throws Exception
     */
    public function selectFile()
    {
        if (!$this->testingModel) {
            throw new Exception('Can not upload file!');
        }
    }

    public function selectDate()
    {
    }

    public function selectTime()
    {
    }

    public function selectDateTime()
    {
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function addToCart()
    {
        if (!$this->testingModel) {
            $by = WebDriverBy::id('button-cart');
            $this->waitAndClick($by);
        }
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function addToWishList()
    {
        if (!$this->testingModel) {
            $by = WebDriverBy::xpath("//button[@data-original-title='Add to Wish List']");
            $this->waitAndClick($by);
            $this->closeAlerts();
        }
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function compareThisProduct()
    {
        if (!$this->testingModel) {
            $by = WebDriverBy::xpath("//button[@data-original-title='Compare this Product']");
            $this->waitAndClick($by);
            $this->closeAlerts();
        }
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function writeAReview()
    {
        if (!$this->testingModel) {
            $by = WebDriverBy::linkText('Write a review');
            $this->waitAndClick($by);
        }
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function fillName()
    {
        if (!$this->testingModel) {
            $by = WebDriverBy::id('input-name');
            $element = $this->waitAndClick($by);
            $element->sendKeys('My Name');
        }
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function fillReview()
    {
        if (!$this->testingModel) {
            $by = WebDriverBy::id('input-review');
            $element = $this->waitAndClick($by);
            $element->sendKeys('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut et rutrum sem, at lacinia orci. Suspendisse eget posuere odio, a venenatis libero. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Sed mauris dui, congue et tellus at, pharetra bibendum diam. Donec diam justo, aliquam quis massa vel, cursus commodo odio. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. In tempus mi sit amet semper imperdiet. Maecenas mollis nisi nulla, at viverra sapien auctor vel. Phasellus tincidunt, dolor et eleifend pretium, nulla magna malesuada nisi, id hendrerit mi orci eget sapien. Proin venenatis aliquet elit eu eleifend. In leo massa, convallis a felis eget, malesuada sagittis ipsum.');
        }
    }

    /**
     * @DataProvider(method="getRandomRating")
     *
     * @throws Exception
     */
    public function fillRating()
    {
        if (empty($this->data['rating'])) {
            throw new Exception('Can not select rating: random option is not chosen');
        }
        $rating = $this->data['rating'];
        if (!$this->testingModel) {
            $by = WebDriverBy::xpath("//input[@name='rating' and @value='{$rating}']");
            $this->waitAndClick($by);
        }
    }

    public function getRandomRating()
    {
        return ['rating' => rand(1, 5)];
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function submitReview()
    {
        if (!$this->testingModel) {
            $by = WebDriverBy::id('button-review');
            $this->waitAndClick($by);
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
     * @return WebDriverElement
     *
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function waitAndClick(WebDriverBy $by): WebDriverElement
    {
        if (!$this->testingModel) {
            $this->client->wait(1)->until(
                WebDriverExpectedCondition::elementToBeClickable($by)
            );
            $element = $this->client->findElement($by);
            $element->click();

            return $element;
        }
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function closeAlerts()
    {
        if (!$this->testingModel) {
            $this->client->waitFor('.alert', 1);
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
