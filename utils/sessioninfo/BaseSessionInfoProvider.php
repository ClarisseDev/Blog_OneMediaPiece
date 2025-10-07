<?php

namespace OneMediaPiece_blog\utils\sessioninfo;

use OneMediaPiece_blog\utils\sessioninfo\ISessionInfoProvider;
use OneMediaPiece_blog\utils\SessionManager;

class BaseSessionInfoProvider implements ISessionInfoProvider {

	private SessionManager $sessionManager;
	
	public function __construct(SessionManager $sessionManager) {
		$this->sessionManager = $sessionManager;
	}
	
	public function getCompteId() : int {
		return $this->sessionManager->getCompteIdFromSession();
	}

	public function getRoleId() : int {
		return $this->sessionManager->getRoleIdFromSession();
	}
}

