<?php

namespace ML\DeveloperTest\Setup;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use ML\DeveloperTest\Model\Config\Countries\ExtensionOption;
use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'countries_option',
            [
                'group' => 'general',
                'label' => 'Countries not allowed',
                'type'  => 'text',
                'input' => 'multiselect',
                'source' => ExtensionOption::class,
                'required' => false,
                'sort_order' => 30,
                'global' => Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'backend' => ArrayBackend::class,
                'visible_on_front' => false
            ]
        );


        $setup->endSetup();
    }
}
