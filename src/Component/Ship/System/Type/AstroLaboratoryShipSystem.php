<?php

declare(strict_types=1);

namespace Stu\Component\Ship\System\Type;

use Stu\Component\Ship\System\ShipSystemModeEnum;
use Stu\Component\Ship\System\ShipSystemTypeEnum;
use Stu\Component\Ship\System\ShipSystemTypeInterface;
use Stu\Orm\Entity\ShipInterface;

final class AstroLaboratoryShipSystem extends AbstractShipSystemType implements ShipSystemTypeInterface
{
    public function activate(ShipInterface $ship): void
    {
        //$ship->getShipSystem(ShipSystemTypeEnum::SYSTEM_SHIELDS)->setMode(ShipSystemModeEnum::MODE_ON);
    }

    public function deactivate(ShipInterface $ship): void
    {
        //$ship->getShipSystem(ShipSystemTypeEnum::SYSTEM_SHIELDS)->setMode(ShipSystemModeEnum::MODE_OFF);
    }

    public function handleDestruction(ShipInterface $ship): void
    {
    }

    public function handleDamage(ShipInterface $ship): void
    {
    }
}
