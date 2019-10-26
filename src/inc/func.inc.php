<?php

use PhpTal\Php\TalesInternal;
use PhpTal\TalesRegistry;
use Stu\Module\Tal\StatusBarColorEnum;
use Stu\Orm\Entity\ColonyInterface;
use Stu\Orm\Entity\ModuleInterface;
use Stu\Orm\Entity\PlanetFieldInterface;
use Stu\Orm\Entity\ShipInterface;
use Stu\Orm\Entity\ShipRumpInterface;

function checkPosition(ShipInterface $shipa, ShipInterface $shipb)
{
    if ($shipa->getSystem() !== null) {
        if ($shipb->getSystem() === null || $shipa->getSystem()->getId() !== $shipb->getSystem()->getId()) {
            return false;
        }
        if ($shipa->getSX() != $shipb->getSX() || $shipa->getSY() != $shipb->getSY()) {
            return false;
        }
        return true;
    }
    if ($shipa->getCX() != $shipb->getCX() || $shipa->getCY() != $shipb->getCY()) {
        return false;
    }
    return true;
}

function checkColonyPosition(ColonyInterface $col, ShipInterface $ship)
{
    if ($col->getSystemsId() != $ship->getSystem()->getId()) {
        return false;
    }
    if ($col->getSX() != $ship->getSX() || $col->getSY() != $ship->getSY()) {
        return false;
    }
    return true;
}

function compareBuildings(PlanetFieldInterface$a, PlanetFieldInterface $b)
{
    if ($a->getBuilding()->getId() == $b->getBuilding()->getId()) {
        return $a->getId() > $b->getId();
    }
    return strcmp($a->getBuilding()->getName(), $b->getBuilding()->getName());
}

function renderResearchStatusBar($points, &$maxpoints)
{
    $pro = getPercentage($points, $maxpoints);
    $bar = getStatusBar(StatusBarColorEnum::STATUSBAR_BLUE, ceil($pro / 2) * 2, 'Fortschritt: ' . $points . '/' . $maxpoints);
    if ($pro < 100) {
        $bar .= getStatusBar(StatusBarColorEnum::STATUSBAR_GREY, floor((100 - $pro) / 2) * 2, 'Fortschritt: ' . $points . '/' . $maxpoints);
    }
    return $bar;
}

function renderShieldStatusBar(&$active, &$shields, &$maxshields)
{
    $pro = getPercentage($shields, $maxshields);
    $bar = getStatusBar(($active == 1 ? StatusBarColorEnum::STATUSBAR_BLUE : StatusBarColorEnum::STATUSBAR_DARKBLUE), ceil($pro / 2),
        'Schilde: ' . $shields . '/' . $maxshields);
    if ($pro < 100) {
        $bar .= getStatusBar(StatusBarColorEnum::STATUSBAR_GREY, floor((100 - $pro) / 2), 'Schilde: ' . $shields . '/' . $maxshields);
    }
    return $bar;
}

function getStatusBar($color, $amount, $title = '')
{
    return '<img src="assets/bars/balken.png" style="background-color: #' . $color . ';height: 12px; width:' . round($amount) . 'px;" title="' . $title . '" />';
}

function getPercentageStatusBar($color, $amount, $maxamount)
{
    $pro = getPercentage($amount, $maxamount);
    $bar = getStatusBar($color, ceil($pro / 2), 'Status: ' . $pro . '%');
    if ($pro < 100) {
        $bar .= getStatusBar(StatusBarColorEnum::STATUSBAR_GREY, floor((100 - $pro) / 2), 'Status: ' . $pro . '%');
    }
    return $bar;
}

function getPercentage($val, $maxval)
{
    if ($val > $maxval) {
        $val = $maxval;
    }
    return max(0, @round((100 / $maxval) * min($val, $maxval)));
}

function renderHuellStatusBar(&$huell, &$maxhuell)
{
    $pro = getPercentage($huell, $maxhuell);
    $bar = getStatusBar(StatusBarColorEnum::STATUSBAR_GREEN, ceil($pro / 2), 'Hülle: ' . $huell . '/' . $maxhuell);
    if ($pro < 100) {
        $bar .= getStatusBar(StatusBarColorEnum::STATUSBAR_GREY, floor((100 - $pro) / 2), 'Hülle: ' . $huell . '/' . $maxhuell);
    }
    return $bar;
}

function renderStorageStatusBar(&$storage, &$maxstorage)
{
    $pro = getPercentage($storage, $maxstorage);
    $bar = getStatusBar(StatusBarColorEnum::STATUSBAR_GREEN, ceil($pro / 2), 'Lager: ' . $storage . '/' . $maxstorage . ' (' . $pro . '%)');
    if ($pro < 100) {
        $bar .= getStatusBar(StatusBarColorEnum::STATUSBAR_GREY, floor((100 - $pro) / 2),
            'Lager: ' . $storage . '/' . $maxstorage . ' (' . $pro . '%)');
    }
    return $bar;
}

