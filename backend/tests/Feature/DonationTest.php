<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\Expense;
use App\Models\Helper;
use App\Models\Payment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DonationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Case 1: Create Donation (Success)
     */
    public function test_can_create_donation_successfully(): void
    {
        $response = $this->postJson('/api/v1/donations', [
            'amount' => 100,
            'donor_name' => 'John Doe',
            'is_monthly' => false,
            'status' => 'completed',
            'payment_method' => 'card'
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('donation.amount', 100)
            ->assertJsonPath('donation.donor_name', 'John Doe');

        $this->assertDatabaseHas('donations', [
            'amount' => 100,
            'donor_name' => 'John Doe'
        ]);
    }

    /**
     * Test Case 2: Donation Validation
     */
    public function test_donation_requires_valid_amount(): void
    {
        $response = $this->postJson('/api/v1/donations', [
            'amount' => 'invalid',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }

    /**
     * Test Case 3: List Financial Transparency
     */
    public function test_can_view_financial_transparency_data(): void
    {
        // Seed data
        Donation::factory()->create(['amount' => 1000, 'created_at' => now()]);
        Expense::factory()->create(['amount' => 200, 'date' => now()]);

        $response = $this->getJson('/api/v1/financial-transparency');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_raised',
                'funds_used_percentage',
                'remaining_funds',
                'chart_data' => ['raised', 'expenses'],
                'recent_expenses'
            ]);
    }

    /**
     * Test Case 4: Create Payment (User initiates payment for Task)
     */
    public function test_user_can_create_payment_for_task(): void
    {
        $user = User::factory()->create();
        $helper = Helper::factory()->create();
        $task = Task::factory()->create(['created_by' => $user->id, 'caregiver_id' => $helper->id]);

        $response = $this->actingAs($user)
            ->postJson('/api/v1/payments', [
                'task_id' => $task->id,
                'payee_id' => $helper->id,
                'amount' => 50,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('amount', '50.00')
            ->assertJsonPath('status', 'paid');
    }

    /**
     * Test Case 5: List User Payments (Payer)
     */
    public function test_user_can_list_their_payments(): void
    {
        $user = User::factory()->create();
        Payment::factory()->count(3)->create(['payer_id' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/v1/payments');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /**
     * Test Case 6: List Helper Payments (Payee)
     */
    public function test_helper_can_list_received_payments(): void
    {
        $helper = Helper::factory()->create();
        Payment::factory()->count(2)->create(['payee_id' => $helper->id]);

        $response = $this->actingAs($helper)
            ->getJson('/api/v1/payments');

        $response->assertStatus(200)
            ->assertJsonCount(2);
    }

    /**
     * Test Case 7: Payer Can Update Payment
     */
    public function test_payer_can_update_payment_status(): void
    {
        $user = User::factory()->create();
        $payment = Payment::factory()->create(['payer_id' => $user->id, 'status' => 'pending']);

        $response = $this->actingAs($user)
            ->putJson("/api/v1/payments/{$payment->id}", [
                'status' => 'paid'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'paid');
            
        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'status' => 'paid']);
    }

    /**
     * Test Case 8: Unauthorized Access to Payments
     */
    public function test_user_cannot_update_others_payment(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $payment = Payment::factory()->create(['payer_id' => $user1->id]);

        $response = $this->actingAs($user2)
            ->putJson("/api/v1/payments/{$payment->id}", [
                'status' => 'paid'
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test Case 9: Payment Summary
     */
    public function test_user_can_view_payment_summary(): void
    {
        $user = User::factory()->create();
        Payment::factory()->count(2)->create(['payer_id' => $user->id, 'status' => 'paid', 'amount' => 100]);

        $response = $this->actingAs($user)
            ->getJson('/api/v1/payments/summary');

        $response->assertStatus(200)
            ->assertJsonPath('total_spent', 200)
            ->assertJsonStructure(['total_spent', 'platform_fees', 'to_helpers']);
    }

    /**
     * Test Case 10: Payment Insights
     */
    public function test_user_can_view_payment_insights(): void
    {
        $user = User::factory()->create();
        Payment::factory()->create(['payer_id' => $user->id, 'status' => 'paid', 'paid_at' => now()]);

        $response = $this->actingAs($user)
            ->getJson('/api/v1/payments/insights');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'monthly',
                'helpers',
                'tasks',
                'spending_summary'
            ]);
    }
}
