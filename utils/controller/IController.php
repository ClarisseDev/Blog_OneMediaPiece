<?php
namespace OneMediaPiece_blog\utils\controller;

interface IController
{
    public function execute() : string;

    public function getName() : string;
}

?>