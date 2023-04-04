<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;

class TagController extends Controller
{
    public function index(Tag $tag)
    {
        return response([
            'tag' => $tag->orderBy('created_at', 'desc')->get(),
        ], 200);

    }

    public function show(Tag $tag)
    {
        return response([
            'tag' => $tag->where('id', $tag->id)->get(),
        ], 200);
    }

    public function store(StoreTagRequest $request)
    {
        $tag = Tag::create([
            'name' => $request->validated('name'),
        ]);

        return response([
            'message' => 'Tag created.',
            'tag' => $tag,
        ], 200);
    }

    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag = $tag->find($tag->id);

        if (!$tag) {
            return response([
                'message' => 'Tag not found.',
            ], 403);
        }

        if ($tag->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied.',
            ], 403);
        }

        $tag->update([
            'name' => $request->validated('name'),
        ]);

        return response([
            'message' => 'Tag updated.',
            'tag' => $tag,
        ], 200);
    }

    public function destroy(Tag $tag)
    {
        $tag = $tag->find($tag->id);

        if (!$tag) {
            return response([
                'message' => 'Categroy not found.',
            ], 403);
        }

        if ($tag->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied.',
            ], 403);
        }

        $tag->forceDelete();

        return response([
            'message' => 'Tag deleted.',
        ], 200);
    }
}