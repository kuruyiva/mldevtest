<?php

namespace ML\DeveloperTest\Block;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

class Countries extends \Magento\Framework\View\Element\Template
{
    protected $_countryCollectionFactory;
    private $remoteAddress;

    protected $client;

    protected $_logger;

    protected $productRepository;
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        RemoteAddress $remoteAddress,
        Client $client,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_countryCollectionFactory = $countryCollectionFactory;
        $this->client = $client;
        $this->remoteAddress = $remoteAddress;
        $this->_logger = $logger;
        $this->productRepository = $productRepository;
    }
    public function getCountryCollection()
    {
        $collection = $this->_countryCollectionFactory->create()->loadByStore();
        return $collection;
    }

    /**
     * Retrieve list of countries in array option
     *
     * @return array
     */
    public function getCountries()
    {
        return $this->getCountryCollection()
            ->setForegroundCountries($this->getTopDestinations())
            ->toOptionArray();
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    public function getUsersCountryData()
    {
        $this->_logger->debug($this->getUserIp());
        $endpointUrl = 'http://api.ipapi.com/api/' . $this->getUserIp() . '?access_key=' . $this->getAccessKey();

        $res = $this->client->post($endpointUrl);
        $subscriptionData = json_decode($res->getBody()->getContents(), true);
        return $subscriptionData['country_code'];
    }

    /**
     * @return string
     */
    public function getUserIp(): string
    {
        $usersIpAddress = $this->remoteAddress->getRemoteAddress();

        if ($usersIpAddress === '127.0.0.1') {
            $usersIpAddress = '80.5.11.222';
        }
        return $usersIpAddress;
    }

    /**
     * @return string
     */
    public function getAccessKey(): string
    {
        return 'e248f9c840a45ca8024911671579b97c';
    }

    public function getProductsCountriesOption($sku)
    {
        $_product = $this->productRepository->get($sku, false, null, true);
        return $_product->getCountriesOption();
    }
}
