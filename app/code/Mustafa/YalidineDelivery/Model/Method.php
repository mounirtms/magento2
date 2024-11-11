<?php 
namespace Mustafa\YalidineDelivery\Model;

use Magento\Framework\App\ResourceConnection;

class Method
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Get or create shipping method ID
     *
     * @param array $methodData
     * @return int
     */
    public function getOrCreateMethodId(array $data): int
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('amasty_table_method'); 

        $select = $connection->select()
            ->from($tableName, ['id']) 
            ->where('stores = ?', ['finset' => $data['storeId']])
            ->where('name = ?', $data['name']);

        $methodId = $connection->fetchOne($select);

        if ($methodId) {
            return (int) $methodId;
        }
        $data['stores']=$data['storeId'];
        unset($data['storeId']);

        $connection->insert($tableName, $data);
        return (int) $connection->lastInsertId();
    }     
}