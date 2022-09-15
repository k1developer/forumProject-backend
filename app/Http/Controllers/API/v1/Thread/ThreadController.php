<?php

namespace App\Http\Controllers\API\v1\Thread;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Repositories\ThreadRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ThreadController extends Controller
{

    protected $thread;

    /**
     * ThreadController constructor.
     */
    public function __construct()
    {
        $this->middleware(['user_block'])->except([
            'index',
            'show'
        ]);

        $this->thread = resolve(ThreadRepository::class);
    }

    /**
     * Validate Update Thread
     * @param Request $request
     */
    public function updateValidated(Request $request)
    {
        $request->has('best_answer_id')
            ?
            $request->validate([
                'best_answer_id' => 'required'
            ])
            :
            $request->validate([
                'title' => 'required',
                'content' => 'required',
                'channel_id' => 'required'
            ]);
    }
    /**
     * List Threads
     * @method GET
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $threads = $this->thread->all();

        return response()->json($threads, Response::HTTP_OK);
    }

    /**
     * Show Thread
     * @method GET
     * @param $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        $thread = $this->thread->get($slug);

        return response()->json($thread, Response::HTTP_OK);
    }

    /**
     * Store Thread
     * @method POST
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'channel_id' => 'required'
        ]);

        $this->thread->store($request);

        return \response()->json([
            'message' => 'thread created successfully'
        ], Response::HTTP_CREATED);
    }


    /**
     * Update Thread
     * @method PUT
     * @param Request $request
     * @param Thread $thread
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Thread $thread)
    {
        // Validated Inputs
        $this->updateValidated($request);

        if (Gate::forUser(auth()->user())->allows('user-thread', $thread)) {
            $this->thread->update($thread, $request);

            return \response()->json([
                'message' => 'thread updated successfully'
            ], Response::HTTP_OK);
        }

        return \response()->json([
            'message' => 'access denied'
        ], Response::HTTP_FORBIDDEN);
    }

    /**
     * Delete Thread
     * method DELETE
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Thread $thread)
    {
        if (Gate::forUser(auth()->user())->allows('user-thread', $thread)) {
            $this->thread->destroy($thread);

            return \response()->json([
                'message' => 'thread deleted successfully'
            ], Response::HTTP_OK);
        }

        return \response()->json([
            'message' => 'access denied'
        ], Response::HTTP_FORBIDDEN);

    }
}
