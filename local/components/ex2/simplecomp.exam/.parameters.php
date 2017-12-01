<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
        "IBLOCK_CATALOG" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("IBLOCK_CATALOG"),
            "TYPE" => "STRING",
        ),
        "IBLOCK_NEWS" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("IBLOCK_NEWS"),
            "TYPE" => "STRING",
        ),
        "USER_PROPERTY" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("USER_PROPERTY"),
            "TYPE" => "STRING",
        ),
        "CACHE_TIME"  =>  Array("DEFAULT"=>180),
	),
);
?>
