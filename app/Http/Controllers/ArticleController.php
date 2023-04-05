<?php

namespace App\Http\Controllers;

use App\Events\ArticlePublished;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Models\Image;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function index()
    {
        return response([
            'articles' => Article::with(['tags'])->orderBy('created_at', 'desc')->with('user:id,name')->get(),
        ], 200);

    }

    public function show(Article $article)
    {
        return response([
            'articles' => $article->where('id', $article->id)->get(),
        ], 200);
    }

    public function store(StoreArticleRequest $request)
    {
        $article = DB::transaction(function () use ($request) {
            $article = Article::create([
                'title' => $request->validated('title'),
                'details' => $request->validated('details'),
                'user_id' => auth()->user()->id,
            ]);

            if ($request->validated('tag') != null) {
                $article->tags()->sync($request->validated('tag'));
            }

            if ($request->hasfile('image')) {
                foreach ($request->file('image') as $file) {
                    $name = $file->getClientOriginalName();
                    $file->move(public_path() . '/Article/', $name);
                    $imgData[] = $name;
                }

                $ImageModal = new Image();
                $ImageModal->parentable_id = $article->id;
                $ImageModal->parentable_type = Article::class;
                $ImageModal->name = json_encode($imgData);
                $ImageModal->image_path = json_encode($imgData);

                $ImageModal->save();
            }

            event(new ArticlePublished('Article Published'));

            return $article;
        });

        return response([
            'message' => 'article created.',
            '$article' => $article,
        ], 200);
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        $article = $article->find($article->id);

        if (!$article) {
            return response([
                'message' => 'Article not found.',
            ], 403);
        }

        if ($article->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied.',
            ], 403);
        }

        $article->update([
            'name' => $request->validated('name'),
        ]);

        if ($request->validated('tag') != null) {
            $article->tags()->sync($request->validated('tag'));
        }

        event(new ArticlePublished('Article Published'));

        return response([
            'message' => 'Category updated.',
            'art$article' => $article,
        ], 200);
    }

    public function destroy(Article $article)
    {
        $article = $article->find($article->id);

        if (!$article) {
            return response([
                'message' => 'Categroy not found.',
            ], 403);
        }

        if ($article->user_id != auth()->user()->id) {
            return response([
                'message' => 'Permission denied.',
            ], 403);
        }

        $article->forceDelete();

        return response([
            'message' => 'Article deleted.',
        ], 200);
    }
}
