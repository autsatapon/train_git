<?php

function getRequestLocale()
{
    return convertLang(Input::get('lang'));
}

function convertLang($lang)
{
    switch ($lang)
    {
        case 'th':
            return 'th_TH';
        case 'en':
            return 'en_US';
        case 'ja':
            return 'ja_JP';
        case 'zh':
            return 'zh_CN';
        case 'vi':
            return 'vi_VN';
        default:
            return 'th_TH';
    }
}