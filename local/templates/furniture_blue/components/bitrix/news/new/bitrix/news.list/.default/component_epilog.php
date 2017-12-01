<?php


if (isset($arResult['FIRST_NEWS_DATE'])) {
    $APPLICATION->SetPageProperty("specialdate", $arResult['FIRST_NEWS_DATE']);
}