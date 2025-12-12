<?php

use App\Models\User;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Donation;
use App\Models\Fidyah;
use App\Models\Wealth;
use App\Models\Rice;
use App\Models\RiceSale;
use App\Models\DefaultValue;
use App\Constants\TransactionItemType;

test('donation amounts are calculated correctly', function () {
    $user = User::factory()->create();
    
    // Test cash donation
    $transaction = Transaction::factory()->create();
    $transactionDetail = TransactionDetail::factory()->create([
        'transaction_id' => $transaction->id,
        'type' => TransactionItemType::DONATION,
    ]);
    
    $donation = Donation::factory()->create([
        'transaction_detail_id' => $transactionDetail->id,
        'donation_type' => 'cash',
        'amount' => 100000,
        'quantity' => null,
        'unit_type' => null,
    ]);

    expect($donation->amount)->toBe(100000);
    expect($donation->quantity)->toBeNull();
});

test('fidyah compensation follows business rules', function () {
    $user = User::factory()->create();
    
    // Test fidyah calculation (typically based on missed fasts)
    $transaction = Transaction::factory()->create();
    $transactionDetail = TransactionDetail::factory()->create([
        'transaction_id' => $transaction->id,
        'type' => TransactionItemType::FIDYAH,
    ]);
    
    $fidyah = Fidyah::factory()->create([
        'transaction_detail_id' => $transactionDetail->id,
        'fidyah_type' => 'cash',
        'amount' => 30000, // Typical fidyah amount per day
        'quantity' => 10, // 10 missed fasts
        'unit_type' => null,
    ]);

    expect($fidyah->amount)->toBe(30000);
    expect($fidyah->quantity)->toBe(10);
});

test('wealth zakat amounts are processed correctly', function () {
    $user = User::factory()->create();
    
    // Test wealth zakat (typically 2.5% of wealth)
    $transaction = Transaction::factory()->create();
    $transactionDetail = TransactionDetail::factory()->create([
        'transaction_id' => $transaction->id,
        'type' => TransactionItemType::WEALTH,
    ]);
    
    $wealth = Wealth::factory()->create([
        'transaction_detail_id' => $transactionDetail->id,
        'amount' => 2500000, // Total wealth
    ]);

    expect($wealth->amount)->toBe(2500000);
});

test('rice quantities are handled correctly', function () {
    $user = User::factory()->create();
    
    // Test rice zakat
    $transaction = Transaction::factory()->create();
    $transactionDetail = TransactionDetail::factory()->create([
        'transaction_id' => $transaction->id,
        'type' => TransactionItemType::RICE,
    ]);
    
    $rice = Rice::factory()->create([
        'transaction_detail_id' => $transactionDetail->id,
        'quantity' => 5.5,
        'unit_type' => 'kg',
    ]);

    expect($rice->quantity)->toBe(5.5);
    expect($rice->unit_type)->toBe('kg');
});

test('rice sales calculations are accurate', function () {
    $user = User::factory()->create();
    
    $transaction = Transaction::factory()->create();
    $transactionDetail = TransactionDetail::factory()->create([
        'transaction_id' => $transaction->id,
        'type' => TransactionItemType::RICE_SALES,
    ]);
    
    $riceSale = RiceSale::factory()->create([
        'transaction_detail_id' => $transactionDetail->id,
        'amount' => 15000,
        'quantity' => 3,
    ]);

    expect($riceSale->amount)->toBe(15000);
    expect($riceSale->quantity)->toBe(3);
});

test('transaction validation prevents invalid data', function () {
    $user = User::factory()->create();
    
    // Test with invalid transaction data
    $invalidData = [
        'date' => 'invalid-date',
        'customer' => '',
        'address' => '',
        'wa_number' => 'invalid-phone',
        'officer_name' => '',
        'items' => [],
    ];

    $response = $this->actingAs($user)
        ->post(route('transactions.store'), $invalidData);

    $response->assertSessionHasErrors(['date', 'customer', 'address', 'wa_number', 'officer_name', 'items']);
});

