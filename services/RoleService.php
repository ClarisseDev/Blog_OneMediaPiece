<?php

namespace OneMediaPiece_blog\services;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\model\Role;
use OneMediaPiece_blog\daos\RoleDao;
use OneMediaPiece_blog\utils\service\IService;
use OneMediaPiece_blog\utils\service\AbstractService;
use OneMediaPiece_blog\utils\entity\IEntity;
use OneMediaPiece_blog\utils\dao\IDao;


class RoleService extends AbstractService implements IService
{
    protected RoleDao $dao;

    public function __construct()
    {
        $this->dao = new RoleDao();
    }

    function findById(int $id) : IEntity
    {
        // throw new Exception ("Not implemented " . __CLASS__ . " " . __FUNCTION__ . " " . __LINE__);
        return $this->getDao()->findById($id);
    }

    public function findAll(): array
    {
        return $this->dao->findAll();
    }

    function getDao() : IDao
    {
        return $this->dao;
    }
}


?>