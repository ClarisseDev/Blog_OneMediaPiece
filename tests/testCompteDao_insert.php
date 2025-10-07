<?php
// fichier testdao.php à la racine du projet
// pour tester un objet Dao (et Entity)

    require_once(__DIR__ .  "../dao/CompteDao.php") ;

    $objCompteDao = new CompteDao() ;

    $objCompte = $objCompteDao->findById(1) ;

//    var_dump($objCompte) ;

    echo '<br/><br/>Utilisateur 1<br/><br/>' ;

    echo json_encode($objCompte) ;

    echo '<br/><br/>Utilisateur 10<br/><br/>' ;

    echo json_encode($objCompteDao->findById(10)) ;

?>
