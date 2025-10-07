<?php

namespace OneMediaPiece_blog\utils\dao;
use OneMediaPiece_blog\utils\entity\IEntity;

interface IDao 
{
    function insert(IEntity $entity) : int;

    function findAll();

    function findById(int $id) : IEntity;

    function update(IEntity $entity);

    function delete(int $id);
    
    function getDao() : IDao;
}

?>