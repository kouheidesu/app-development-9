<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Blog Assistant - 記事作成管理</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* スマホでの入力時のズーム防止と大きな入力欄 */
        input,
        textarea,
        select {
            font-size: 18px !important;
            min-height: 48px !important;
            line-height: 1.5 !important;
        }

        textarea {
            min-height: 120px !important;
        }

        /* ラベルも大きく */
        label {
            font-size: 16px !important;
        }

        /* ボタンも大きく */
        button[type="submit"] {
            min-height: 56px !important;
            font-size: 18px !important;
        }

        /* モーダルのスクロール対応 */
        .modal-content {
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
        }

        /* iOS Safariでのビューポート高さ対応 */
        @supports (-webkit-touch-callout: none) {
            .modal-content {
                max-height: calc(100dvh - 2rem);
            }
        }

        /* テキストの切り詰め */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* フォーカス時のスタイル強化 */
        input:focus,
        textarea:focus,
        select:focus {
            transform: scale(1.01);
            transition: transform 0.2s;
        }

        /* 全画面入力モーダル */
        .fullscreen-input-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: white;
            z-index: 9999;
            padding: 1rem;
            overflow-y: auto;
        }

        .fullscreen-input-modal.active {
            display: flex;
            flex-direction: column;
        }

        .fullscreen-input-modal input,
        .fullscreen-input-modal textarea,
        .fullscreen-input-modal select {
            font-size: 20px !important;
            min-height: 56px !important;
            padding: 1rem !important;
        }

        .fullscreen-input-modal textarea {
            flex: 1;
            min-height: 300px !important;
        }

        /* 自動保存インジケーター */
        .autosave-indicator {
            position: fixed;
            top: 1rem;
            right: 1rem;
            padding: 0.5rem 1rem;
            background: #10b981;
            color: white;
            border-radius: 0.5rem;
            font-size: 14px;
            opacity: 0;
            transition: opacity 0.3s;
            z-index: 10000;
        }

        .autosave-indicator.show {
            opacity: 1;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- 自動保存インジケーター -->
    <div id="autosaveIndicator" class="autosave-indicator">
        ✓ 自動保存しました
    </div>

    <!-- 全画面入力モーダル -->
    <div id="fullscreenInputModal" class="fullscreen-input-modal">
        <div class="flex items-center justify-between mb-4">
            <h2 id="fullscreenInputLabel" class="text-xl font-bold text-slate-800"></h2>
            <button onclick="closeFullscreenInput()" class="p-2 hover:bg-slate-100 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="fullscreenInputContainer" class="flex-1"></div>
        <button onclick="closeFullscreenInput()" class="mt-4 w-full bg-indigo-600 text-white font-bold py-4 rounded-lg text-lg">
            完了
        </button>
    </div>

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
                <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-4 sm:p-6 sticky top-4">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-4">📝 新規記事</h2>
                    <p class="text-xs text-slate-500 mb-6">WordPress (SWELL) 投稿用</p>

                    <form action="{{ route('articles.store') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- タイトル -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-2">タイトル *</label>
                            <input type="text" name="title" required
                                class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                        </div>

                        <!-- SEO設定（アコーディオン） -->
                        <details class="bg-blue-50 rounded-lg p-3">
                            <summary class="cursor-pointer font-semibold text-slate-700 text-sm">🔍 SEO設定</summary>
                            <div class="mt-3 space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-slate-600 mb-1">SEOタイトル</label>
                                    <input type="text" name="seo_title" placeholder="未設定の場合は記事タイトルを使用"
                                        class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:border-blue-500 outline-none">
                                    <p class="text-xs text-slate-500 mt-1">推奨: 60文字以内</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-600 mb-1">メタディスクリプション</label>
                                    <textarea name="seo_description" rows="2" placeholder="検索結果に表示される説明文"
                                        class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:border-blue-500 outline-none resize-none"></textarea>
                                    <p class="text-xs text-slate-500 mt-1">推奨: 120-160文字</p>
                                </div>
                            </div>
                        </details>

                        <!-- アイキャッチ画像 -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-2">🖼️ アイキャッチ画像URL</label>
                            <input type="url" name="featured_image" placeholder="https://example.com/image.jpg"
                                class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 outline-none">
                        </div>

                        <!-- ステータス・カテゴリ -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-2">ステータス</label>
                                <select name="status" class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 outline-none">
                                    <option value="draft">📋 下書き</option>
                                    <option value="in_progress">✏️ 執筆中</option>
                                    <option value="ready">✅ 公開準備</option>
                                    <option value="published">🚀 公開済み</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-2">カテゴリ</label>
                                <select name="category_id" class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 outline-none">
                                    <option value="">未選択</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 本文 -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block font-semibold text-slate-700">本文</label>
                                <button type="button" onclick="toggleSwellBlocks()" class="text-xs bg-gradient-to-r from-blue-500 to-cyan-500 text-white px-3 py-1 rounded-full hover:from-blue-600 hover:to-cyan-600">
                                    SWELL ブロック
                                </button>
                            </div>

                            <!-- SWELLブロック挿入パネル -->
                            <div id="swellBlocksPanel" class="hidden mb-3 p-3 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg border border-blue-200">
                                <p class="text-xs font-semibold text-slate-700 mb-2">クリックで本文に挿入:</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" onclick="insertSwellBlock('caption')" class="text-xs bg-white hover:bg-blue-50 border border-blue-300 px-3 py-2 rounded-lg text-slate-700">💬 キャプション</button>
                                    <button type="button" onclick="insertSwellBlock('balloon')" class="text-xs bg-white hover:bg-blue-50 border border-blue-300 px-3 py-2 rounded-lg text-slate-700">💭 吹き出し</button>
                                    <button type="button" onclick="insertSwellBlock('button')" class="text-xs bg-white hover:bg-blue-50 border border-blue-300 px-3 py-2 rounded-lg text-slate-700">🔘 ボタン</button>
                                    <button type="button" onclick="insertSwellBlock('box')" class="text-xs bg-white hover:bg-blue-50 border border-blue-300 px-3 py-2 rounded-lg text-slate-700">📦 ボックス</button>
                                    <button type="button" onclick="insertSwellBlock('step')" class="text-xs bg-white hover:bg-blue-50 border border-blue-300 px-3 py-2 rounded-lg text-slate-700">📊 ステップ</button>
                                    <button type="button" onclick="insertSwellBlock('faq')" class="text-xs bg-white hover:bg-blue-50 border border-blue-300 px-3 py-2 rounded-lg text-slate-700">❓ FAQ</button>
                                    <button type="button" onclick="insertSwellBlock('table')" class="text-xs bg-white hover:bg-blue-50 border border-blue-300 px-3 py-2 rounded-lg text-slate-700">📋 テーブル</button>
                                    <button type="button" onclick="insertSwellBlock('list')" class="text-xs bg-white hover:bg-blue-50 border border-blue-300 px-3 py-2 rounded-lg text-slate-700">✓ リスト</button>
                                </div>
                            </div>

                            <textarea id="contentArea" name="content" rows="6" class="fullscreen-target w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none resize-none"></textarea>
                        </div>

                        <!-- メモ -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-2">📝 メモ</label>
                            <textarea name="notes" rows="2" placeholder="アイデア、参考リンク、キーワードなど..."
                                class="fullscreen-target w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 outline-none resize-none"></textarea>
                        </div>

                        <button type="submit"
                            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold py-4 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all shadow-md text-lg">
                            記事を作成
                        </button>
                    </form>
                </div>
            </div>

            <!-- Main Content - Articles List -->
            <div class="lg:col-span-2 space-y-4">
                <!-- articlesの配列の値を全て繰り返して処理する -->
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
                        <div class="flex gap-2">
                            <button onclick="copyWordPressHTML({{ $article->id }})" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition" title="WordPress形式でコピー">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                            <button onclick="openEditModal({{ $article->id }})" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
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
                    </div>

                    <!-- 見出し構造 -->
                    @if($article->content)
                        @php
                            preg_match_all('/<h([2-4])>(.*?)<\/h\1>/i', $article->content, $headings);
                            $hasHeadings = !empty($headings[0]);
                        @endphp
                        @if($hasHeadings)
                        <details class="mb-3">
                            <summary class="text-xs text-blue-600 cursor-pointer hover:text-blue-700">📑 見出し構造を表示</summary>
                            <div class="mt-2 pl-3 border-l-2 border-blue-200">
                                @foreach($headings[1] as $index => $level)
                                    <div class="text-xs py-1 {{ $level == 2 ? 'font-semibold text-slate-700' : ($level == 3 ? 'pl-3 text-slate-600' : 'pl-6 text-slate-500') }}">
                                        {{ $level == 2 ? 'H2' : ($level == 3 ? 'H3' : 'H4') }}: {{ strip_tags($headings[2][$index]) }}
                                    </div>
                                @endforeach
                            </div>
                        </details>
                        @endif
                    @endif

                    @if($article->content)
                    <p class="text-sm text-slate-600 mb-3 line-clamp-2">{{ Str::limit(strip_tags($article->content), 150) }}</p>
                    @endif

                    <!-- SEO・アイキャッチ情報 -->
                    @if($article->seo_title || $article->seo_description || $article->featured_image)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3">
                        @if($article->seo_title)
                        <p class="text-xs text-blue-800"><strong>SEOタイトル:</strong> {{ $article->seo_title }}</p>
                        @endif
                        @if($article->seo_description)
                        <p class="text-xs text-blue-800 mt-1"><strong>説明文:</strong> {{ Str::limit($article->seo_description, 80) }}</p>
                        @endif
                        @if($article->featured_image)
                        <p class="text-xs text-blue-800 mt-1"><strong>🖼️</strong> アイキャッチ設定済み</p>
                        @endif
                    </div>
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
                <!-- 値がからの場合は以下処理を実行 -->
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

    <!-- 編集モーダル -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full modal-content">
            <div class="sticky top-0 bg-white border-b border-slate-200 px-4 sm:px-6 py-4 flex items-center justify-between rounded-t-2xl z-10">
                <h2 class="text-lg sm:text-xl font-bold text-slate-800">✏️ 記事を編集</h2>
                <button type="button" onclick="closeEditModal()" class="p-2 hover:bg-slate-100 rounded-lg transition">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="editForm" method="POST" class="p-4 sm:p-6 space-y-5">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold text-slate-700 mb-2">タイトル *</label>
                    <input type="text" name="title" id="edit_title" required
                        class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                </div>

                <!-- SEO設定（アコーディオン） -->
                <details class="bg-blue-50 rounded-lg p-3">
                    <summary class="cursor-pointer font-semibold text-slate-700 text-sm">🔍 SEO設定</summary>
                    <div class="mt-3 space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">SEOタイトル</label>
                            <input type="text" name="seo_title" id="edit_seo_title" placeholder="未設定の場合は記事タイトルを使用"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:border-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">メタディスクリプション</label>
                            <textarea name="seo_description" id="edit_seo_description" rows="2" placeholder="検索結果に表示される説明文"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 focus:border-blue-500 outline-none resize-none"></textarea>
                        </div>
                    </div>
                </details>

                <!-- アイキャッチ画像 -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-2">🖼️ アイキャッチ画像URL</label>
                    <input type="url" name="featured_image" id="edit_featured_image" placeholder="https://example.com/image.jpg"
                        class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-2">ステータス</label>
                        <select name="status" id="edit_status" class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 outline-none">
                            <option value="draft">📋 下書き</option>
                            <option value="in_progress">✏️ 執筆中</option>
                            <option value="ready">✅ 公開準備</option>
                            <option value="published">🚀 公開済み</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-2">カテゴリ</label>
                        <select name="category_id" id="edit_category_id" class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 outline-none">
                            <option value="">未選択</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block font-semibold text-slate-700">本文</label>
                        <button type="button" onclick="toggleSwellBlocksEdit()" class="text-xs bg-gradient-to-r from-blue-500 to-cyan-500 text-white px-3 py-1 rounded-full hover:from-blue-600 hover:to-cyan-600">
                            SWELL ブロック
                        </button>
                    </div>

                    <!-- SWELLブロック挿入パネル（編集用） -->
                    <div id="swellBlocksPanelEdit" class="hidden mb-3 p-3 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg border border-blue-200">
                        <p class="text-xs font-semibold text-slate-700 mb-2">クリックで本文に挿入:</p>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" onclick="insertSwellBlockEdit('caption')" class="text-xs bg-white hover:bg-blue-50 border border-blue-300 px-3 py-2 rounded-lg text-slate-700">💬 キャプション</button>
                            <button type="button" onclick="insertSwellBlockEdit('balloon')" class="text-xs bg-white hover:bg-blue-50 border border-blue-300 px-3 py-2 rounded-lg text-slate-700">💭 吹き出し</button>
                            <button type="button" onclick="insertSwellBlockEdit('button')" class="text-xs bg-white hover:bg-blue-50 border border-blue-300 px-3 py-2 rounded-lg text-slate-700">🔘 ボタン</button>
                            <button type="button" onclick="insertSwellBlockEdit('box')" class="text-xs bg-white hover:bg-blue-50 border border-blue-300 px-3 py-2 rounded-lg text-slate-700">📦 ボックス</button>
                            <button type="button" onclick="insertSwellBlockEdit('step')" class="text-xs bg-white hover:bg-blue-50 border border-blue-300 px-3 py-2 rounded-lg text-slate-700">📊 ステップ</button>
                            <button type="button" onclick="insertSwellBlockEdit('faq')" class="text-xs bg-white hover:bg-blue-50 border border-blue-300 px-3 py-2 rounded-lg text-slate-700">❓ FAQ</button>
                            <button type="button" onclick="insertSwellBlockEdit('table')" class="text-xs bg-white hover:bg-blue-50 border border-blue-300 px-3 py-2 rounded-lg text-slate-700">📋 テーブル</button>
                            <button type="button" onclick="insertSwellBlockEdit('list')" class="text-xs bg-white hover:bg-blue-50 border border-blue-300 px-3 py-2 rounded-lg text-slate-700">✓ リスト</button>
                        </div>
                    </div>

                    <textarea name="content" id="edit_content" rows="8" class="fullscreen-target w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none resize-none"></textarea>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-2">メモ</label>
                    <textarea name="notes" id="edit_notes" rows="2" placeholder="アイデア、参考リンクなど..."
                        class="fullscreen-target w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 outline-none resize-none"></textarea>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold py-4 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all shadow-md text-lg">
                        更新する
                    </button>
                    <button type="button" onclick="closeEditModal()"
                        class="sm:w-auto w-full px-6 bg-slate-200 text-slate-700 font-bold py-4 rounded-lg hover:bg-slate-300 transition-all text-lg">
                        キャンセル
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 記事データをJavaScriptで使えるように -->
    <script>
        const articles = @json($articles);

        // SWELLブロックのテンプレート
        const swellBlocks = {
            caption: '\n<!-- wp:swell-blocks/caption-box {"style":"default"} -->\n<div class="swell-block-caption-box"><p>ここにテキストを入力</p></div>\n<!-- /wp:swell-blocks/caption-box -->\n',
            balloon: '\n<!-- wp:swell-blocks/balloon {"iconName":"icon-1","iconType":"img"} -->\n<div class="swell-block-balloon"><div class="swell-block-balloon__icon"><img src="" alt=""/></div><div class="swell-block-balloon__body"><p>ここに会話内容を入力</p></div></div>\n<!-- /wp:swell-blocks/balloon -->\n',
            button: '\n<!-- wp:swell-blocks/button {"buttonUrl":"#","buttonText":"ボタンテキスト","buttonStyle":"normal"} /-->\n',
            box: '\n<!-- wp:swell-blocks/box {"boxStyle":"default"} -->\n<div class="swell-block-box"><p>ここにテキストを入力</p></div>\n<!-- /wp:swell-blocks/box -->\n',
            step: '\n<!-- wp:swell-blocks/step {"steps":[]} -->\n<div class="swell-block-step"><div class="step-item"><span class="step-number">1</span><p>ステップ1の内容</p></div></div>\n<!-- /wp:swell-blocks/step -->\n',
            faq: '\n<!-- wp:swell-blocks/faq -->\n<div class="swell-block-faq"><dt>質問をここに入力</dt><dd>回答をここに入力</dd></div>\n<!-- /wp:swell-blocks/faq -->\n',
            table: '\n<table class="wp-block-table"><tbody><tr><td>項目1</td><td>内容1</td></tr><tr><td>項目2</td><td>内容2</td></tr></tbody></table>\n',
            list: '\n<ul><li>リスト項目1</li><li>リスト項目2</li><li>リスト項目3</li></ul>\n'
        };

        // SWELLブロックパネルの表示切り替え
        function toggleSwellBlocks() {
            const panel = document.getElementById('swellBlocksPanel');
            panel.classList.toggle('hidden');
        }

        function toggleSwellBlocksEdit() {
            const panel = document.getElementById('swellBlocksPanelEdit');
            panel.classList.toggle('hidden');
        }

        // SWELLブロックを本文に挿入（新規作成用）
        function insertSwellBlock(type) {
            const textarea = document.getElementById('contentArea');
            const cursorPos = textarea.selectionStart;
            const textBefore = textarea.value.substring(0, cursorPos);
            const textAfter = textarea.value.substring(cursorPos);

            textarea.value = textBefore + swellBlocks[type] + textAfter;
            textarea.focus();

            // カーソル位置を挿入したブロックの後に移動
            const newCursorPos = cursorPos + swellBlocks[type].length;
            textarea.setSelectionRange(newCursorPos, newCursorPos);
        }

        // SWELLブロックを本文に挿入（編集用）
        function insertSwellBlockEdit(type) {
            const textarea = document.getElementById('edit_content');
            const cursorPos = textarea.selectionStart;
            const textBefore = textarea.value.substring(0, cursorPos);
            const textAfter = textarea.value.substring(cursorPos);

            textarea.value = textBefore + swellBlocks[type] + textAfter;
            textarea.focus();

            const newCursorPos = cursorPos + swellBlocks[type].length;
            textarea.setSelectionRange(newCursorPos, newCursorPos);
        }

        // WordPress形式でコピー
        function copyWordPressHTML(articleId) {
            const article = articles.find(a => a.id === articleId);
            if (!article) return;

            // WordPress投稿用のHTMLを生成
            let html = '';

            // タイトル（コメントとして）
            html += `<!-- タイトル: ${article.title} -->\n\n`;

            // SEO情報（コメントとして）
            if (article.seo_title || article.seo_description) {
                html += `<!-- SEO設定 -->\n`;
                if (article.seo_title) html += `<!-- SEOタイトル: ${article.seo_title} -->\n`;
                if (article.seo_description) html += `<!-- 説明文: ${article.seo_description} -->\n`;
                html += `\n`;
            }

            // アイキャッチ画像（コメントとして）
            if (article.featured_image) {
                html += `<!-- アイキャッチ画像: ${article.featured_image} -->\n\n`;
            }

            // 本文
            html += article.content || '';

            // クリップボードにコピー
            navigator.clipboard.writeText(html).then(() => {
                alert('✅ WordPress形式でクリップボードにコピーしました！\nWordPressの投稿画面に貼り付けてください。');
            }).catch(err => {
                alert('❌ コピーに失敗しました: ' + err);
            });
        }

        function openEditModal(articleId) {
            const article = articles.find(a => a.id === articleId);
            if (!article) return;

            // フォームのaction属性を設定
            document.getElementById('editForm').action = `/articles/${articleId}`;

            // フォームの各フィールドに値を設定
            document.getElementById('edit_title').value = article.title || '';
            document.getElementById('edit_status').value = article.status || 'draft';
            document.getElementById('edit_category_id').value = article.category_id || '';
            document.getElementById('edit_content').value = article.content || '';
            document.getElementById('edit_notes').value = article.notes || '';
            document.getElementById('edit_seo_title').value = article.seo_title || '';
            document.getElementById('edit_seo_description').value = article.seo_description || '';
            document.getElementById('edit_featured_image').value = article.featured_image || '';

            // モーダルを表示
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');

            // 背景のスクロールを防ぐ
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');

            // 背景のスクロールを戻す
            document.body.style.overflow = '';
        }

        // モーダル外クリックで閉じる
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // 全画面入力機能
        let currentFullscreenInput = null;
        let fullscreenInputOriginalParent = null;

        function openFullscreenInput(element) {
            const label = element.closest('div').querySelector('label');
            const labelText = label ? label.textContent : '入力';

            currentFullscreenInput = element;
            fullscreenInputOriginalParent = element.parentElement;

            // ラベルを設定
            document.getElementById('fullscreenInputLabel').textContent = labelText;

            // 入力欄をクローン
            const clone = element.cloneNode(true);
            clone.value = element.value;
            clone.id = element.id + '_fullscreen';

            // コンテナに追加
            const container = document.getElementById('fullscreenInputContainer');
            container.innerHTML = '';
            container.appendChild(clone);

            // モーダルを表示
            document.getElementById('fullscreenInputModal').classList.add('active');
            document.body.style.overflow = 'hidden';

            // フォーカス
            setTimeout(() => clone.focus(), 100);

            // 入力値を同期
            clone.addEventListener('input', function() {
                element.value = clone.value;
                // 自動保存をトリガー
                handleAutoSave(element);
            });
        }

        function closeFullscreenInput() {
            const modal = document.getElementById('fullscreenInputModal');
            modal.classList.remove('active');
            document.body.style.overflow = '';

            // クリーンアップ
            document.getElementById('fullscreenInputContainer').innerHTML = '';
            currentFullscreenInput = null;
            fullscreenInputOriginalParent = null;
        }

        // 自動保存機能
        let autoSaveTimeout = null;
        const AUTOSAVE_DELAY = 2000; // 2秒後に自動保存

        function showAutoSaveIndicator() {
            const indicator = document.getElementById('autosaveIndicator');
            indicator.classList.add('show');
            setTimeout(() => {
                indicator.classList.remove('show');
            }, 2000);
        }

        function handleAutoSave(element) {
            // 既存のタイムアウトをクリア
            if (autoSaveTimeout) {
                clearTimeout(autoSaveTimeout);
            }

            // 2秒後に自動保存
            autoSaveTimeout = setTimeout(() => {
                saveFormData();
                showAutoSaveIndicator();
            }, AUTOSAVE_DELAY);
        }

        function saveFormData() {
            // 新規作成フォームのデータを保存
            const newArticleForm = document.querySelector('form[action*="articles.store"]');
            if (newArticleForm) {
                const formData = {
                    title: newArticleForm.querySelector('[name="title"]').value,
                    status: newArticleForm.querySelector('[name="status"]').value,
                    category_id: newArticleForm.querySelector('[name="category_id"]').value,
                    content: newArticleForm.querySelector('[name="content"]').value,
                    notes: newArticleForm.querySelector('[name="notes"]').value,
                };
                localStorage.setItem('blog_assistant_draft', JSON.stringify(formData));
            }

            // 編集フォームのデータを保存（編集中の場合）
            const editForm = document.getElementById('editForm');
            if (editForm && !document.getElementById('editModal').classList.contains('hidden')) {
                const articleId = editForm.action.split('/').pop();
                const formData = {
                    title: document.getElementById('edit_title').value,
                    status: document.getElementById('edit_status').value,
                    category_id: document.getElementById('edit_category_id').value,
                    content: document.getElementById('edit_content').value,
                    notes: document.getElementById('edit_notes').value,
                };
                localStorage.setItem('blog_assistant_edit_' + articleId, JSON.stringify(formData));
            }
        }

        function restoreFormData() {
            // 新規作成フォームのデータを復元
            const savedDraft = localStorage.getItem('blog_assistant_draft');
            if (savedDraft) {
                try {
                    const data = JSON.parse(savedDraft);
                    const form = document.querySelector('form[action*="articles.store"]');
                    if (form && data.title) {
                        const shouldRestore = confirm('保存された下書きがあります。復元しますか？');
                        if (shouldRestore) {
                            form.querySelector('[name="title"]').value = data.title || '';
                            form.querySelector('[name="status"]').value = data.status || 'draft';
                            form.querySelector('[name="category_id"]').value = data.category_id || '';
                            form.querySelector('[name="content"]').value = data.content || '';
                            form.querySelector('[name="notes"]').value = data.notes || '';
                        }
                    }
                } catch (e) {
                    console.error('下書きの復元に失敗しました', e);
                }
            }
        }

        // フォーム送信時に自動保存データをクリア
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                localStorage.removeItem('blog_assistant_draft');
            });
        });

        // ページ読み込み時に下書きを復元
        document.addEventListener('DOMContentLoaded', function() {
            restoreFormData();

            // 本文とメモ（fullscreen-target）には全画面入力を適用
            const fullscreenTargets = document.querySelectorAll('.fullscreen-target');
            fullscreenTargets.forEach(input => {
                // フォーカス時に全画面表示（すべてのデバイス）
                input.addEventListener('focus', function(e) {
                    openFullscreenInput(this);
                });

                // 入力時に自動保存
                input.addEventListener('input', function() {
                    handleAutoSave(this);
                });
            });

            // その他の入力欄には自動保存のみ適用
            const otherInputs = document.querySelectorAll('input[type="text"], textarea:not(.fullscreen-target)');
            otherInputs.forEach(input => {
                // 入力時に自動保存
                input.addEventListener('input', function() {
                    handleAutoSave(this);
                });
            });
        });

        // Escキーで全画面入力を閉じる
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const fullscreenModal = document.getElementById('fullscreenInputModal');
                if (fullscreenModal.classList.contains('active')) {
                    closeFullscreenInput();
                } else {
                    closeEditModal();
                }
            }
        });
    </script>

    <!-- cdn.min.js使用するため -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>

</html>
