<?php

namespace OneMediaPiece_blog\model;
use DateTime;
use OneMediaPiece_blog\utils\entity\IEntity;
use OneMediaPiece_blog\utils\entity\AbstractEntity;
use OneMediaPiece_blog\utils\Functions;

class Compte extends AbstractEntity implements IEntity 
{
	private int $idCompte;
	private string $login;
	private string $password;
	private DateTime $dateCreation;
	private DateTime $dateModification;

	private bool $enAttenteDeModeration;
	private bool $estSupprime;

	private Role $role;

	private bool $estSignale;
	private bool $estBanni;
	private string $pseudo;

	function __construct() { $this->role = new Role();  }

	function getIdCompte() : int 
	{
		return $this->idCompte;
	}

	function setIdCompte(int $id) 
	{
		$this->idCompte = $id;
	}

	function getLogin() : string 
	{
		return $this->login;
	}

	function setLogin(string $login) 
	{
		$this->login = $login;
	}

	function getPassword() : string 
	{
		return $this->password;
	}

	function setPassword(?string $pwd) 
	{
		$this->password = $pwd;
	}

	function getDateCreation() : DateTime 
	{
		return $this->dateCreation;
	}

	function setDateCreation(DateTime $date) 
	{
		$this->dateCreation = $date;
	}

	function getDateModification() : DateTime 
	{
		return $this->dateModification;
	}

	function setDateModification(DateTime $date) 
	{
		$this->dateModification = $date;
	}

	function getEnAttenteDeModeration() : bool 
	{
		return $this->enAttenteDeModeration;
	}

	function setEnAttenteDeModeration(bool $b) 
	{
		$this->enAttenteDeModeration = $b;
	}

	function getEstSupprime() : bool 
	{
		return $this->estSupprime;
	}

	function setEstSupprime(bool $b) 
	{
		$this->estSupprime = $b;
	}

	function getEstSignale() : bool 
	{
		return $this->estSignale;
	}

	function setEstSignale(bool $b) 
	{
		$this->estSignale = $b;
	}

	function setEstBanni(bool $b) 
	{
		$this->estBanni = $b;
	}

	function getEstBanni() : bool 
	{
		return $this->estBanni;
	}

	function getPseudo() : string 
	{
		return $this->pseudo;
	}

	function setPseudo(string $s) 
	{
		$this->pseudo = $s;
	}

	function getRole() : Role 
	{
		return $this->role;
	}

	function setRole(Role $Role) 
	{
		$this->role = $Role;
	}

	// CREATE
	// Créer à partir d'un tuple récupérer de la BDD
	// public static function createFromRow($row, bool $keepPassword = false) 
	// {
	// 	$compte = new Compte();
	// 	$compte->setIdCompte( intval($row->id_compte) );
	// 	$compte->setLogin( $row->login );
	// 	$compte->setPseudo($row->pseudo); 
	// 	$compte->setPassword( $keepPassword ? $row->password : NULL );
	// 	$compte->setDateCreation( new DateTime($row->dateCreation) );
	// 	$compte->setDateModification( new DateTime($row->dateModification) );
	// 	$compte->setEnAttenteDeModeration( boolval($row->enAttenteDeModeration) );
	// 	$compte->setEstSupprime( boolval($row->estSupprime) );
	// 	$compte->setEstSignale( boolval($row->estSignale) ); 
	// 	$compte->setEstBanni( boolval($row->estBanni) ); 
	// 	return $compte;
	// }
	public static function createFromRow($row, bool $keepPassword = false): Compte
	{
		$compte = new Compte();
		$compte->setIdCompte(intval($row->id_compte));
		$compte->setLogin($row->login);
		$compte->setPseudo($row->pseudo);
		$compte->setPassword($keepPassword ? $row->password : '');

		$dateCreation = new DateTime($row->dateCreation);
		$dateModification = new DateTime($row->dateModification);

		$compte->setDateCreation($dateCreation);
		$compte->setDateModification($dateModification);

		$compte->setEnAttenteDeModeration(boolval($row->enAttenteDeModeration ?? false));
		$compte->setEstSupprime(boolval($row->estSupprime ?? false));
		$compte->setEstSignale(boolval($row->estSignale ?? false));
		$compte->setEstBanni(boolval($row->estBanni ?? false));

		return $compte;
	}

/****************************************************************************************************************************** */
	// public static function create($email, $pseudo, $password) 
	// {
	// 	$compte = new Compte();
	// 	$compte->setLogin( $email );
	// 	$compte->setPseudo($pseudo); 
	// 	$compte->setPassword( $password );
	// 	$compte->setDateCreation( new DateTime() );
	// 	$compte->setDateModification( new DateTime() );
	// 	$compte->setEnAttenteDeModeration( true );
	//     $compte->setEstSupprime( false );
	// 	$compte->setEstSignale( false ); 
	// 	$compte->setEstBanni( false ); 
	// 	return $compte;
	// }
/****************************************************************************************************************************** */

	// RETREIVE
	// Créer un tuple à partir des data
	public static function createForCredential(string $login, string $password, ?string $pseudo = null) 
	{
		$compte = new Compte();
		$compte->setLogin( $login );
		$compte->setPassword( $password );
		if(!is_null($pseudo))
		{
			$compte->setPseudo( $pseudo );
		}
		return $compte;
	}

	public function toArray() : array
	{
		return [
			'id' => $this->getIdCompte(),
			'login' => $this->getLogin(),
			'pseudo' => $this->getPseudo(),
			'dateCreation' => $this->getDateCreation()->format('Y-m-d H:i:s'),
			'dateModification' => $this->getDateModification()->format('Y-m-d H:i:s'),
			'enAttenteDeModeration' => $this->getEnAttenteDeModeration(),
			'estSupprime' => $this->getEstSupprime(),
			'estSignale' => $this->getEstSignale(),
			'estBanni' => $this->getEstBanni(),
			'role' => $this->getRole()->toArray()
		];
	}

	// UPDATE
	public static function createFromForm($form) 
	{
		// var_dump($form);
		$compte = new Compte();
		$compte->setIdCompte( intval($form['id_compte'] ) );
		$compte->setLogin( $form['login'] );
		$compte->setPseudo($form['pseudo']); 
		$compte->setPassword( $form['password']);
		$compte->setDateCreation( new DateTime($form['dateCreation']) );
		$compte->setDateModification( new DateTime($form['dateModification']));
		$compte->setEnAttenteDeModeration( Functions::stringToBool($form['enAttenteDeModeration']) );
		$compte->setEstSupprime( Functions::stringToBool($form['estSupprime']) );
		$compte->setEstSignale( Functions::stringToBool($form['estSignale']) ); 
		$compte->setEstBanni( Functions::stringToBool($form['estBanni']) ); 
		$compte->setRole( $form['role'] );
		return $compte;
	}

}

?>