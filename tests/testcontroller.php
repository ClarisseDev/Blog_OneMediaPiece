<?php
// fichier testcontroller.php à la racine du projet
// pour tester un objet Contrôleur

    require_once(__DIR__ . "../controllers/RoleGetController.php") ;

    // simulation du formulaire
    $form = array('id' => 1) ;

    $objRoleGetController = new RoleGetController($form, 'RoleGetController') ;

    $objRole = $objRoleGetController->execute() ;

    echo '<br/><br/>Role 1<br/><br/>' ;

    echo json_encode($objRole) ;

//////////////////// User 10

    $form = array('id' => 10) ;

    $objRoleGetController = new RoleGetController($form, 'RoleGetController') ;

    $objRole = $objRoleGetController->execute() ;

//    var_dump($objCompte) ;

    echo '<br/><br/>Role 10<br/><br/>' ;

    echo json_encode($objRole) ;

?>
