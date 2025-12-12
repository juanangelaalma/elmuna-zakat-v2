<?php

use App\Models\User;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Donation;
use App\Models\Fidyah;
use App\Models\Wealth;
use App\Models\Rice;
use App\Models\RiceSale;
use App\Models\RiceItem;
use App\Models\PurchaseRice;
use App\Models\PurchaseRiceAllocation;
use App\Constants\TransactionItemType;

test('complete transaction workflow from creation to receipt', function () {
    $user = User::factory()->create();
    
    // Create a comprehensive transaction with multiple item types
    $transactionData = [
        'date' => now()->format('Y-m-d'),
        'customer' => 'John Doe',
        'address' => '123 Main St',
        'wa_number' => '08123456789',
        'officer_name' => 'Jane Smith',
        'items' => [
            [
                'giver_name' => 'Donor 1',
                'type' => TransactionItemType::DONATION,
                'donation_type' => 'cash',
                'amount' => 100000,
            ],
            [
                'giver_name' => 'Fidyah Giver',
                'type' => TransactionItemType::FIDYAH,
                'fidyah_type' => 'cash',
                'amount' => 30000,
                'quantity' => 10,
            ],
            [
                'giver_name' => 'Wealth Giver',
                'type' => TransactionItemType::WEALTH,
                'amount' => 2500000,
            ]
        ]
    ];

    // Create the transaction
    $response = $this->actingAs($user)
        ->post(route('transactions.store'), $transactionData);

    $response->assertRedirect();
    
    // Verify transaction was created
    $transaction = Transaction::where('customer', 'John Doe')->first();
    expect($transaction)->not->toBeNull();
    expect($transaction->transactionDetails)->toHaveCount(3);
    
    // Verify all items were created correctly
    $this->assertDatabaseHas('donations', ['amount' => 100000]);
    $this->assertDatabaseHas('fidyahs', ['amount' => 30000, 'quantity' => 10]);
    $this->assertDatabaseHas('wealths', ['amount' => 2500000]);
    
    // Test viewing the transaction
    $showResponse = $this->actingAs($user)
        ->get(route('transactions.show', $transaction));
    
    $showResponse->assertOk()
        ->assertSee($transaction->transaction_number)
        ->assertSee('John Doe')
        ->assertSee('Donor 1')
        ->assertSee('Fidyah Giver')
        ->assertSee('Wealth Giver');
});

test('rice purchase to sale allocation workflow', function () {
    $user = User::factory()->create();
    $riceItem = RiceItem::factory()->create(['name' => 'Premium Rice']);
    
    // Step 1: Purchase rice
    $purchaseData = [
        'rice_item_id' => $riceItem->id,
        'quantity' => 100,
        'price_per_kg' => 15000,
    ];

    $purchaseResponse = $this->actingAs($user)
        ->post(route('purchase-rice.store'), $purchaseData);

    $purchaseResponse->assertRedirect();
    
    $purchase = PurchaseRice::where('rice_item_id', $riceItem->id)->first();
    expect($purchase->quantity)->toBe(100);
    
    // Step 2: Create a rice sale transaction
    $transactionData = [
        'date' => now()->format('Y-m-d'),
        'customer' => 'Rice Customer',
        'address' => '456 Oak Ave',
        'wa_number' => '08234567890',
        'officer_name' => 'Rice Officer',
        'items' => [
            [
                'giver_name' => 'Rice Buyer',
                'type' => TransactionItemType::RICE_SALES,
                'amount' => 45000,
                'quantity' => 3,
            ]
        ]
    ];

    $transactionResponse = $this->actingAs($user)
        ->post(route('transactions.store'), $transactionData);

    $transactionResponse->assertRedirect();
    
    // Step 3: Verify allocation was created
    $transaction = Transaction::where('customer', 'Rice Customer')->first();
    $transactionDetail = TransactionDetail::where('transaction_id', $transaction->id)->first();
    $riceSale = RiceSale::where('transaction_detail_id', $transactionDetail->id)->first();
    
    $this->assertDatabaseHas('purchase_rice_allocations', [
        'purchase_rice_id' => $purchase->id,
        'rice_sale_id' => $riceSale->id,
        'quantity' => 3,
    ]);
    
    // Step 4: Verify stock is updated
    $remainingStock = $purchase->quantity - 3;
    expect($remainingStock)->toBe(97);
});

