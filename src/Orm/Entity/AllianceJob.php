<?php

declare(strict_types=1);

namespace Stu\Orm\Entity;

use Alliance;
use User;

/**
 * @Entity(repositoryClass="Stu\Orm\Repository\AllianceJobRepository")
 * @Table(
 *     name="stu_alliances_jobs",
 *     indexes={
 *     }
 * )
 **/
class AllianceJob implements AllianceJobInterface
{
    /** @Id @Column(type="integer") @GeneratedValue * */
    private $id;

    /** @Column(type="integer") * */
    private $alliance_id = 0;

    /** @Column(type="integer") * */
    private $user_id = 0;

    /** @Column(type="smallint") * */
    private $type = 0;

    public function getId(): int
    {
        return $this->id;
    }

    public function getAllianceId(): int
    {
        return $this->alliance_id;
    }

    public function setAllianceId(int $allianceId): AllianceJobInterface
    {
        $this->alliance_id = $allianceId;

        return $this;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $userId): AllianceJobInterface
    {
        $this->user_id = $userId;

        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): AllianceJobInterface
    {
        $this->type = $type;

        return $this;
    }

    public function getAlliance(): Alliance
    {
        return new Alliance($this->getAllianceId());
    }

    public function getUser(): User
    {
        return new User($this->getUserId());
    }
}