<?php

namespace Mustafa\YalidineDelivery\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $this->installRates($installer);
        $installer->endSetup();
    }
    
    protected function installRates(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('yalidine_delivery_rates')

        )->addColumn(
            'rate_id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true,
            ],
            'Primary key'

        )->addColumn(
            'scope',
            Table::TYPE_TEXT,
            10,
            [
                'nullable' => false,
                'default'  => '*',
            ],
            'Scope'
            
        )->addColumn(
            'scope_id',
            Table::TYPE_INTEGER,
            null,
            [
                'nullable' => false,
                'default'  => '0',
            ],
            'Scope Id'

        )->addColumn(
            'wilaya_name',
            Table::TYPE_TEXT,
            30,
            [
                'nullable' => false,
                'default'  => '*',
            ],
            'Destination Wilaya Name'

        )->addColumn(
            'wilaya_id',
            Table::TYPE_INTEGER,
            null,
            [
                'nullable' => false,
                'default'  => '0',
            ],
            'Destination Wilaya Id'

        )->addColumn(
            'home_fee',
            Table::TYPE_DECIMAL,
            '6,2',
            [
                'nullable' => false,
                'default'  => '0.00',
            ],
            'Home Fee'
        )->addColumn(
            'desk_fee',
            Table::TYPE_DECIMAL,
            '6,2',
            [
                'nullable' => false,
                'default'  => '0.00',
            ],
            'Desk Fee'

        )->addIndex(
            $installer->getIdxName(
                'shipping_tablerate',
                [
                    'scope',
                    'scope_id',
                    'wilaya_id',                    
                ],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [
                'scope',
                'scope_id',
                'wilaya_id',  
            ],
            [
                'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE,
            ]
        )->setComment(
            'Yalidine Delivery Rates'
        );

        $installer->getConnection()->createTable($table);
    }
}