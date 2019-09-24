<?php

declare(strict_types=1);

namespace Stu\Module\Api\V1\Common\Register;

use Psr\Http\Message\ServerRequestInterface;
use Stu\Component\ErrorHandling\ErrorCodeEnum;
use Stu\Component\Player\Register\PlayerCreatorInterface;
use Stu\Component\Player\Register\Exception\RegistrationException;
use Stu\Module\Api\Middleware\Action;
use Stu\Module\Api\Middleware\Request\JsonSchemaRequestInterface;
use Stu\Module\Api\Middleware\Response\JsonResponseInterface;
use Stu\Orm\Entity\FactionInterface;
use Stu\Orm\Repository\FactionRepositoryInterface;

final class Register extends Action
{
    private $jsonSchemaRequest;

    private $createPlayer;

    private $factionRepository;

    public function __construct(
        JsonSchemaRequestInterface $jsonSchemaRequest,
        PlayerCreatorInterface $createPlayer,
        FactionRepositoryInterface $factionRepository
    ) {
        $this->jsonSchemaRequest = $jsonSchemaRequest;
        $this->createPlayer = $createPlayer;
        $this->factionRepository = $factionRepository;
    }

    public function action(
        ServerRequestInterface $request,
        JsonResponseInterface $response,
        array $args
    ): JsonResponseInterface {
        $data = $this->jsonSchemaRequest->getData($this);

        $factions = array_filter(
            $this->factionRepository->getByChooseable(true),
            function (FactionInterface $faction) use ($data): bool {
                return $data->factionId === $faction->getId() && $faction->hasFreePlayerSlots();
            }
        );
        if ($factions === []) {
            return $response->withError(
                'BAD REQUEST',
                'No suitable faction transmitted',
                ErrorCodeEnum::INVALID_FACTION
            );
        }

        try {
            $this->createPlayer->create(
                $data->loginName,
                $data->emailAddress,
                current($factions)
            );
        } catch (RegistrationException $e) {
            return $response->withError(
                'BAD REQUEST',
                $e->getMessage(),
                $e->getCode()
            );
        }

        return $response->withData(true);
    }

    public function getJsonSchemaFile(): ?string
    {
        return __DIR__ . '/register.json';
    }
}