<?php

namespace App\Helpers;

class FidyahHelper
{
    public static function normalizeDetail(array $detail): array
    {
        if (!isset($detail['fidyah_type'])) {
            return $detail;
        }

        $dayCount = $detail['day_count'] ?? 1;

        if ($detail['fidyah_type'] === 'money') {
            $detail['quantity'] = null;
            $detail['amount'] = ($detail['amount'] ?? 0) * $dayCount;
        } elseif ($detail['fidyah_type'] === 'rice') {
            $detail['amount'] = null;
            $detail['quantity'] = ($detail['quantity'] ?? 0) * $dayCount;
        }

        return $detail;
    }

    public static function normalizeItems(array $items): array
    {
        return array_map(function ($item) {
            if (($item['item_type'] ?? '') === 'FIDYAH') {
                $item['detail'] = self::normalizeDetail($item['detail']);
            }
            return $item;
        }, $items);
    }
}
