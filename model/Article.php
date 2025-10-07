<?php
namespace OneMediaPiece_blog\model;
use DateTime;
use OneMediaPiece_blog\utils\entity\IEntity;
use OneMediaPiece_blog\utils\entity\AbstractEntity;

class Article extends AbstractEntity implements IEntity 
{
	private int $idArticle;
	private string $titre;
	private string $contenu;
	private DateTime $dateCreation;
	private DateTime $dateModification;
	private bool $estPublic;
	private bool $estEnAttenteDeModeration;
	private bool $estSupprime;

	private Compte $auteur;
	private Compte $moderateur;
	private DateTime $dateModeration;
	private string $motifModeration;

	public function __construct() { /* RAS */ }

	public function getidArticle() {
    return $this->idArticle;
	}

	public function setidArticle($idArticle) {
		$this->idArticle = $idArticle;
	}

	public function getTitre() : string{
	return $this->titre;
	}

	public function setTitre(string $titre)  {
		$this->titre = $titre;
	}

	public function getContenu() : string{
		return $this->contenu;
	}

	public function setContenu(string $contenu) {
		$this->contenu = $contenu;
	}

	public function getDateCreation() : DateTime{
		return $this->dateCreation;
	}

	public function setDateCreation(DateTime $dateCreation) {
		$this->dateCreation = $dateCreation;
	}

	public function getDateModification() : DateTime {
		return $this->dateModification;
	}

	public function setDateModification(DateTime $dateModification) {
		$this->dateModification = $dateModification;
	}

	public function getEstPublic() : bool {
		return $this->estPublic;
	}

	public function setEstPublic(bool $estPublic) {
		$this->estPublic = $estPublic;
	}

	public function getEstEnAttenteDeModeration() : bool {
		return $this->estEnAttenteDeModeration;
	}

	public function setEstEnAttenteDeModeration(bool $estEnAttenteDeModeration) {
		$this->estEnAttenteDeModeration = $estEnAttenteDeModeration;
	}

	public function getEstSupprime() : bool{
		return $this->estSupprime;
	}

	public function setEstSupprime(bool $estSupprime) {
		$this->estSupprime = $estSupprime;
	}

	public function getAuteur() : Compte {
		return $this->auteur;
	}

	public function setAuteur(Compte $auteur) {
		$this->auteur = $auteur;
	}

	public function getModerateur() : Compte {
		return $this->moderateur;
	}

	public function setModerateur(Compte $moderateur) {
		$this->moderateur = $moderateur;
	}

	public function getDateModeration() : DateTime {
		return $this->dateModeration;
	}

	public function setDateModeration(DateTime $dateModeration) {
		$this->dateModeration = $dateModeration;
	}

	public function getMotifModeration() : string {
		return $this->motifModeration;
	}

	public function setMotifModeration(?string $motifModeration): void {
		$this->motifModeration = $motifModeration;
	}

	// CREATE
	// Créer à partir d'un tuple récupérer de la BDD
	public static function createFromRow($row) 
	{
		$article = new Article();
		$article->setidArticle( intval($row->id_article) );
		$article->setTitre($row->titre );
		$article->setContenu($row->contenu); 
		$article->setDateCreation(new DateTime($row->dateCreation) );
		$article->setDateModification(new DateTime($row->dateModification) );
		$article->setEstEnAttenteDeModeration(boolval($row->enAttenteDeModeration) );
		$article->setEstPublic(boolval($row->estPublic) );
		$article->setEstSupprime(boolval($row->estSupprime) ); 
		$article->setAuteur (Compte::createFromRow($row, false) ); // false car on ne veut pas les infos de compte
		$article->setModerateur (Compte::createFromRow($row, false) ); // false car on ne veut pas les infos de compte
		$article->setDateModeration (new DateTime($row->dateModeration) );
		$article->setMotifModeration ($row->motifModeration );
		return $article;
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
	public static function createForCredential(string $titre, string $contenu)
	{
		$article = new Article();
		$article->setTitre($titre);
		$article->setContenu($contenu);
		return $article;
	}

	// UPDATE
	public static function createFromForm($form) 
	{
		// var_dump($form);
		$article = new Article();
		$article->setidArticle( intval($form['id_article'] ) );
		$article->setTitre( $form['titre'] );
		$article->setContenu( $form['contenu']);
		$article->setDateCreation( new DateTime($form['dateCreation']) );
		$article->setDateModification( new DateTime($form['dateModification']) );
		$article->setEstEnAttenteDeModeration( boolval($form['enAttenteDeModeration']) );
	    $article->setEstPublic( boolval($form['estPublic']) );
		$article->setEstSupprime( boolval($form['estSupprime']) );
		$article->setMotifModeration( $form['motifModeration'] );
		return $article;
	}

}

?>