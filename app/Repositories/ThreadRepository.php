<?php


namespace App\Repositories;


use App\Models\Thread;
use App\Repositories\Interfaces\GetRepositoryInterface;
use App\Repositories\Interfaces\RepositoryInterface;
use App\Repositories\Interfaces\StoreRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ThreadRepository implements RepositoryInterface, GetRepositoryInterface, StoreRepositoryInterface
{
    /**
     * @return mixed
     */
    public function all()
    {
        return Thread::whereFlag(1)->with([
            'channel:id,name,slug',
            'user:id,name'
        ])->latest()->paginate(10);
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function get($slug)
    {
        return Thread::whereSlug($slug)->whereFlag(1)->with(['channel', 'user', 'answers', 'answers.user:id,name'])->first();
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        Thread::create([
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')),
            'content' => $request->input('content'),
            'channel_id' => $request->input('channel_id'),
            'user_id' => auth()->user()->id,
        ]);
    }

    /**
     * @param $thread
     * @param Request $request
     */
    public function update($thread, Request $request)
    {
        if (!$request->has('best_answer_id')) {
            $thread->update([
                'title' => $request->input('title'),
                'slug' => Str::slug($request->input('title')),
                'content' => $request->input('content'),
                'channel_id' => $request->input('channel_id'),
            ]);
        } else {
            $thread->update([
                'best_answer_id' => $request->input('best_answer_id')
            ]);
        }
    }

    /**
     * @param $thread
     */
    public function destroy($thread)
    {
        $thread->delete();
    }
}
