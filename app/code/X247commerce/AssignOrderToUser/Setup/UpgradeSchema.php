<?php
/** 
 * Copyright © 247Commerce, Inc. All rights reserved.
 * See COPYING.txt for license details. 
 * @author 247Commerce Core Team <core@247commerce.com> 
 * 
 */
namespace X247commerce\AssignOrderToUser\Setup;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
class UpgradeSchema implements  UpgradeSchemaInterface
{
     /**
     * @info:create the new fileds
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.5') < 0) {
            $connection = $setup->getConnection();
            $connection->addColumn(
                $setup->getTable('sales_order'),
                'assigned_user_name',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'sub admin name',
                    'after' => 'entity_id'
                ]
            );

            $connection->addColumn(
                $setup->getTable('sales_order'),
                'assigned_user_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'sub admin id',
                    'after' => 'entity_id'
                ]
            );

            $connection->addColumn(
                $setup->getTable('sales_order_grid'),
                'assigned_user_name',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'sub admin name',
                    'after' => 'entity_id'
                ]
            );

            $connection->addColumn(
                $setup->getTable('sales_order_grid'),
                 'assigned_user_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'sub admin id',
                    'after' => 'entity_id'
                ]
            );

            //invoice table field
            $connection->addColumn(
                $setup->getTable('sales_invoice'),
                'assigned_user_name',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'sub admin name',
                    'after' => 'entity_id'
                ]
            );

            $connection->addColumn(
                $setup->getTable('sales_invoice'),
                'assigned_user_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'sub admin id',
                    'after' => 'entity_id'
                ]
            );

            $connection->addColumn(
                $setup->getTable('sales_invoice_grid'),
                'assigned_user_name',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'sub admin name',
                    'after' => 'entity_id'
                ]
            );

            $connection->addColumn(
                $setup->getTable('sales_invoice_grid'),
                 'assigned_user_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'sub admin id',
                    'after' => 'entity_id'
                ]
            );

            //shipment table fields
            $connection->addColumn(
                $setup->getTable('sales_shipment'),
                'assigned_user_name',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'sub admin name',
                    'after' => 'entity_id'
                ]
            );

            $connection->addColumn(
                $setup->getTable('sales_shipment'),
                'assigned_user_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'sub admin id',
                    'after' => 'entity_id'
                ]
            );

            $connection->addColumn(
                $setup->getTable('sales_shipment_grid'),
                'assigned_user_name',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'sub admin name',
                    'after' => 'entity_id'
                ]
            );

            $connection->addColumn(
                $setup->getTable('sales_shipment_grid'),
                 'assigned_user_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'sub admin id',
                    'after' => 'entity_id'
                ]
            );

            //creditmemo table fields
            $connection->addColumn(
                $setup->getTable('sales_creditmemo'),
                'assigned_user_name',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'sub admin name',
                    'after' => 'entity_id'
                ]
            );

            $connection->addColumn(
                $setup->getTable('sales_creditmemo'),
                'assigned_user_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'sub admin id',
                    'after' => 'entity_id'
                ]
            );

            $connection->addColumn(
                $setup->getTable('sales_creditmemo_grid'),
                'assigned_user_name',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'sub admin name',
                    'after' => 'entity_id'
                ]
            );

            $connection->addColumn(
                $setup->getTable('sales_creditmemo_grid'),
                 'assigned_user_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'sub admin id',
                    'after' => 'entity_id'
                ]
            );
        }
    }
}