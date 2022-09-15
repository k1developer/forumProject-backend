<?php


namespace App\Repositories;


use App\Models\Channel;
use App\Repositories\Interfaces\RepositoryInterface;
use App\Repositories\Interfaces\StoreRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChannelRepository implements RepositoryInterface, StoreRepositoryInterface
{

    /**
     * @return Channel[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return Channel::all();
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        Channel::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);
    }

    /**
     * @param Request $request
     */
    public function update($channel, Request $request)
    {
        $channel->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);
    }

    /**
     * @param $id
     */
    public function destroy($channel)
    {
        $channel->delete();
    }
}
