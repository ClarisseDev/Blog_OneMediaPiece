<?php

namespace OneMediaPiece_blog\utils\service;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\utils\dao\IDao;
use OneMediaPiece_blog\utils\entity\IEntity;
use OneMediaPiece_blog\utils\service\IService;
use InvalidArgumentException;

// On utlise le concept de design pattern Adapter
// Le service va proposer une "adaptation" vers le Dao
abstract class AbstractService implements IService
{
    function insert(IEntity $entity) : int
    {
        return $this->getDao()->insert($entity);
    }

    function findAll() : array
    {
        return $this->getDao()->findAll();
    }

    function findById(int $id) : IEntity
    {
        return $this->getDao()->findById($id);
    }

    function update(IEntity $entity)
    {
        return $this->getDao()->update($entity);
    }

    function delete(int $id)
    {
        return $this->getDao()->delete($id);
    }

    abstract function getDao() : IDao;
}

?>
