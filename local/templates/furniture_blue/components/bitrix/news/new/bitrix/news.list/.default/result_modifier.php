<?php


if ($arParams['SET_SPECIALDATE'] == 'Y') {
    $arResult['FIRST_NEWS_DATE'] = $arResult['ITEMS'][0]['ACTIVE_FROM'];

    $this->__component->SetResultCacheKeys(array("FIRST_NEWS_DATE"));
}