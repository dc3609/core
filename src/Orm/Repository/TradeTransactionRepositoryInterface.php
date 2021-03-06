<?php

namespace Stu\Orm\Repository;

use Doctrine\Persistence\ObjectRepository;
use Stu\Orm\Entity\TradeTransactionInterface;

/**
 * @method null|TradeTransactionInterface find(integer $id)
 */
interface TradeTransactionRepositoryInterface extends ObjectRepository
{
    public function prototype(): TradeTransactionInterface;

    public function save(TradeTransactionInterface $tradeTransaction): void;

    public function getLatestTransactions(int $offered, int $wanted): array;
}
