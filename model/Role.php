<?php
namespace OneMediaPiece_blog\model;
use OneMediaPiece_blog\utils\entity\IEntity;
use OneMediaPiece_blog\utils\entity\AbstractEntity;

	class Role extends AbstractEntity implements IEntity 
    {
		private int $idRole;
		private string $label;
        
		function __contruct() { /* RAS */ }

		function getIdRole() : int 
        {
			return $this->idRole;
		}

		function setIdRole(int $id) 
        {
			$this->idRole = $id;
		}

		function getLabel() : string 
        {
			return $this->label;
		}

		function setLabel(string $l) 
        {
			$this->label = $l;
		}

		public static function createFromRow($row) 
        {
			$role = new Role();
			$role->setIdRole( intval($row->id_role) );
			$role->setLabel( $row->label );
			return $role;
		}

		public static function toArray() : array 
		{
			return [ "idRole" => "id_role", "label" => "label" ];
		}	
	}
?>