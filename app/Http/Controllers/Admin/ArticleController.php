<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('user')->orderByDesc('created_at')->get();

        return view('admin.articles', compact('articles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        Article::create([
            'user_id' => auth()->id(),
            'title'   => $validated['title'],
            'content' => $validated['content'],
        ]);

        ActivityLog::record('publish_article', "Mempublikasikan artikel: {$validated['title']}");

        return back()->with('success', 'Artikel berhasil dipublikasikan.');
    }

    public function destroy(Article $article)
    {
        $title = $article->title;
        $article->delete();

        ActivityLog::record('delete_article', "Menghapus artikel: {$title}");

        return back()->with('success', 'Artikel berhasil dihapus.');
    }
}
