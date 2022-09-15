<?php

namespace App\Http\Controllers\API\v1\Thread;

use App\Http\Controllers\Controller;
use App\Models\Subscribe;
use App\Models\Thread;
use Symfony\Component\HttpFoundation\Response;

class SubscribeController extends Controller
{
    /**
     * SubscribeController constructor.
     */
    public function __construct()
    {
        $this->middleware(['user_block']);
    }

    /**
     * Subscribe User In Thread
     * @param Thread $thread
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Thread $thread)
    {
        auth()->user()->subscribes()->create([
            'thread_id' => $thread->id
        ]);

        return response()->json([
            'message' => 'user subscribed successfully'
        ], Response::HTTP_OK);
    }

    /**
     * unSubscribe User In Thread
     * @param Thread $thread
     * @return \Illuminate\Http\JsonResponse
     */
    public function unSubscribe(Thread $thread)
    {
        Subscribe::query()->where([
           ['thread_id', $thread->id],
           ['user_id', auth()->user()->id]
        ])->delete();

        return response()->json([
            'message' => 'user unsubscribed successfully'
        ], Response::HTTP_OK);
    }
}
