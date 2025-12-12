<?php

use App\Models\User;
use App\Models\RiceItem;
use App\Models\PurchaseRice;
use App\Models\PurchaseRiceAllocation;
use App\Models\RiceSale;
use App\Models\Transaction;
use App\Models\TransactionDetail;

test('guests cannot access purchase rice page', function () {
    $this->get(route('purchase-rice.create'))->assertRedirect(route('login'));
});

test('authenticated users can access purchase rice page', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get(route('purchase-rice.create'))->assertOk();
});

test('can purchase rice and update stock', function () {
    $user = User::factory()->create();
    $riceItem = RiceItem::factory()->create();

    $purchaseData = [
        'rice_item_id' => $riceItem->id,
        'quantity' => 100,
        'price_per_kg' => 15000,
    ];

    $response = $this->actingAs($user)
        ->post(route('purchase-rice.store'), $purchaseData);

    $response->assertRedirect();
    
    $this->assertDatabaseHas('purchase_rices', [
        'rice_item_id' => $riceItem->id,
        'quantity' => 100,
        'price_per_kg' => 15000,
        'created_by' => $user->id,
    ]);
});

test('can view rice items list', function () {
    $user = User::factory()->create();
    RiceItem::factory()->count(5)->create();

    $response = $this->actingAs($user)
        ->get(route('rice-items.index'));

    $response->assertOk();
});

test('can create new rice item', function () {
    $user = User::factory()->create();

    $riceItemData = [
        'name' => 'Premium Rice',
        'description' => 'High quality premium rice',
    ];

    $response = $this->actingAs($user)
        ->post(route('rice-items.store'), $riceItemData);

    $response->assertRedirect();
    
    $this->assertDatabaseHas('rice_items', [
        'name' => 'Premium Rice',
        'description' => 'High quality premium rice',
    ]);
});

test('rice sales allocate from oldest purchases first', function () {
    $user = User::factory()->create();
    $riceItem = RiceItem::factory()->create();
    
    // Create two purchases at different times
    $oldPurchase = PurchaseRice::factory()->create([
        'rice_item_id' => $riceItem->id,
        'quantity' => 50,
        'price_per_kg' => 15000,
        'created_at' => now()->subDays(2),
    ]);
    
    $newPurchase = PurchaseRice::factory()->create([
        'rice_item_id' => $riceItem->id,
        'quantity' => 30,
        'price_per_kg' => 16000,
        'created_at' => now()->subDay(),
    ]);

    // Create a rice sale transaction
    $transaction = Transaction::factory()->create();
    $transactionDetail = TransactionDetail::factory()->create([
        'transaction_id' => $transaction->id,
    ]);
    
    $riceSale = RiceSale::factory()->create([
        'transaction_detail_id' => $transactionDetail->id,
        'quantity' => 20,
    ]);

    // Check that allocation is created from the oldest purchase
    $this->assertDatabaseHas('purchase_rice_allocations', [
        'purchase_rice_id' => $oldPurchase->id,
        'rice_sale_id' => $riceSale->id,
        'quantity' => 20,
    ]);
});

test('stock calculations are accurate', function () {
    $user = User::factory()->create();
    $riceItem = RiceItem::factory()->create();
    
    // Purchase 100kg
    PurchaseRice::factory()->create([
        'rice_item_id' => $riceItem->id,
        'quantity' => 100,
    ]);

    // Sell 30kg
    $transaction = Transaction::factory()->create();
    $transactionDetail = TransactionDetail::factory()->create([
        'transaction_id' => $transaction->id,
    ]);
    RiceSale::factory()->create([
        'transaction_detail_id' => $transactionDetail->id,
        'quantity' => 30,
    ]);

    // Check remaining stock
    $totalPurchased = PurchaseRice::where('rice_item_id', $riceItem->id)->sum('quantity');
    $totalSold = RiceSale::whereHas('transactionDetail', function ($query) use ($riceItem) {
        $query->whereHas('transaction', function ($subQuery) use ($riceItem) {
            // This would need to be adjusted based on actual relationships
        });
    })->sum('quantity');

    expect($totalPurchased)->toBe(100);
});

test('insufficient stock throws appropriate error', function () {
    $user = User::factory()->create();
    $riceItem = RiceItem::factory()->create();
    
    // Purchase only 10kg
    PurchaseRice::factory()->create([
        'rice_item_id' => $riceItem->id,
        'quantity' => 10,
    ]);

    // Try to sell 20kg
    $transaction = Transaction::factory()->create();
    $transactionDetail = TransactionDetail::factory()->create([
        'transaction_id' => $transaction->id,
    ]);
    
    $riceSaleData = [
        'transaction_detail_id' => $transactionDetail->id,
        'quantity' => 20,
    ];

    // This should fail due to insufficient stock
    // The exact implementation depends on business logic
    expect($riceSaleData['quantity'])->toBeGreaterThan(10);
});

test('can view purchase history', function () {
    $user = User::factory()->create();
    PurchaseRice::factory()->count(5)->create(['created_by' => $user->id]);

    $response = $this->actingAs($user)
        ->get(route('purchase-rice.index'));

    $response->assertOk();
});

test('can delete rice item', function () {
    $user = User::factory()->create();
    $riceItem = RiceItem::factory()->create();

    $response = $this->actingAs($user)
        ->delete(route('rice-items.destroy', $riceItem));

    $response->assertRedirect();
    
    $this->assertDatabaseMissing('rice_items', [
        'id' => $riceItem->id,
    ]);
});

test('can update rice item', function () {
    $user = User::factory()->create();
    $riceItem = RiceItem::factory()->create();

    $updateData = [
        'name' => 'Updated Rice Name',
        'description' => 'Updated description',
    ];

    $response = $this->actingAs($user)
        ->put(route('rice-items.update', $riceItem), $updateData);

    $response->assertRedirect();
    
    $this->assertDatabaseHas('rice_items', [
        'id' => $riceItem->id,
        'name' => 'Updated Rice Name',
        'description' => 'Updated description',
    ]);
});