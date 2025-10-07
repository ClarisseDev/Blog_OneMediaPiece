<?php

namespace OneMediaPiece_blog\utils\dao;
use OneMediaPiece_blog\utils\entity\IEntity;
use OneMediaPiece_blog\utils\dao\IDao;
use OneMediaPiece_blog\utils\BddSingleton;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use PDO;
use Exception;

abstract class AbstractDao implements IDao 
{
    function insert(IEntity $entity) : int
    {
        throw new Exception ("Not implemented " . __CLASS__ . " " . __FUNCTION__ . " " . __LINE__);
    }

    function findAll()
    {
        throw new Exception ("Not implemented " . __CLASS__ . " " . __FUNCTION__ . " " . __LINE__);
    }


    function findById(int $id) : IEntity
    {
        // throw new Exception ("Not implemented " . __CLASS__ . " " . __FUNCTION__ . " " . __LINE__);

        $pdo = BddSingleton::getInstance()->getPdo();
        $sql = "SELECT t.* FROM " . $this->getTableName() . " t WHERE t." . $this->getPrimaryKeyName() . " = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $stmt->execute();
        $row = $stmt->fetch();
        if ( ! $row ) 
        {
            throw new HttpStatusException("Entity " . $this->getTableName()  . " not found : " . $id . " " . __CLASS__  . " " . __FUNCTION__ . " " . __LINE__, 404);
        }
        return $this->createEntityFromRow($row);
    }

    abstract function getTableName() : string;

    abstract function getPrimaryKeyName() : string;

    abstract function createEntityFromRow($row) : IEntity;


    function update(IEntity $entity)
    {
        throw new Exception ("Not implemented " . __CLASS__ . " " . __FUNCTION__ . " " . __LINE__);
    }


    function delete(int $id)
    {
        throw new Exception ("Not implemented " . __CLASS__ . " " . __FUNCTION__ . " " . __LINE__);
    }

    
    function getDao() : IDao
    {
        throw new Exception ("Not implemented " . __CLASS__ . " " . __FUNCTION__ . " " . __LINE__);
    }
}

?>