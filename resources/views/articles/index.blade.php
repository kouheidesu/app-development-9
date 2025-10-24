<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Assistant - 記事作成管理</title>
    <!-- viteを使うところを指定している -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- Success Message -->
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed top-4 right-4 z-50">
        <div class="bg-white rounded-xl shadow-lg border border-emerald-200 px-5 py-3 flex items-center gap-3">
            <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <!-- successの値を一時的に取得 -->
            <p class="text-sm font-medium text-slate-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <header class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        ✍️ Blog Assistant
                    </h1>
                    <p class="text-slate-600 mt-1">ブログ記事の作成を効率化</p>
                </div>
                <div class="bg-white rounded-full px-4 py-2 shadow-sm border border-slate-200">
                    <span class="text-sm font-semibold text-slate-600">記事数: </span>
                    <!-- articles変数の値でcountメソッドを実行 -->
                    <span class="text-sm font-bold text-indigo-600">{{ $articles->count() }}</span>
                </div>
            </div>
        </header>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Left Sidebar - New Article Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 sticky top-4">
                    <h2 class="text-xl font-bold text-slate-800 mb-4">📝 新規記事</h2>
                    <form action="{{ route('articles.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">タイトル *</label>
                            <input type="text" name="title" required
                                class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">ステータス</label>
                            <select name="status" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 outline-none text-sm">
                                <option value="draft">📋 下書き</option>
                                <option value="in_progress">✏️ 執筆中</option>
                                <option value="ready">✅ 公開準備</option>
                                <option value="published">🚀 公開済み</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">カテゴリ</label>
                            <select name="category_id" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 outline-none text-sm">
                                <option value="">未選択</option>
                                <!-- categories配列の中身分繰り返す -->
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">本文</label>
                            <textarea name="content" rows="4" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">メモ</label>
                            <textarea name="notes" rows="2" placeholder="アイデア、参考リンクなど..."
                                class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 outline-none text-sm resize-none"></textarea>
                        </div>
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold py-2.5 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all shadow-md">
                            記事を作成
                        </button>
                    </form>
                </div>
            </div>

            <!-- Main Content - Articles List -->
            <div class="lg:col-span-2 space-y-4">
                @forelse($articles as $article)
                <div class="bg-white rounded-xl shadow-md border border-slate-200 p-5 hover:shadow-lg transition-all">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-slate-800 mb-1">{{ $article->title }}</h3>
                            <div class="flex flex-wrap gap-2 items-center">
                                @if($article->category)
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold" style="background-color: {{ $article->category->color }}20; color: {{ $article->category->color }};">
                                    {{ $article->category->name }}
                                </span>
                                @endif
                                <span class="px-2 py-1 rounded-md text-xs font-semibold
                                    {{ $article->status === 'published' ? 'bg-green-100 text-green-700' :
                                       ($article->status === 'ready' ? 'bg-blue-100 text-blue-700' :
                                       ($article->status === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : 'bg-slate-100 text-slate-700')) }}">
                                    {{ $article->status === 'published' ? '🚀 公開済み' :
                                       ($article->status === 'ready' ? '✅ 準備完了' :
                                       ($article->status === 'in_progress' ? '✏️ 執筆中' : '📋 下書き')) }}
                                </span>
                                @if($article->word_count > 0)
                                <span class="text-xs text-slate-500">{{ number_format($article->word_count) }}字</span>
                                @endif
                                <span class="text-xs text-slate-400">{{ $article->created_at->format('m/d') }}</span>
                            </div>
                        </div>
                        <form action="{{ route('articles.destroy', $article) }}" method="POST" onsubmit="return confirm('削除しますか？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>

                    @if($article->content)
                    <p class="text-sm text-slate-600 mb-3 line-clamp-2">{{ Str::limit($article->content, 150) }}</p>
                    @endif

                    @if($article->notes)
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-3">
                        <p class="text-xs text-amber-800"><strong>メモ:</strong> {{ $article->notes }}</p>
                    </div>
                    @endif

                    @if($article->tags->count() > 0)
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        @foreach($article->tags as $tag)
                        <span class="inline-block px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-xs">#{{ $tag->name }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-16">
                    <div class="text-6xl mb-4">📝</div>
                    <p class="text-lg font-semibold text-slate-400">まだ記事がありません</p>
                    <p class="text-sm text-slate-400 mt-1">左のフォームから新しい記事を作成しましょう！</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>

</html>
