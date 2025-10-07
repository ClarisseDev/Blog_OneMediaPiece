<?php
session_start();
// define("ROOT", dirname(__DIR__) ) ; 
// var_dump(ROOT);
require_once(ROOT .  "/services/CompteService.php") ; 
require_once(ROOT .  "/model/Role.php") ;  

// Récupérer le compte connecté
$_SESSION["compteId"]=2;

$compteService = new CompteService() ;
$compte = new Compte() ;
$role = new Role() ;

// Sélectionner le compte à modifier (essayer un compte existant et un compte inexistant)
$compte->setIdCompte(99);

// Saisir les paramètres à modifier
$compte->setLogin("NoLogin");     // On teste un champ qui ne devrait pas être modifié
$compte->setPassword("toto");
$compte->setDateCreation(new DateTime());   // On teste un champ qui ne devrait pas être modifié
$compte->setDateModification(new DateTime());
$compte->setEstSupprime(true);
$compte->setEstSignale(true);
$compte->setEstBanni(false);
$compte->setEnAttenteDeModeration(false);
$role->setIdRole(1);
$compte->setRole($role);

// Appliquer la mise à jour
$newEntity = $compteService->update($compte);

var_dump($newEntity);
?>
