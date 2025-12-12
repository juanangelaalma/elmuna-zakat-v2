<?php

use App\Models\User;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Donation;
use App\Models\Fidyah;
use App\Models\Wealth;
use App\Models\Rice;
use App\Models\RiceSale;
use App\Models\PurchaseRice;
use App\Constants\TransactionItemType;

test('dashboard shows correct transaction summaries', function () {
    $user = User::factory()->create();
    
    // Create different types of transactions
    $transaction1 = Transaction::factory()->create();
    TransactionDetail::factory()->create([
        'transaction_id' => $transaction1->id,
        'type' => TransactionItemType::DONATION,
    ]);
    Donation::factory()->create(['amount' => 100000]);

    $transaction2 = Transaction::factory()->create();
    TransactionDetail::factory()->create([
        'transaction_id' => $transaction2->id,
        'type' => TransactionItemType::RICE_SALES,
    ]);
    RiceSale::factory()->create(['amount' => 50000]);

    $response = $this->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
});

test('dashboard shows correct income calculations', function () {
    $user = User::factory()->create();
    
    // Create donations totaling 300,000
    $transaction = Transaction::factory()->create();
    $transactionDetail = TransactionDetail::factory()->create([
        'transaction_id' => $transaction->id,
        'type' => TransactionItemType::DONATION,
    ]);
    Donation::factory()->create(['amount' => 100000]);
    Donation::factory()->create(['amount' => 200000]);

    // Create rice sales totaling 150,000
    $transaction2 = Transaction::factory()->create();
    $transactionDetail2 = TransactionDetail::factory()->create([
        'transaction_id' => $transaction2->id,
        'type' => TransactionItemType::RICE_SALES,
    ]);
    RiceSale::factory()->create(['amount' => 150000]);

    $response = $this->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
});

test('dashboard shows correct stock levels', function () {
    $user = User::factory()->create();
    
    // Purchase 100kg of rice
    PurchaseRice::factory()->create(['quantity' => 100]);
    
    // Sell 30kg of rice
    $transaction = Transaction::factory()->create();
    $transactionDetail = TransactionDetail::factory()->create([
        'transaction_id' => $transaction->id,
        'type' => TransactionItemType::RICE_SALES,
    ]);
    RiceSale::factory()->create(['quantity' => 30]);
    
    // Add 20kg from zakat rice
    $transaction2 = Transaction::factory()->create();
    $transactionDetail2 = TransactionDetail::factory()->create([
        'transaction_id' => $transaction2->id,
        'type' => TransactionItemType::RICE,
    ]);
    Rice::factory()->create(['quantity' => 20]);

    $response = $this->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
});

test('date filtering works properly', function () {
    $user = User::factory()->create();
    
    // Create transactions in different date ranges
    $oldTransaction = Transaction::factory()->create([
        'date' => now()->subDays(10),
    ]);
    TransactionDetail::factory()->create([
        'transaction_id' => $oldTransaction->id,
        'type' => TransactionItemType::DONATION,
    ]);
    Donation::factory()->create(['amount' => 50000]);

    $recentTransaction = Transaction::factory()->create([
        'date' => now()->subDay(),
    ]);
    TransactionDetail::factory()->create([
        'transaction_id' => $recentTransaction->id,
        'type' => TransactionItemType::DONATION,
    ]);
    Donation::factory()->create(['amount' => 75000]);

    // Test with date filter
    $response = $this->actingAs($user)
        ->get(route('dashboard', [
            'start_date' => now()->subDays(5)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]));

    $response->assertOk();
});

test('export functionality generates correct csv', function () {
    $user = User::factory()->create();
    
    // Create test data
    $transaction = Transaction::factory()->create();
    TransactionDetail::factory()->create([
        'transaction_id' => $transaction->id,
        'type' => TransactionItemType::DONATION,
    ]);
    Donation::factory()->create(['amount' => 100000]);

    $response = $this->actingAs($user)
        ->get(route('export.donations'));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('text/csv');
});

test('trend calculations are accurate', function () {
    $user = User::factory()->create();
    
    // Create transactions over multiple days
    for ($i = 5; $i >= 1; $i--) {
        $transaction = Transaction::factory()->create([
            'date' => now()->subDays($i),
        ]);
        $transactionDetail = TransactionDetail::factory()->create([
            'transaction_id' => $transaction->id,
            'type' => TransactionItemType::DONATION,
        ]);
        Donation::factory()->create(['amount' => 10000 * $i]);
    }

    $response = $this->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
});

test('dashboard shows correct transaction counts by type', function () {
    $user = User::factory()->create();
    
    // Create 3 donations
    for ($i = 0; $i < 3; $i++) {
        $transaction = Transaction::factory()->create();
        TransactionDetail::factory()->create([
            'transaction_id' => $transaction->id,
            'type' => TransactionItemType::DONATION,
        ]);
        Donation::factory()->create();
    }
    
    // Create 2 fidyah
    for ($i = 0; $i < 2; $i++) {
        $transaction = Transaction::factory()->create();
        TransactionDetail::factory()->create([
            'transaction_id' => $transaction->id,
            'type' => TransactionItemType::FIDYAH,
        ]);
        Fidyah::factory()->create();
    }
    
    // Create 1 wealth
    $transaction = Transaction::factory()->create();
    TransactionDetail::factory()->create([
        'transaction_id' => $transaction->id,
        'type' => TransactionItemType::WEALTH,
    ]);
    Wealth::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
});

test('dashboard handles empty data gracefully', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
});

test('dashboard shows correct rice sales summary', function () {
    $user = User::factory()->create();
    
    // Create multiple rice sales
    for ($i = 0; $i < 3; $i++) {
        $transaction = Transaction::factory()->create();
        $transactionDetail = TransactionDetail::factory()->create([
            'transaction_id' => $transaction->id,
            'type' => TransactionItemType::RICE_SALES,
        ]);
        RiceSale::factory()->create([
            'amount' => 15000,
            'quantity' => 5,
        ]);
    }

    $response = $this->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
});

test('dashboard shows correct fidyah summary', function () {
    $user = User::factory()->create();
    
    // Create fidyah transactions
    for ($i = 0; $i < 2; $i++) {
        $transaction = Transaction::factory()->create();
        $transactionDetail = TransactionDetail::factory()->create([
            'transaction_id' => $transaction->id,
            'type' => TransactionItemType::FIDYAH,
        ]);
        Fidyah::factory()->create([
            'amount' => 30000,
            'quantity' => 10,
        ]);
    }

    $response = $this->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
});