<?php

namespace App\Http\Controllers\API\v1\Thread;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Thread;
use App\Notifications\NewReplySubmitted;
use App\Repositories\AnswerRepository;
use App\Repositories\SubscribeRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;

class AnswerController extends Controller
{
    protected $answer;

    /**
     * AnswerController constructor.
     */
    public function __construct()
    {
        $this->middleware(['user_block'])->except([
            'index'
        ]);

        $this->answer = resolve(AnswerRepository::class);
    }

    /**
     * All Answer
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $answers = $this->answer->all();

        return response()->json($answers, Response::HTTP_OK);
    }

    /**
     * Create Answer
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
           'content' => 'required',
           'thread_id' => 'required',
        ]);

        //Insert Data Into DB
        $this->answer->store($request);

        // Get List Of User Id Which Subscribe To A Thread Id
        $notifiableUsersId = resolve(SubscribeRepository::class)->get($request->thread_id);
        // Get User Instance From Id
        $notifiableUsers = resolve(UserRepository::class)->get($notifiableUsersId);
        // Send NewReplySubmitted Notification To Subscribed Users
        Notification::send($notifiableUsers, new NewReplySubmitted(Thread::find($request->thread_id)));

        // Increase User Score
        if (Thread::find($request->thread_id)->user_id != auth()->user()->id) {
            auth()->user()->increment('score', 10);
        }

        return \response()->json([
            'message' => 'answer submitted successfully'
        ], Response::HTTP_CREATED);
    }

    /**
     * Update Answer
     * @param Request $request
     * @param Answer $answer
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Answer $answer)
    {
        $request->validate([
           'content' => 'required'
        ]);
        if (Gate::forUser(auth()->user())->allows('user-answer', $answer)) {
            $this->answer->update($answer, $request);

            return \response()->json([
                'message' => 'answer updated successfully'
            ], Response::HTTP_OK);
        }

        return \response()->json([
            'message' => 'access denied'
        ], Response::HTTP_FORBIDDEN);
    }

    /**
     * Delete Answer
     * @param Answer $answer
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Answer $answer)
    {
        if (Gate::forUser(auth()->user())->allows('user-answer', $answer)) {
            $this->answer->destroy($answer);

            return \response()->json([
                'message' => 'answer deleted successfully'
            ], Response::HTTP_OK);
        }

        return \response()->json([
            'message' => 'access denied'
        ], Response::HTTP_FORBIDDEN);
    }
}
