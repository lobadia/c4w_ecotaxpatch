<?php
/**
 * Created by PhpStorm.
 * User: remidalmeida
 * Date: 18/09/2018
 * Time: 23:46
 */

namespace EcotaxPatch\Repository;

use Doctrine\DBAL\Connection;

class ProductEcotaxRepository
{
    /**
     * @const string the module table.
     */
    const ECOTAX_PRODUCT_TABLE = 'c4w_ecotaxpatch_product';

    /**
     * @var Connection the Database connection.
     */
    private $connection;

    /**
     * @var string the Database table.
     */
    private $table;

    public function __construct(Connection $connection, $databasePrefix)
    {
        $this->connection = $connection;
        $this->table = $databasePrefix . self::ECOTAX_PRODUCT_TABLE;
    }

    /**
     * @param int $productId the product id
     * @return array the ecotax price
     */
    public function findEcotaxByProductId(int $productId)
    {
        $table = $this->table;
        $query = "SELECT e.* FROM ${table} e WHERE e.`id_product` = :productId";
        $statement = $this->connection->prepare($query);
        $statement->bindValue('productId', $productId);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @param int $productId the product id
     * @param string $ecotax_taxincl the ecotax tax included value
     * @return bool
     */
    public function setEcotaxByProductId($ecotax, int $productId)
    {
        $table = $this->table;
        if(empty($this->findEcotaxByProductId($productId))) {
            $query = "INSERT INTO ${table} (id_product,ecotax_taxincl) VALUES (:productId, :ecotax)";
        } else {
            $query = "UPDATE ${table} SET ecotax_taxincl = :ecotax WHERE `id_product` = :productId";
        }
        $statement = $this->connection->prepare($query);
        $statement->bindValue('productId', $productId);
        $statement->bindValue('ecotax', $ecotax);
        $statement->execute();
    }
}