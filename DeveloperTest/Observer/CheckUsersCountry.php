<?php

namespace ML\DeveloperTest\Observer;

use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CheckUsersCountry implements ObserverInterface
{
    protected $_messageManager;
    protected $_logger;
    protected $_countries;

    private $_urlInterface;
    protected $_dataHelper;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger,
        \ML\DeveloperTest\Block\Countries $countries,
        \Magento\Framework\UrlInterface $urlInterface,
        RedirectFactory $redirectFactory,
        \ML\DeveloperTest\Helper\Data $dataHelper
    ) {
        $this->_logger = $logger;
        $this->_countries = $countries;
        $this->_messageManager = $messageManager;
        $this->_urlInterface = $urlInterface;
        $this->_redirectFactory = $redirectFactory;
        $this->_dataHelper = $dataHelper;
    }

    public function execute(Observer $observer)
    {
        // check if the module is enabled
        $isEnabled = $this->_dataHelper->getGeneralConfig('enable');
        $this->_logger->debug($isEnabled);

        if ($isEnabled == 1) {
            $getQuoteItem = $observer->getQuoteItem();
            $getProductData = $getQuoteItem->getProduct();
            $usersCurrentCountryData = $this->_countries->getUsersCountryData();
            $countriesOptions = $this->_countries->getProductsCountriesOption($getProductData->getSku());
            // String into array
            $productCountriesNotAllowed = explode(",", $countriesOptions);

            if (in_array($usersCurrentCountryData, $productCountriesNotAllowed)) {
                $message = $this->_dataHelper->getGeneralConfig('messagetoshow') . ' ' . $usersCurrentCountryData;
                $this->_messageManager->addErrorMessage(__($message));
                //set false if you not want to add product to cart
                $observer->getRequest()->setParam('product', false);
                exit;
            }
        }
    }
}
