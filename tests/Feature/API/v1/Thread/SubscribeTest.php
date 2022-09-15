<?php

namespace Tests\Feature\API\v1\Thread;

use App\Models\Answer;
use App\Models\Channel;
use App\Models\Subscribe;
use App\Models\Thread;
use App\Models\User;
use App\Notifications\NewReplySubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SubscribeTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_subscribe_to_a_thread()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->create();

        $response = $this->post(route('subscribe', [$thread]));

        $response->assertSuccessful();
        $response->assertJson([
            'message' => 'user subscribed successfully'
        ]);

        $this->assertFalse(Subscribe::where([
            ['thread_id', $thread->id],
            ['user_id', $user->id]
        ])->count() === 0);
    }

    public function test_can_unsubscribe_from_a_thread()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $thread = Thread::factory()->create();

        $response = $this->post(route('unSubscribe', [$thread]));

        $response->assertSuccessful();
        $response->assertJson([
            'message' => 'user unsubscribed successfully'
        ]);
    }

    public function test_notification_will_send_to_subscribers_of_a_thread()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        Notification::fake();

        $thread = Thread::factory()->create();

        $subscribeResponse = $this->post(route('subscribe', [$thread]));
        $subscribeResponse->assertSuccessful();
        $subscribeResponse->assertJson([
            'message' => 'user subscribed successfully'
        ]);

        $answerResponse = $this->postJson(route('answers.store'), [
            'content' => 'Foo',
            'thread_id' => $thread->id
        ]);
        $answerResponse->assertSuccessful();
        $answerResponse->assertJson([
            'message' => 'answer submitted successfully'
        ]);


        Notification::assertSentTo($user, NewReplySubmitted::class);
    }
}
