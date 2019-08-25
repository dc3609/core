<?php

declare(strict_types=1);

namespace Stu\Module\Colony\View\ShowShipyard;

use BuildingFunctions;
use ColonyMenu;
use request;
use Shiprump;
use Stu\Control\GameControllerInterface;
use Stu\Control\ViewControllerInterface;
use Stu\Module\Colony\Lib\ColonyGuiHelperInterface;
use Stu\Module\Colony\Lib\ColonyLoaderInterface;

final class ShowShipyard implements ViewControllerInterface
{
    public const VIEW_IDENTIFIER = 'SHOW_SHIPYARD';

    private $colonyLoader;

    private $colonyGuiHelper;

    public function __construct(
        ColonyLoaderInterface $colonyLoader,
        ColonyGuiHelperInterface $colonyGuiHelper
    ) {
        $this->colonyLoader = $colonyLoader;
        $this->colonyGuiHelper = $colonyGuiHelper;
    }

    public function handle(GameControllerInterface $game): void
    {
        $userId = $game->getUser()->getId();

        $colony = $this->colonyLoader->byIdAndUser(
            request::indInt('id'),
            $userId
        );

        $this->colonyGuiHelper->register($colony, $game);

        $func_id = request::getIntFatal('func');
        $function = new BuildingFunctions($func_id);
        $buildableShips = Shiprump::getBuildableRumpsByBuildingFunction($userId, $function->getFunction());

        $game->setTemplateFile('html/ajaxempty.xhtml');
        $game->setAjaxMacro('html/colonymacros.xhtml/cm_shipyard');

        $game->setTemplateVar('COLONY', $colony);
        $game->setTemplateVar('COLONY_MENU_SELECTOR', new ColonyMenu(MENU_SHIPYARD));

        $game->setTemplateVar('BUILDABLE_SHIPS', $buildableShips);
        $game->setTemplateVar('BUILDABLE_RUMPS', Shiprump::getBuildableRumpsByUser($userId));
    }
}