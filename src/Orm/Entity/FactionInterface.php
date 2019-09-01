<?php

namespace Stu\Orm\Entity;

interface FactionInterface
{
    public function getId(): int;

    public function getName(): string;

    public function setName(string $name): FactionInterface;

    public function getDescription(): string;

    public function setDescription(string $description): FactionInterface;

    public function getDarkerColor(): string;

    public function setDarkerColor(string $darkerColor): FactionInterface;

    public function getChooseable(): bool;

    public function setChooseable(bool $chooseable): FactionInterface;

    public function getPlayerLimit(): int;

    public function setPlayerLimit(int $playerLimit): FactionInterface;

    public function getPlayerAmount(): int;

    public function hasFreePlayerSlots(): bool;
}