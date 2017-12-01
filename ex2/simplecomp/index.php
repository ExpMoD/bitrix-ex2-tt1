<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Простой компонент");
?><?$APPLICATION->IncludeComponent(
	"ex2:simplecomp.exam", 
	".default", 
	array(
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "180",
		"CACHE_TYPE" => "A",
		"DETAIL_URL" => "",
		"IBLOCKS" => "",
		"IBLOCK_TYPE" => "news",
		"PARENT_SECTION" => "",
		"COMPONENT_TEMPLATE" => ".default",
		"IBLOCK_CATALOG" => "2",
		"IBLOCK_NEWS" => "1",
		"USER_PROPERTY" => "UF_NEWS_LINK"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>