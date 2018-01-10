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

if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 180;

$arParams['IBLOCK_CATALOG'] = intval($arParams['IBLOCK_CATALOG']);
$arParams['IBLOCK_NEWS'] = intval($arParams['IBLOCK_NEWS']);

if (! $arParams['IBLOCK_CATALOG'] || ! $arParams['IBLOCK_NEWS'])
    return;

if ($this->StartResultCache()) {
    if (!CModule::IncludeModule("iblock")) {
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


    $rsSelect = array(
        "ID",
        "NAME",
        "IBLOCK_SECTION_ID",
        "PROPERTY_MATERIAL",
        "PROPERTY_PRICE",
        "PROPERTY_ARTNUMBER",
    );
    $rsFilter = array(
        "IBLOCK_ID" => $arParams['IBLOCK_CATALOG'],
        "ACTIVE" => "Y",
    );

    $arElements = CIBlockElement::GetList(array(), $rsFilter, false, false, $rsSelect);

    while ($element = $arElements->Fetch()) {
        foreach ($catalogByNews as $news_id => $news_value) {
            foreach ($news_value['SECTIONS'] as $sect_id => $sect_value) {
                if ($element['IBLOCK_SECTION_ID'] == $sect_id) {
                    $catalogByNews[$news_id]['ITEMS'][$element['ID']] = array(
                        "NAME" => $element['NAME'],
                        "MATERIAL" => $element['PROPERTY_MATERIAL_VALUE'],
                        "PRICE" => $element['PROPERTY_PRICE_VALUE'],
                        "ARTNUMBER" => $element['PROPERTY_ARTNUMBER_VALUE'],
                    );
                }
            }
        }
    }


    $rsSelect = array("ID", "NAME", "ACTIVE_FROM");
    $rsFilter = array(
        "IBLOCK_ID" => $arParams['IBLOCK_NEWS'],
        "ACTIVE" => "Y",
    );

    $arNews = CIBlockElement::GetList(array(), $rsFilter, false, false, $rsSelect);
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
        "COUNT_ITEMS" => $allItems,
        "NEWS" => $catalogByNews
    );


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


    $this->setResultCacheKeys(array("COUNT_ITEMS"));

    $this->IncludeComponentTemplate();
    $this->endResultCache();

    $APPLICATION->SetTitle('В каталоге товаров представлено товаров: ' . $arResult['COUNT_ITEMS']);
} else {
    $this->AbortResultCache();
}
?>
