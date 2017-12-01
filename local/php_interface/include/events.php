<?php


AddEventHandler("main", "OnBeforeEventAdd", array("MyClass", "OnBeforeEventAddHandler"));
class MyClass
{
    function OnBeforeEventAddHandler(&$event, &$lid, &$arFields)
    {
        if ($event == 'FEEDBACK_FORM') {
            global $USER;

            if ($USER->IsAuthorized()) {
                $arFields['AUTHOR'] = "Пользователь авторизован: " . $USER->GetID() . " (" . $USER->GetLogin() . ") " . $USER->GetFullName() . ", данные из формы: " . $arFields['AUTHOR'];
            } else {
                $arFields['AUTHOR'] = "Пользователь не авторизован, данные из формы: " . $arFields['AUTHOR'];
            }

            CEventLog::Add(array(
                "SEVERITY" => "SECURITY",
                "AUDIT_TYPE_ID" => "Замена данных в отсылаемом письме",
                "MODULE_ID" => "main",
                "DESCRIPTION" => "Замена данных в отсылаемом письме – " . $arFields['AUTHOR'],
            ));
        }
    }
}