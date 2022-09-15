<?php


namespace App\Repositories;


use App\Models\Answer;
use App\Models\Thread;
use App\Repositories\Interfaces\RepositoryInterface;
use App\Repositories\Interfaces\StoreRepositoryInterface;
use Illuminate\Http\Request;

class AnswerRepository implements RepositoryInterface, StoreRepositoryInterface
{

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return Answer::query()->latest()->get();
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        Thread::find($request->thread_id)->answers()->create([
            'content' => $request->input('content'),
            'user_id' => auth()->user()->id
        ]);
    }

    /**
     * @param $answer
     * @param Request $request
     */
    public function update($answer, Request $request)
    {
        $answer->update([
            'content' => $request->input('content')
        ]);
    }

    /**
     * @param $answer
     */
    public function destroy($answer)
    {
        $answer->delete();
    }
}