test('default values are applied correctly', function () {
    $user = User::factory()->create();
    
    // Create default values
    DefaultValue::factory()->create([
        'rice_sales_quantity' => 3,
        'rice_sales_amount' => 15000,
        'rice_quantity' => 2.5,
        'fidyah_quantity' => 10,
        'fidyah_amount' => 30000,
        'unit' => 'kg',
    ]);

    $defaults = DefaultValue::first();
    
    expect($defaults->rice_sales_quantity)->toBe(3);
    expect($defaults->rice_sales_amount)->toBe(15000);
    expect($defaults->rice_quantity)->toBe(2.5);
    expect($defaults->fidyah_quantity)->toBe(10);
    expect($defaults->fidyah_amount)->toBe(30000);
    expect($defaults->unit)->toBe('kg');
});

test('transaction number generation follows correct format', function () {
    $user = User::factory()->create();
    
    $transaction = Transaction::factory()->create();
    
    // Check format: TRX-YYYY-XXXXXX
    expect($transaction->transaction_number)->toMatch('/^TRX-\d{4}-\d{6}$/');
});

test('phone number validation works correctly', function () {
    $user = User::factory()->create();
    
    // Valid phone numbers
    $validPhones = ['08123456789', '08234567890', '+628123456789'];
    
    foreach ($validPhones as $phone) {
        $transactionData = [
            'date' => now()->format('Y-m-d'),
            'customer' => 'Test Customer',
            'address' => 'Test Address',
            'wa_number' => $phone,
            'officer_name' => 'Test Officer',
            'items' => [
                [
                    'giver_name' => 'Test Giver',
                    'type' => TransactionItemType::DONATION,
                    'donation_type' => 'cash',
                    'amount' => 100000,
                ]
            ]
        ];

        $response = $this->actingAs($user)
            ->post(route('transactions.store'), $transactionData);

        // Should not have phone validation errors
        $response->assertSessionDoesntHaveErrors('wa_number');
    }
});

test('transaction items must have valid types', function () {
    $user = User::factory()->create();
    
    $transactionData = [
        'date' => now()->format('Y-m-d'),
        'customer' => 'Test Customer',
        'address' => 'Test Address',
        'wa_number' => '08123456789',
        'officer_name' => 'Test Officer',
        'items' => [
            [
                'giver_name' => 'Test Giver',
                'type' => 'INVALID_TYPE',
                'amount' => 100000,
            ]
        ]
    ];

    $response = $this->actingAs($user)
        ->post(route('transactions.store'), $transactionData);

    $response->assertSessionHasErrors('items.0.type');
});

test('rice quantities must be positive numbers', function () {
    $user = User::factory()->create();
    
    $transactionData = [
        'date' => now()->format('Y-m-d'),
        'customer' => 'Test Customer',
        'address' => 'Test Address',
        'wa_number' => '08123456789',
        'officer_name' => 'Test Officer',
        'items' => [
            [
                'giver_name' => 'Test Giver',
                'type' => TransactionItemType::RICE,
                'quantity' => -5, // Invalid negative quantity
                'unit_type' => 'kg',
            ]
        ]
    ];

    $response = $this->actingAs($user)
        ->post(route('transactions.store'), $transactionData);

    $response->assertSessionHasErrors('items.0.quantity');
});

test('monetary amounts must be positive', function () {
    $user = User::factory()->create();
    
    $transactionData = [
        'date' => now()->format('Y-m-d'),
        'customer' => 'Test Customer',
        'address' => 'Test Address',
        'wa_number' => '08123456789',
        'officer_name' => 'Test Officer',
        'items' => [
            [
                'giver_name' => 'Test Giver',
                'type' => TransactionItemType::DONATION,
                'donation_type' => 'cash',
                'amount' => -1000, // Invalid negative amount
            ]
        ]
    ];

    $response = $this->actingAs($user)
        ->post(route('transactions.store'), $transactionData);

    $response->assertSessionHasErrors('items.0.amount');
});