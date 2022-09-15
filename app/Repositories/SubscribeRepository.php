<?php


namespace App\Repositories;


use App\Models\Subscribe;
use App\Repositories\Interfaces\GetRepositoryInterface;

class SubscribeRepository implements GetRepositoryInterface
{
    /**
     * @param $thread_id
     * @return array
     */
    public function get($thread_id)
    {
        return Subscribe::query()->where('thread_id', $thread_id)->pluck('user_id')->all();
    }
}
