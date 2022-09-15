<?php

namespace App\Http\Controllers\API\v1\Channel;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Repositories\ChannelRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChannelController extends Controller
{
    protected $channel;

    /**
     * ChannelController constructor.
     */
    public function __construct()
    {
        $this->channel = resolve(ChannelRepository::class);
    }

    /**
     * All Channels
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $allChannels = $this->channel->all();

        return response()->json($allChannels, Response::HTTP_OK);
    }


    /**
     * Create New Channel
     * @method POST
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
           'name' => 'required'
        ]);

        // Insert Channel To Database
        $this->channel->store($request);

        return response()->json([
            'message' => 'channel created successfully'
        ], Response::HTTP_CREATED);
    }

    /**
     * Update Channel
     * @method PUT
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Channel $channel)
    {
        $request->validate([
            'name' => 'required'
        ]);

        // Update Channel In Database
        $this->channel->update($channel, $request);


        return response()->json([
            'message' => 'channel edited successfully'
        ], Response::HTTP_OK);
    }

    /**
     * Delete Channel(s)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Channel $channel)
    {
        // Delete Channel In Database
        $this->channel->destroy($channel);

        return response()->json([
           'message' => 'channel deleted successfully'
        ], Response::HTTP_OK);
    }

}
