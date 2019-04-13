<?php

namespace Tienvx\Bundle\MbtExamplesBundle\Subject;

use Assert\Assertion;
use League\Flysystem\FileNotFoundException;
use Exception;
use GuzzleHttp\Client;
use Tienvx\Bundle\MbtBundle\Annotation\DataProvider;
use Tienvx\Bundle\MbtBundle\Subject\AbstractSubject;
use GuzzleHttp\Exception\GuzzleException;

class ApiCart extends AbstractSubject
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $apiToken;

    /**
     * @var array
     */
    protected $body = [];

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
        //'20' => [
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
        //],
        //'20_27' => [
            '41', // 'iMac',
        //],
        //'18' => [
            '47', // 'HP LP3065',
            '43', // 'MacBook',
            '44', // 'MacBook Air',
            '45', // 'MacBook Pro',
            '46', // 'Sony VAIO',
        //],
        //'25' => [],
        //'25_28' => [
            '42', // 'Apple Cinema 30',
            '33', // 'Samsung SyncMaster 941BW'
        //],
        //'57' => [
            '49', // 'Samsung Galaxy Tab 10.1',
        //],
        //'17' => [],
        //'24' => [
            '28', // 'HTC Touch HD',
            '40', // 'iPhone',
            '29', // 'Palm Treo Pro',
        //],
        //'33' => [
            '30', // 'Canon EOS 5D',
            '31', // 'Nikon D300'
        //],
        //'34' => [
            '48', // 'iPod Classic',
            '36', // 'iPod Nano',
            '34', // 'iPod Shuffle',
            '32', // 'iPod Touch',
        //],
    ];

    public function __construct()
    {
        $this->cart = [];
    }

    public static function support(): string
    {
        return 'api_cart';
    }

    public function setUp()
    {
        if (!$this->testingModel) {
            $this->client = new Client(['base_uri' => $this->url]);
        }
    }

    public function tearDown()
    {
        if (!$this->testingModel) {
            $this->client = null;
        }
    }

    /**
     * @throws GuzzleException
     */
    public function login()
    {
        $response = $this->client->request('POST', '/index.php?route=api/login', [
            'username' => 'admin',
            'key' => 'admin',
        ]);
        Assertion::eq(200, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        Assertion::keyIsset($body, 'api_token');
        $this->apiToken = $body['api_token'];
    }

    /**
     * @throws GuzzleException
     */
    public function products()
    {
        $response = $this->client->request('POST', '/index.php?route=api/cart/products&api_token='.$this->apiToken, []);
        Assertion::eq(200, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);

        return $body;
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     * @DataProvider(method="getRandomProductFromCart")
     */
    public function edit()
    {
        if (empty($this->data['product'])) {
            throw new Exception('Can not edit cart: product is not selected');
        }
        $product = $this->data['product'];
        $quantity = rand(1, 9);
        $response = $this->client->request('POST', '/index.php?route=api/cart/edit&api_token='.$this->apiToken, [
            'key' => $product,
            'quantity' => $quantity,
        ]);
        Assertion::eq(200, $response->getStatusCode());
        $this->cart[$product] = $quantity;
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     * @DataProvider(method="getRandomProductFromCart")
     */
    public function remove()
    {
        if (empty($this->data['product'])) {
            throw new Exception('Can not remove from cart: product is not selected');
        }
        $product = $this->data['product'];
        $response = $this->client->request('POST', '/index.php?route=api/cart/remove&api_token='.$this->apiToken, [
            'key' => $product,
        ]);
        Assertion::eq(200, $response->getStatusCode());
        unset($this->cart[$product]);
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     * @DataProvider(method="getRandomProductNotInCart")
     */
    public function add()
    {
        if (empty($this->data['product'])) {
            throw new Exception('Can not add to cart: product is not selected');
        }
        $product = $this->data['product'];
        $response = $this->client->request('POST', '/index.php?route=api/cart/remove&api_token='.$this->apiToken, [
            'key' => $product,
        ]);
        Assertion::eq(200, $response->getStatusCode());
    }

    public function productHasBeenSelected()
    {
        return !empty($this->data['product']);
    }

    public function hasApiToken()
    {
        return !empty($this->apiToken);
    }

    public function getRandomProductNotInCart()
    {
        do {
            $product = $this->products[array_rand($this->products)];
        } while (isset($this->cart[$product]));

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
     * @throws GuzzleException
     */
    public function captureScreenshot($bugId, $index)
    {
        $text = json_encode($this->products());
        $this->filesystem->put("{$bugId}/{$index}.txt", $text);
    }

    public function isImageScreenshot()
    {
        return false;
    }

    public function getScreenshot($bugId, $index)
    {
        try {
            return $this->filesystem->read("{$bugId}/{$index}.txt");
        } catch (FileNotFoundException $e) {
            return '';
        }
    }

    public function getScreenshotUrl($bugId, $index)
    {
        return sprintf('http://localhost/mbt-api/bug-screenshot/%d/%d', $bugId, $index);
    }
}
