<?php

namespace OneMediaPiece_blog\utils\sessioninfo;

interface ISessionInfoProvider {
	
	function getCompteId(): int;
	
	function getRoleId(): int;
	
}

