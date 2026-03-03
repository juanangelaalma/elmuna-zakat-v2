import { TransactionItem } from '@/types';

/**
 * Returns effective amount/quantity for any transaction item detail.
 * For fidyah items, day_count is already multiplied in.
 * This centralizes the day_count multiplication so consumers
 * don't need to check/multiply day_count themselves.
 */
export function getEffectiveValues(detail: TransactionItem['detail']): {
    amount: number;
    quantity: number;
} {
    let amount = 0;
    let quantity = 0;

    if ('amount' in detail && detail.amount !== null) {
        amount = Number(detail.amount);
    }
    if ('quantity' in detail && detail.quantity !== null) {
        quantity = Number(detail.quantity);
    }

    // If fidyah, multiply by day_count
    if ('day_count' in detail && detail.day_count) {
        if (amount) amount *= Number(detail.day_count);
        if (quantity) quantity *= Number(detail.day_count);
    }

    return { amount, quantity };
}
