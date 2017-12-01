<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */


/*************************************************************************
    Processing of received parameters
*************************************************************************/
if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 180;

$arParams['IBLOCK_CATALOG'] = intval($arParams['IBLOCK_CATALOG']);
$arParams['IBLOCK_NEWS'] = intval($arParams['IBLOCK_NEWS']);




if ($this->StartResultCache()) {
    if(!CModule::IncludeModule("iblock"))
    {
        ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
        return;
    }

    $catalogByNews = array();

    $rsSelect = array(
        $arParams['USER_PROPERTY']
    );
    $rsFilter = array(
        "IBLOCK_ID" => $arParams['IBLOCK_CATALOG'],
        "ACTIVE" => "Y",
        "!".$arParams['USER_PROPERTY'] => false
    );

    $arSections = CIBlockSection::GetList(array(), $rsFilter, false, $rsSelect);

    while ($section = $arSections->GetNext()) {
        foreach ($section[$arParams['USER_PROPERTY']] as $k => $v) {
            $catalogByNews[$v]['SECTIONS'][$section['ID']] = array(
                "NAME" => $section['NAME']
            );
        }
    }



    $rsSelect = array();
    $rsFilter = array(
        "IBLOCK_ID" => $arParams['IBLOCK_CATALOG'],
        "ACTIVE" => "Y",
    );

    $arElements = CIBlockElement::GetList(array(), $rsFilter, false, $rsSelect);

    while ($element = $arElements->GetNextElement()) {
        $elFields = $element->GetFields();
        $elProps = $element->GetProperties();

        foreach ($catalogByNews as $news_id => $news_value) {
            foreach ($news_value['SECTIONS'] as $sect_id => $sect_value) {
                if ($elFields['IBLOCK_SECTION_ID'] == $sect_id) {
                    $catalogByNews[$news_id]['ITEMS'][$elFields['ID']] = array(
                        "NAME" => $elFields['NAME'],
                        "MATERIAL" => $elProps['MATERIAL']['VALUE'],
                        "PRICE" => $elProps['PRICE']['VALUE'],
                        "ARTNUMBER" => $elProps['ARTNUMBER']['VALUE'],
                    );
                }
            }
        }
    }


    $rsSelect = array();
    $rsFilter = array(
        "IBLOCK_ID" => $arParams['IBLOCK_NEWS'],
        "ACTIVE" => "Y",
    );


    $arNews = CIBlockElement::GetList(array(), $rsFilter, false, $rsSelect);
    while ($one_news = $arNews->GetNext()) {
        foreach ($catalogByNews as $news_id => $v) {
            if ($one_news['ID'] == $news_id) {
                $catalogByNews[$news_id]["NAME"] = $one_news['NAME'];
                $catalogByNews[$news_id]["ACTIVE_FROM"] = $one_news['ACTIVE_FROM'];
            }
        }
    }

    $allItems = array();


    foreach ($catalogByNews as $k1 => $v1) {
        foreach ($v1['ITEMS'] as $item_id => $item) {
            $allItems[$item_id] = $item;
        }
    }

    $arResult = array(
        "ALL_ITEMS" => $allItems,
        "NEWS" => $catalogByNews
    );

    $APPLICATION->SetTitle('В каталоге товаров представлено товаров: ' . count($allItems));


    if ($APPLICATION->GetShowIncludeAreas()) {

        $IBLOCK = GetIBlock($arParams['IBLOCK_CATALOG']);

        $urlIBLOCK = "/bitrix/admin/iblock_section_admin.php?IBLOCK_ID=" . $IBLOCK['ID'] . "&type=" . $IBLOCK['IBLOCK_TYPE_ID'] . "&lang=ru&find_section_section=0";

        $this->AddIncludeAreaIcon(
            array(
                'URL'   => $urlIBLOCK,
                'TITLE' => "ИБ в админке",
                "IN_PARAMS_MENU" => true
            )
        );
    }


    $this->IncludeComponentTemplate();
} else {
    $this->AbortResultCache();
}











/*
if(empty($arIBlockFilter))
{

    $rsIBlocks = CIBlock::GetList(array("sort" => "asc"), array(
        "type" => $arParams["IBLOCK_TYPE"],
        "LID" => SITE_ID,
        "ACTIVE" => "Y",
    ));
    if($arIBlock = $rsIBlocks->Fetch())
        $arIBlockFilter[] = $arIBlock["ID"];
}

unset($arParams["IBLOCK_TYPE"]);
$arParams["PARENT_SECTION"] = intval($arParams["PARENT_SECTION"]);
$arParams["IBLOCKS"] = $arIBlockFilter;

if(!empty($arIBlockFilter) && $this->StartResultCache(false, ($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups())))
{
    if(!CModule::IncludeModule("iblock"))
    {
        $this->AbortResultCache();
        ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
        return;
    }
    //SELECT
    $arSelect = array(
        "ID",
        "IBLOCK_ID",
        "CODE",
        "IBLOCK_SECTION_ID",
        "NAME",
        "PREVIEW_PICTURE",
        "DETAIL_PICTURE",
        "DETAIL_PAGE_URL",
    );
    //WHERE
    $arFilter = array(
        "IBLOCK_ID" => $arParams["IBLOCKS"],
        "ACTIVE_DATE" => "Y",
        "ACTIVE"=>"Y",
        "CHECK_PERMISSIONS"=>"Y",
    );
    if($arParams["PARENT_SECTION"]>0)
    {
        $arFilter["SECTION_ID"] = $arParams["PARENT_SECTION"];
        $arFilter["INCLUDE_SUBSECTIONS"] = "Y";
    }
    //ORDER BY
    $arSort = array(
        "RAND"=>"ASC",
    );
    //EXECUTE
    $rsIBlockElement = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
    $rsIBlockElement->SetUrlTemplates($arParams["DETAIL_URL"]);
    if($arResult = $rsIBlockElement->GetNext())
    {
        $arResult["PICTURE"] = CFile::GetFileArray($arResult["PREVIEW_PICTURE"]);
        if(!is_array($arResult["PICTURE"]))
            $arResult["PICTURE"] = CFile::GetFileArray($arResult["DETAIL_PICTURE"]);

        $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arResult["IBLOCK_ID"], $arResult["ID"]);
        $arResult["IPROPERTY_VALUES"] = $ipropValues->getValues();

        if ($arResult["PICTURE"])
        {
            $arResult["PICTURE"]["ALT"] = $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"];
            if ($arResult["PICTURE"]["ALT"] == "")
                $arResult["PICTURE"]["ALT"] = $arResult["NAME"];
            $arResult["PICTURE"]["TITLE"] = $arResult["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"];
            if ($arResult["PICTURE"]["TITLE"] == "")
                $arResult["PICTURE"]["TITLE"] = $arResult["NAME"];
        }

        $this->SetResultCacheKeys(array(
        ));
        $this->IncludeComponentTemplate();
    }
    else
    {
        $this->AbortResultCache();
    }
}*/
?>
