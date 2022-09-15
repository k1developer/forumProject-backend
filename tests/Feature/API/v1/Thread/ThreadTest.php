<?php

namespace Tests\Feature\API\v1\Thread;

use App\Models\Channel;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use RefreshDatabase;


    /**
     * Test All Threads List Should Be Accessible
     */
    public function test_all_threads_list_should_be_accessible()
    {
        $response = $this->get(route('threads.index'));

        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Test All Thread Should Be Accessible BY Slug
     */
    public function test_thread_should_be_accessible_by_slug()
    {
        $thread = Thread::factory()->create();

        $response = $this->get(route('threads.show', [$thread->slug]));

        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Test Create Thread
     */
    public function test_create_thread_should_be_validated()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('threads.store'), []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_can_create_thread()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('threads.store'), [
            'title' => 'Foo',
            'content' => 'Bar',
            'channel_id' => Channel::factory()->create()->id
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    /**
     * Test Update Thread
     */
    public function test_update_thread_should_be_validated()
    {
        Sanctum::actingAs(User::factory()->create());
        $thread = Thread::factory()->create();

        $response = $this->putJson(route('threads.update', [$thread]), []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_can_update_thread()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $thread = Thread::factory()->create([
            'title' => 'Foo',
            'content' => 'Bar',
            'channel_id' => Channel::factory()->create()->id,
            'user_id' => $user->id
        ]);

        $response = $this->putJson(route('threads.update', [$thread]), [
            'title' => 'Bar',
            'content' => 'Bar',
            'channel_id' => Channel::factory()->create()->id
        ])->assertSuccessful();

        $thread->refresh();

        $this->assertSame('Bar', $thread->title);
    }

    public function test_can_add_best_answer_id_for_thread()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $thread = Thread::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->putJson(route('threads.update', [$thread]), [
            'best_answer_id' => 1
        ])->assertSuccessful();

        $thread->refresh();
        $this->assertSame('1', $thread->best_answer_id);
    }

    /**
     * Test Delete Thread
     */
    public function test_can_delete_thread()
    {

        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $thread = Thread::factory()->create([
            'user_id' => $user->id
        ]);
        $response = $this->delete(route('threads.destroy', [$thread]));

        $response->assertStatus(Response::HTTP_OK);

        $this->assertTrue(Thread::where('id', $thread->id)->count() === 0);
    }
}