function renderEpsStatusBar(&$eps, &$maxeps)
{
    $pro = getPercentage($eps, $maxeps);
    $bar = getStatusBar(StatusBarColorEnum::STATUSBAR_YELLOW, ceil($pro / 2), 'Energie: ' . $eps . '/' . $maxeps);
    if ($pro < 100) {
        $bar .= getStatusBar(StatusBarColorEnum::STATUSBAR_GREY, floor((100 - $pro) / 2), 'Energie: ' . $eps . '/' . $maxeps);
    }
    return $bar;
}

/**
 */
function calculateModuleValue(ShipRumpInterface $rump, ModuleInterface $module, $callback = 'aggi', $value = false): int
{ #{{{
    if (!$value) {
        $value = $rump->$callback();
    }
    if ($rump->getModuleLevel() > $module->getLevel()) {
        return (int)round($value - $value / 100 * $module->getDowngradeFactor());
    }
    if ($rump->getModuleLevel() < $module->getLevel()) {
        return (int)round($value + $value / 100 * $module->getUpgradeFactor());
    }
    return (int)$value;
} # }}}

/**
 */
function calculateDamageImpact(ShipRumpInterface $rump, ModuleInterface $module)
{ #{{{
    if ($rump->getModuleLevel() > $module->getLevel()) {
        return '-' . $module->getDowngradeFactor() . '%';
    }
    if ($rump->getModuleLevel() < $module->getLevel()) {
        return '+' . $module->getUpgradeFactor() . '%';
    }
    return _('Normal');
} # }}}

/**
 */
function calculateEvadeChance(ShipRumpInterface $rump, ModuleInterface $module): int
{ #{{{
    $base = $rump->getEvadeChance();
    if ($rump->getModuleLevel() > $module->getLevel()) {
        $value = (1 - $base / 100) * 1 / (1 - $module->getDowngradeFactor() / 100);
    } elseif ($rump->getModuleLevel() < $module->getLevel()) {
        $value = (1 - $base / 100) * 1 / (1 + $module->getUpgradeFactor() / 100);
    } else {
        return $base;
    }
    return (int)round((1 - $value) * 100);
} # }}}

TalesRegistry::registerPrefix(
    'clmodeDescription',
    function ($src, $nothrow): string {
        return '\Stu\Module\Tal\TalHelper::getContactListModeDescription((int) ' . TalesInternal::compileToPHPExpression($src, $nothrow) . ')';
    }
);
TalesRegistry::registerPrefix(
    'addPlusCharacter',
    function ($src, $nothrow): string {
        return '\Stu\Module\Tal\TalHelper::addPlusCharacter((int)' . TalesInternal::compileToPHPExpression($src, $nothrow) . ')';
    }
);
TalesRegistry::registerPrefix(
    'isPositive',
    function ($src, $nothrow): string {
        return '(int) ' . TalesInternal::compileToPHPExpression($src, $nothrow) . ' > 0';
    }
);
TalesRegistry::registerPrefix(
    'isNegative',
    function ($src, $nothrow): string {
        return '(int) ' . TalesInternal::compileToPHPExpression($src, $nothrow) . ' < 0';
    }
);

TalesRegistry::registerPrefix(
    'bbcode',
    function ($src, $nothrow): string {
        return '\Stu\Module\Tal\TalHelper::getBBCodeParser()->parse(' . TalesInternal::compileToPHPExpression($src, $nothrow) . ')->getAsHtml()';
    }
);
TalesRegistry::registerPrefix(
    'bbcode2txt',
    function ($src, $nothrow): string {
        return '\Stu\Module\Tal\TalHelper::getBBCodeParser()->parse(' . TalesInternal::compileToPHPExpression($src, $nothrow) . ')->getAsText()';
    }
);
TalesRegistry::registerPrefix(
    'jsquote',
    function ($src, $nothrow): string {
        return '\Stu\Module\Tal\TalHelper::jsquote(' . TalesInternal::compileToPHPExpression($src, $nothrow) . ')';
    }
);
TalesRegistry::registerPrefix(
    'datetime',
    function ($src, $nothrow): string {
        return 'date(\'d.m.Y H:i\', ' . TalesInternal::compileToPHPExpression($src, $nothrow) . ')';
    }
);
TalesRegistry::registerPrefix(
    'date',
    function ($src, $nothrow): string {
        return 'date(\'d.m.Y\', ' . TalesInternal::compileToPHPExpression($src, $nothrow) . ')';
    }
);
TalesRegistry::registerPrefix(
    'nl2br',
    function ($src, $nothrow): string {
        return 'nl2br(' . TalesInternal::compileToPHPExpression($src, $nothrow) . ')';
    }
);
TalesRegistry::registerPrefix(
    'formatSeconds',
    function ($src, $nothrow): string {
        return '\Stu\Module\Tal\TalHelper::formatSeconds(' . TalesInternal::compileToPHPExpression($src, $nothrow) . ')';
    }
);
