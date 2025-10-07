<?php
// define("ROOT", dirname(__DIR__) ) ; 
define("COMPTE_ID", "compteId");
define("MYSQL_DATE_FORMAT", "Y-m-d h:m:s");
var_dump(ROOT);
require_once(ROOT .  "/daos/CompteDao.php") ; 
require_once(ROOT .  "/model/Role.php") ;  



$compteDao = new CompteDao() ;
$compte = new Compte() ;
$role = new Role() ;

// Sélectionner le compte à modifier (essayer un compte existant et un compte inexistant)
$compte->setIdCompte(2);

// Saisir les paramètres à modifier
$compte->setLogin("NoLogin");     // On teste un champ qui ne devrait pas être modifié
$compte->setPassword("toto");
$compte->setDateCreation(new DateTime());   // On teste un champ qui ne devrait pas être modifié
$compte->setDateModification(new DateTime());
$compte->setEstSupprime(false);
$compte->setEstSignale(false);
$compte->setEstBanni(false);
$compte->setEnAttenteDeModeration(false);
$role->setIdRole(2);
$compte->setRole($role);

// Appliquer la mise à jour
$newEntity = $compteDao->update($compte);

var_dump($newEntity);
?>
