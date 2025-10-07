<?php
// https://dev.mysql.com/doc/mysql-errors/9.2/en/server-error-reference.html#error_er_no_such_table
namespace OneMediaPiece_blog\utils;

use OneMediaPiece_blog\exceptions\SqlConnectionRefusedException;
use OneMediaPiece_blog\exceptions\SqlConstraintForeignKeyException;
use OneMediaPiece_blog\exceptions\SqlConstraintUniqueException;
use OneMediaPiece_blog\exceptions\SqlAccessDeniedException;
use OneMediaPiece_blog\exceptions\SqlNumericOutOfRangeException;
use Exception;
use PDOException;
use OneMediaPiece_blog\utils\logs\LOG_LEVEL;
use OneMediaPiece_blog\utils\logs\Logger;
class Exceptions {

	public static function wrapPDOException(PDOException $ex) {
		$code = intval($ex->getCode());
		switch ($code) {
			case 22003 : $newEx = self::_22003($ex);
			if ( isset($newEx)) {
				return $newEx;
			}
			break;
			case 23000 : $newEx = self::_23000($ex);
				if ( isset($newEx)) {
					return $newEx;
				}
				break;
			case 1044 : case 1045 :
				$lclSplit = explode("]", $ex->getMessage());
				return new SqlAccessDeniedException( trim( end( $lclSplit ), "'" ), $code );
			case 2002 :
				$lclSplit = explode("]", $ex->getMessage());
				return new SqlConnectionRefusedException( trim( end( $lclSplit ), "'" ), $code );
		}
		Logger::logInErrorLog("PDO Exception not managed " . get_class($ex) . ", message : '" . $ex->getMessage() . "'", level: LOG_LEVEL::FATAL);
		return $ex;
	}
	
	private static function _22003(PDOException $ex) : ?Exception {
		$splitColon = explode( ":", $ex->getMessage() );
		$codePart = trim( $splitColon[2] );
		$split = explode( " ", $codePart );
		$dbErrorCode = intval( $split[0] );
		switch ($dbErrorCode) {
			case 1264 : return new SqlNumericOutOfRangeException( "Column " . trim( $split[7], "'" ), $dbErrorCode );
		}
		return null;
	}
	
	private static function _23000(PDOException $ex) : ?Exception {
		$splitColon = explode( ":", $ex->getMessage() );
		$codePart = trim( $splitColon[2] );
		$split = explode( " ", $codePart );
		$dbErrorCode = intval( $split[0] );
		switch ($dbErrorCode ) {
			case 1062 : return new SqlConstraintUniqueException( trim( end( $split ), "'" ), $dbErrorCode );
			case 1451 : return new SqlConstraintForeignKeyException( trim( $splitColon[3], "'" ), $dbErrorCode );
		}
		return null;
	}
}

?>