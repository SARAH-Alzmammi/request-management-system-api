<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RequestRequest;
use App\Http\Resources\RequestResource;
use App\Models\Request as ModelsRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
     public function index()
     {
        return RequestResource::collection(auth()->user()->requests->sortByDesc('created_at'));
    }

    public function show(ModelsRequest $request)
    {
        if  (auth()->user()->id !== $request->user_id) {
            return response()->json(['message' => 'You can only see your own requests.'], 403);
        }
        return new RequestResource($request);
    }

    public function store(RequestRequest $request)
    {
        $request = auth()->user()->requests()->create($request->validated());
        return new RequestResource($request);
    }
    public function update($id, Request $request)
    {
        $modelRequest = ModelsRequest::find($id);

        $request->validate([
            'status' => 'in:pending,in_progress,completed'
        ]);
        $modelRequest->update(['status' => $request['status']]);
        $modelRequest->save();
        return new RequestResource($request);
    }
}
