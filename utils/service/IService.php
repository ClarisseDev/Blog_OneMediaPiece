<?php

namespace OneMediaPiece_blog\utils\service;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\utils\dao\IDao;
use OneMediaPiece_blog\utils\entity\IEntity;

// Définit les opérations de base pour tout service
interface IService 
{
    function insert(IEntity $entity) : int;

    function findAll() : array;

    function findById(int $id) : IEntity;

    function update(IEntity $entity);

    function delete(int $id);

    function getDao() : IDao;

}

?>