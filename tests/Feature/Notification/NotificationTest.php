<?php

namespace Tests\Feature\Notification;

use App\Models\Business;
use App\Models\Plan;
use App\Models\User;
use App\Notifications\BusinessDeleted;
use App\Notifications\PaymentFailed;
use App\Notifications\SubscriptionActivated;
use App\Notifications\SubscriptionCancelled;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_activated_notification_is_sent(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $plan = Plan::factory()->create(['name' => 'Pro', 'max_users_per_business' => 10, 'max_projects' => 50]);
        $business = Business::factory()->create(['plan_id' => $plan->id]);
        $business->users()->attach($user, ['role' => 'owner']);

        $user->notify(new SubscriptionActivated($business, $plan));

        Notification::assertSentTo($user, SubscriptionActivated::class, function ($notification) use ($business, $plan) {
            return $notification->business->id === $business->id
                && $notification->plan->id === $plan->id;
        });
    }

    public function test_subscription_activated_notification_contains_correct_data(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create(['name' => 'Pro', 'max_users_per_business' => 10, 'max_projects' => 50]);
        $business = Business::factory()->create(['name' => 'Test Company', 'plan_id' => $plan->id]);
        $business->users()->attach($user, ['role' => 'owner']);

        $notification = new SubscriptionActivated($business, $plan);
        $mailData = $notification->toMail($user);

        $this->assertStringContainsString('Subscription Activated', $mailData->subject);
        $this->assertStringContainsString('Test Company', $mailData->subject);

        $arrayData = $notification->toArray($user);
        $this->assertEquals($business->id, $arrayData['business_id']);
        $this->assertEquals($plan->id, $arrayData['plan_id']);
    }

    public function test_subscription_cancelled_notification_is_sent(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        $user->notify(new SubscriptionCancelled($business, 'Pro'));

        Notification::assertSentTo($user, SubscriptionCancelled::class, function ($notification) use ($business) {
            return $notification->business->id === $business->id
                && $notification->planName === 'Pro';
        });
    }

    public function test_subscription_cancelled_notification_contains_correct_data(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create(['name' => 'Cancelled Company']);

        $notification = new SubscriptionCancelled($business, 'Enterprise');
        $mailData = $notification->toMail($user);

        $this->assertStringContainsString('Cancelled Company', $mailData->subject);

        $arrayData = $notification->toArray($user);
        $this->assertEquals($business->id, $arrayData['business_id']);
        $this->assertEquals('Enterprise', $arrayData['previous_plan']);
    }

    public function test_payment_failed_notification_is_sent(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $business = Business::factory()->create();
        $business->users()->attach($user, ['role' => 'owner']);

        $user->notify(new PaymentFailed($business, 2999));

        Notification::assertSentTo($user, PaymentFailed::class, function ($notification) use ($business) {
            return $notification->business->id === $business->id
                && $notification->amountCents === 2999;
        });
    }

    public function test_payment_failed_notification_contains_correct_data(): void
    {
        $user = User::factory()->create();
        $business = Business::factory()->create(['name' => 'Payment Issue Co']);

        $notification = new PaymentFailed($business, 4999);
        $mailData = $notification->toMail($user);

        $this->assertStringContainsString('Payment Issue Co', $mailData->subject);

        $arrayData = $notification->toArray($user);
        $this->assertEquals($business->id, $arrayData['business_id']);
        $this->assertEquals(4999, $arrayData['amount_cents']);
    }

    public function test_business_deleted_notification_is_sent(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $user->notify(new BusinessDeleted('Old Company', 'John Doe'));

        Notification::assertSentTo($user, BusinessDeleted::class, function ($notification) {
            return $notification->businessName === 'Old Company'
                && $notification->deletedByName === 'John Doe';
        });
    }

    public function test_business_deleted_notification_contains_correct_data(): void
    {
        $user = User::factory()->create();

        $notification = new BusinessDeleted('Deleted Corp', 'Jane Smith');
        $mailData = $notification->toMail($user);

        $this->assertStringContainsString('Deleted Corp', $mailData->subject);

        $arrayData = $notification->toArray($user);
        $this->assertEquals('Deleted Corp', $arrayData['business_name']);
        $this->assertEquals('Jane Smith', $arrayData['deleted_by']);
    }
}
