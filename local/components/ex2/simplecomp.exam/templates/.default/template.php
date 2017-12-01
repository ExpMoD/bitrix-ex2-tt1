<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$frame = $this->createFrame()->begin('');


?>
<b>Каталог:</b>
<ul>
<? foreach ($arResult['NEWS'] as $news): ?>
    <?
        $allSections = array();
        foreach ($news['SECTIONS'] as $section) {
            $allSections[] = $section['NAME'];
        }
    ?>
    <li>
        <b><?=$news['NAME']?></b> - <?=$news['ACTIVE_FROM']?> (<?=implode(', ', $allSections)?>)
        <ul>
            <? foreach ($news['ITEMS'] as $item): ?>
                <li>
                    <?=$item['NAME']?>
                    <?=($item['PRICE']) ? " - " . $item['PRICE'] : ""?>
                    <?=($item['MATERIAL']) ? " - " . $item['MATERIAL'] : ""?>
                    <?=($item['ARTNUMBER']) ? " - " . $item['ARTNUMBER'] : ""?>
                </li>
            <? endforeach; ?>
        </ul>
    </li>

<? endforeach; ?>
</ul>

<?
$frame->end();
?>