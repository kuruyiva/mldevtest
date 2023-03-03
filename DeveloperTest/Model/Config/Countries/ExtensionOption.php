<?php

namespace ML\DeveloperTest\Model\Config\Countries;

use ML\DeveloperTest\Block\Countries;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class ExtensionOption extends AbstractSource
{
    protected $_countryCollection;
    public function __construct(
        Countries $countriesCollection
    ) {
        $this->_countryCollection = $countriesCollection;
    }

    /**
     * Options getter
     *
     * @return array|null
     */
    public function getAllOptions(): ?array
    {
        return $this->_countryCollection->getCountries();
    }
}
