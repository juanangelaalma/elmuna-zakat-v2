<?php

use App\Models\User;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Constants\TransactionItemType;

test('guests cannot access transaction creation page', function () {
    $this->get(route('transactions.create'))->assertRedirect(route('login'));
});

test('authenticated users can access transaction creation page', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get(route('transactions.create'))->assertOk();
});

test('can create transaction with single donation item', function () {
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
                'amount' => 100000,
                'quantity' => null,
                'unit_type' => null,
            ]
        ]
    ];

    $response = $this->actingAs($user)
        ->post(route('transactions.store'), $transactionData);

    $response->assertRedirect();
    
    $this->assertDatabaseHas('transactions', [
        'customer' => 'Test Customer',
        'address' => 'Test Address',
        'wa_number' => '08123456789',
        'officer_name' => 'Test Officer',
        'created_by' => $user->id,
    ]);

    $transaction = Transaction::where('customer', 'Test Customer')->first();
    
    $this->assertDatabaseHas('transaction_details', [
        'transaction_id' => $transaction->id,
        'giver_name' => 'Test Giver',
        'type' => TransactionItemType::DONATION,
    ]);

    $transactionDetail = TransactionDetail::where('transaction_id', $transaction->id)->first();
    
    $this->assertDatabaseHas('donations', [
        'transaction_detail_id' => $transactionDetail->id,
        'donation_type' => 'cash',
        'amount' => 100000,
    ]);
});

test('can create transaction with multiple item types', function () {
    $user = User::factory()->create();
    
    $transactionData = [
        'date' => now()->format('Y-m-d'),
        'customer' => 'Test Customer',
        'address' => 'Test Address',
        'wa_number' => '08123456789',
        'officer_name' => 'Test Officer',
        'items' => [
            [
                'giver_name' => 'Giver 1',
                'type' => TransactionItemType::DONATION,
                'donation_type' => 'cash',
                'amount' => 50000,
                'quantity' => null,
                'unit_type' => null,
            ],
            [
                'giver_name' => 'Giver 2',
                'type' => TransactionItemType::FIDYAH,
                'fidyah_type' => 'cash',
                'amount' => 30000,
                'quantity' => null,
                'unit_type' => null,
            ],
            [
                'giver_name' => 'Giver 3',
                'type' => TransactionItemType::WEALTH,
                'amount' => 2000000,
            ]
        ]
    ];

    $response = $this->actingAs($user)
        ->post(route('transactions.store'), $transactionData);

    $response->assertRedirect();
    
    $transaction = Transaction::where('customer', 'Test Customer')->first();
    expect($transaction->transactionDetails)->toHaveCount(3);
    
    $this->assertDatabaseHas('donations', ['amount' => 50000]);
    $this->assertDatabaseHas('fidyahs', ['amount' => 30000]);
    $this->assertDatabaseHas('wealths', ['amount' => 2000000]);
});

test('can create transaction with rice items', function () {
    $user = User::factory()->create();
    
    $transactionData = [
        'date' => now()->format('Y-m-d'),
        'customer' => 'Test Customer',
        'address' => 'Test Address',
        'wa_number' => '08123456789',
        'officer_name' => 'Test Officer',
        'items' => [
            [
                'giver_name' => 'Rice Giver',
                'type' => TransactionItemType::RICE,
                'quantity' => 5,
                'unit_type' => 'kg',
            ],
            [
                'giver_name' => 'Rice Buyer',
                'type' => TransactionItemType::RICE_SALES,
                'amount' => 15000,
                'quantity' => 3,
            ]
        ]
    ];

    $response = $this->actingAs($user)
        ->post(route('transactions.store'), $transactionData);

    $response->assertRedirect();
    
    $transaction = Transaction::where('customer', 'Test Customer')->first();
    
    $this->assertDatabaseHas('rices', [
        'quantity' => 5,
        'unit_type' => 'kg',
    ]);
    
    $this->assertDatabaseHas('rice_sales', [
        'amount' => 15000,
        'quantity' => 3,
    ]);
});

test('transaction number is unique and properly formatted', function () {
    $user = User::factory()->create();
    
    $transactionData = [
        'date' => now()->format('Y-m-d'),
        'customer' => 'Customer 1',
        'address' => 'Address 1',
        'wa_number' => '08123456789',
        'officer_name' => 'Officer 1',
        'items' => [
            [
                'giver_name' => 'Giver 1',
                'type' => TransactionItemType::DONATION,
                'donation_type' => 'cash',
                'amount' => 100000,
            ]
        ]
    ];

    $this->actingAs($user)->post(route('transactions.store'), $transactionData);
    
    $transaction1 = Transaction::where('customer', 'Customer 1')->first();
    expect($transaction1->transaction_number)->toMatch('/^TRX-\d{4}-\d{6}$/');
    
    $transactionData['customer'] = 'Customer 2';
    $this->actingAs($user)->post(route('transactions.store'), $transactionData);
    
    $transaction2 = Transaction::where('customer', 'Customer 2')->first();
    expect($transaction2->transaction_number)->not->toBe($transaction1->transaction_number);
});

test('transaction validation requires required fields', function () {
    $user = User::factory()->create();
    
    $invalidData = [
        'date' => '',
        'customer' => '',
        'address' => '',
        'wa_number' => '',
        'officer_name' => '',
        'items' => [],
    ];

    $response = $this->actingAs($user)
        ->post(route('transactions.store'), $invalidData);

    $response->assertSessionHasErrors(['date', 'customer', 'address', 'wa_number', 'officer_name', 'items']);
});

test('can view transaction details', function () {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->create();
    $transactionDetail = TransactionDetail::factory()->create([
        'transaction_id' => $transaction->id,
    ]);

    $response = $this->actingAs($user)
        ->get(route('transactions.show', $transaction));

    $response->assertOk()
        ->assertSee($transaction->transaction_number)
        ->assertSee($transaction->customer)
        ->assertSee($transactionDetail->giver_name);
});

test('can view transaction index', function () {
    $user = User::factory()->create();
    Transaction::factory()->count(5)->create();

    $response = $this->actingAs($user)
        ->get(route('transactions.index'));

    $response->assertOk();
});

test('can delete transaction', function () {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->create();
    $transactionDetail = TransactionDetail::factory()->create([
        'transaction_id' => $transaction->id,
    ]);

    $response = $this->actingAs($user)
        ->delete(route('transactions.destroy', $transaction));

    $response->assertRedirect();
    
    $this->assertDatabaseMissing('transactions', [
        'id' => $transaction->id,
    ]);
    
    $this->assertDatabaseMissing('transaction_details', [
        'transaction_id' => $transaction->id,
    ]);
});