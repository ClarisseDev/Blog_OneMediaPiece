<?php

namespace OneMediaPiece_blog\utils\entity;
use OneMediaPiece_blog\utils\entity\IEntity;
use ReflectionClass;

	abstract class AbstractEntity implements IEntity 
    {
		/* Permet de prendre les champs privés d'une instance de classe
		 * et de les rendre disponibles pour la serialisation */
		function jsonSerialize() : mixed
        {
			// return get_object_vars($this);
			// Permet d'explorer la classe avec le mécanisme de Réflexivité / Introspection
			$reflection  = new ReflectionClass($this);
			$properties = array();
			// Pour chaque champs de de ma classe
			foreach ($reflection->getProperties() as $property) 
            {
				$property->setAccessible(true); // Il est privé, je le rend public
				// J'extrais la valeur que je stocke dans un tableau associatif
				$properties[ $property->getName() ] = $property->getValue($this);
				$property->setAccessible(false); // Je remets le champs privé
			}
			return $properties;
		}
	}

?>