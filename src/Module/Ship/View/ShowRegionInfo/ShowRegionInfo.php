<?php

declare(strict_types=1);

namespace Stu\Module\Ship\View\ShowRegionInfo;

use AccessViolation;
use request;
use Stu\Control\GameControllerInterface;
use Stu\Control\ViewControllerInterface;
use Stu\Module\Ship\Lib\ShipLoaderInterface;

final class ShowRegionInfo implements ViewControllerInterface
{
    public const VIEW_IDENTIFIER = 'SHOW_REGION_INFO';

    private $shipLoader;

    public function __construct(
        ShipLoaderInterface $shipLoader
    ) {
        $this->shipLoader = $shipLoader;
    }

    public function handle(GameControllerInterface $game): void
    {
        $userId = $game->getUser()->getId();

        $ship = $this->shipLoader->getByIdAndUser(
            request::indInt('id'),
            $userId
        );

        $regionId = request::getIntFatal('region');

        $mapField = $ship->getCurrentMapField();
        $region = $mapField->getMapRegion();

        if (!$mapField->hasRegion()) {
            throw new AccessViolation();
        }
        if ($region->getId() != $regionId) {
            throw new AccessViolation();
        }

        $game->checkDatabaseItem($region->getDatabaseId());

        $game->setTemplateFile('html/ajaxwindow.xhtml');
        $game->setAjaxMacro('html/shipmacros.xhtml/regioninfo');
        $game->setPageTitle(sprintf('Details: %s', $region->getDescription()));

        $game->setTemplateVar('REGION', $region);
        $game->setTemplateVar('SHIP', $ship);
    }
}