test('multi-user transaction handling', function () {
    $user1 = User::factory()->create(['name' => 'User 1']);
    $user2 = User::factory()->create(['name' => 'User 2']);
    
    // User 1 creates a transaction
    $transaction1Data = [
        'date' => now()->format('Y-m-d'),
        'customer' => 'Customer 1',
        'address' => 'Address 1',
        'wa_number' => '08111111111',
        'officer_name' => 'Officer 1',
        'items' => [
            [
                'giver_name' => 'Giver 1',
                'type' => TransactionItemType::DONATION,
                'donation_type' => 'cash',
                'amount' => 50000,
            ]
        ]
    ];

    $this->actingAs($user1)
        ->post(route('transactions.store'), $transaction1Data);
    
    // User 2 creates a transaction
    $transaction2Data = [
        'date' => now()->format('Y-m-d'),
        'customer' => 'Customer 2',
        'address' => 'Address 2',
        'wa_number' => '08222222222',
        'officer_name' => 'Officer 2',
        'items' => [
            [
                'giver_name' => 'Giver 2',
                'type' => TransactionItemType::DONATION,
                'donation_type' => 'cash',
                'amount' => 75000,
            ]
        ]
    ];

    $this->actingAs($user2)
        ->post(route('transactions.store'), $transaction2Data);
    
    // Verify both transactions exist with correct creators
    $transaction1 = Transaction::where('customer', 'Customer 1')->first();
    $transaction2 = Transaction::where('customer', 'Customer 2')->first();
    
    expect($transaction1->created_by)->toBe($user1->id);
    expect($transaction2->created_by)->toBe($user2->id);
    expect($transaction1->transaction_number)->not->toBe($transaction2->transaction_number);
});

test('complex zakat calculation workflow', function () {
    $user = User::factory()->create();
    
    // Create a transaction with all zakat types
    $transactionData = [
        'date' => now()->format('Y-m-d'),
        'customer' => 'Zakat Payer',
        'address' => '789 Islamic Center',
        'wa_number' => '08333333333',
        'officer_name' => 'Zakat Officer',
        'items' => [
            [
                'giver_name' => 'Wealth Zakat Giver',
                'type' => TransactionItemType::WEALTH,
                'amount' => 10000000, // 10 million wealth
            ],
            [
                'giver_name' => 'Rice Zakat Giver',
                'type' => TransactionItemType::RICE,
                'quantity' => 10,
                'unit_type' => 'kg',
            ],
            [
                'giver_name' => 'Fidyah Giver',
                'type' => TransactionItemType::FIDYAH,
                'fidyah_type' => 'rice',
                'quantity' => 30,
                'unit_type' => 'kg',
            ]
        ]
    ];

    $response = $this->actingAs($user)
        ->post(route('transactions.store'), $transactionData);

    $response->assertRedirect();
    
    $transaction = Transaction::where('customer', 'Zakat Payer')->first();
    expect($transaction->transactionDetails)->toHaveCount(3);
    
    // Verify all zakat types were processed
    $this->assertDatabaseHas('wealths', ['amount' => 10000000]);
    $this->assertDatabaseHas('rices', ['quantity' => 10, 'unit_type' => 'kg']);
    $this->assertDatabaseHas('fidyahs', ['quantity' => 30, 'unit_type' => 'kg']);
});

test('transaction deletion cascade workflow', function () {
    $user = User::factory()->create();
    
    // Create a transaction with multiple items
    $transaction = Transaction::factory()->create();
    $transactionDetail1 = TransactionDetail::factory()->create([
        'transaction_id' => $transaction->id,
        'type' => TransactionItemType::DONATION,
    ]);
    $transactionDetail2 = TransactionDetail::factory()->create([
        'transaction_id' => $transaction->id,
        'type' => TransactionItemType::RICE_SALES,
    ]);
    
    $donation = Donation::factory()->create(['transaction_detail_id' => $transactionDetail1->id]);
    $riceSale = RiceSale::factory()->create(['transaction_detail_id' => $transactionDetail2->id]);
    
    // Verify all records exist
    $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
    $this->assertDatabaseHas('transaction_details', ['transaction_id' => $transaction->id]);
    $this->assertDatabaseHas('donations', ['id' => $donation->id]);
    $this->assertDatabaseHas('rice_sales', ['id' => $riceSale->id]);
    
    // Delete the transaction
    $response = $this->actingAs($user)
        ->delete(route('transactions.destroy', $transaction));

    $response->assertRedirect();
    
    // Verify cascade deletion worked
    $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    $this->assertDatabaseMissing('transaction_details', ['transaction_id' => $transaction->id]);
    $this->assertDatabaseMissing('donations', ['id' => $donation->id]);
    $this->assertDatabaseMissing('rice_sales', ['id' => $riceSale->id]);
});

test('dashboard data aggregation workflow', function () {
    $user = User::factory()->create();
    
    // Create various transactions
    for ($i = 0; $i < 3; $i++) {
        $transaction = Transaction::factory()->create();
        TransactionDetail::factory()->create([
            'transaction_id' => $transaction->id,
            'type' => TransactionItemType::DONATION,
        ]);
        Donation::factory()->create(['amount' => 50000 * ($i + 1)]);
    }
    
    for ($i = 0; $i < 2; $i++) {
        $transaction = Transaction::factory()->create();
        TransactionDetail::factory()->create([
            'transaction_id' => $transaction->id,
            'type' => TransactionItemType::RICE_SALES,
        ]);
        RiceSale::factory()->create(['amount' => 15000, 'quantity' => 3]);
    }
    
    // Test dashboard aggregation
    $response = $this->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
    
    // Verify totals
    $totalDonations = Donation::sum('amount');
    $totalRiceSales = RiceSale::sum('amount');
    $totalTransactions = Transaction::count();
    
    expect($totalDonations)->toBe(300000); // 50000 + 100000 + 150000
    expect($totalRiceSales)->toBe(30000); // 15000 + 15000
    expect($totalTransactions)->toBe(5);
});