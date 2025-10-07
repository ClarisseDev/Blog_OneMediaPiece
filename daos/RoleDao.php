<?php

namespace OneMediaPiece_blog\daos;
use OneMediaPiece_blog\utils\entity\IEntity;
use OneMediaPiece_blog\utils\dao\IDao;
use OneMediaPiece_blog\utils\BddSingleton;
use OneMediaPiece_blog\utils\dao\AbstractDao;
use OneMediaPiece_blog\exceptions\HttpStatusException;
use OneMediaPiece_blog\model\Role;
use PDO;
use PDOException;
use InvalidArgumentException;

class RoleDao extends AbstractDao implements IDao
{
    function getTableName() : string
    {
        return "role";
    }

    function getPrimaryKeyName() : string
    {
        return "id_role";
    }

    function createEntityFromRow($row) : IEntity
    {
        return Role::createFromRow($row);
    }

    public function findAll(): array
    {
        $pdo = BddSingleton::getInstance()->getPdo();
        $sql = "SELECT * FROM role";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $roles = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) 
        {
            $roles[] = Role::createFromRow($row);
        }

        return $roles;
    }

}

?>