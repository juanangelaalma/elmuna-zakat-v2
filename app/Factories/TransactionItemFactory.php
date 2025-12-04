<?php

namespace App\Factories;

use App\Constants\TransactionItemType;
use App\Contracts\TransactionItemInterface;
use App\Models\TransactionItem\{ RiceSaleTransactionItem, RiceTransactionItem, DonationTransactionItem, FidyahTransactionItem, WealthTransactionItem };

class TransactionItemFactory
{
    public static function create($item): TransactionItemInterface
    {
        return match ($item['item_type']) {
            TransactionItemType::RICE_SALES => app(RiceSaleTransactionItem::class, ['item' => $item]),  // Menggunakan app() untuk dependency injection
            TransactionItemType::RICE => app(RiceTransactionItem::class, ['item' => $item]),
            TransactionItemType::DONATION => app(DonationTransactionItem::class, ['item' => $item]),
            TransactionItemType::FIDYAH => app(FidyahTransactionItem::class, ['item' => $item]),
            TransactionItemType::WEALTH => app(WealthTransactionItem::class, ['item' => $item]),
            default => throw new \InvalidArgumentException("Invalid item type: {$item['item_type']}"),
        };
    }
}