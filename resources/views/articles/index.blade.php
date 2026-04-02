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

        /* 編集モーダルを常に全画面で表示 */
        .modal-content {
            width: 100%;
            max-width: none;
            height: 100vh;
            max-height: 100vh;
            border-radius: 0;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        /* iOS Safariでのビューポート高さ対応 */
        @supports (-webkit-touch-callout: none) {
            .modal-content {
                height: 100dvh;
                max-height: 100dvh;
            }

            .fullscreen-input-modal {
                height: 100dvh;
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
            width: 100vw;
            height: 100vh;
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

        #fullscreenInputContainer {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
        }

        .fullscreen-input-modal input,
        .fullscreen-input-modal textarea,
        .fullscreen-input-modal select {
            font-size: 20px !important;
            min-height: 56px !important;
            padding: 1rem !important;
            width: 100%;
            box-sizing: border-box;
        }

        .fullscreen-input-modal textarea {
            flex: 1;
            min-height: 0 !important;
            height: 100%;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
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
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        ✍️ Blog Assistant
                    </h1>
                    <p class="text-slate-600 mt-1">ブログ記事の作成を効率化</p>
                </div>
                <div class="flex items-center gap-3 flex-wrap justify-end">
                    <div class="bg-white rounded-full px-4 py-2 shadow-sm border border-slate-200">
                        <span class="text-sm font-semibold text-slate-600">記事数: </span>
                        <!-- articles変数の値でcountメソッドを実行 -->
                        <span class="text-sm font-bold text-indigo-600">{{ $articles->count() }}</span>
                    </div>
                    <div class="bg-white rounded-lg px-4 py-2 shadow-sm border border-slate-200 flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-sm font-semibold text-slate-700">{{ auth()->user()->name }}</span>
                    </div>
                    <a href="{{ route('privacy') }}"
                        class="text-sm font-semibold text-indigo-600 bg-white border border-indigo-100 px-4 py-2 rounded-lg shadow-sm hover:bg-indigo-50 transition">
                        プライバシーポリシー
                    </a>
                    <a href="{{ route('account.delete') }}"
                        class="text-sm font-semibold text-red-500 bg-white border border-red-100 px-4 py-2 rounded-lg shadow-sm hover:bg-red-50 transition">
                        アカウント削除
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-lg shadow-sm transition text-sm">
                            ログアウト
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Left Sidebar - New Article Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-4 sm:p-6 sticky top-4">
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-6">📝 新規記事</h2>
                    <form action="{{ route('articles.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block font-semibold text-slate-700 mb-2">タイトル *</label>
                            <input type="text" name="title" id="create_title" required
                                class="char-count-input w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                            <div class="text-sm text-slate-500 mt-1 text-right">
                                <span id="create_title_count">0</span>文字
                            </div>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-2">ステータス</label>
                            <select name="status" class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 outline-none">
                                <option value="draft">📋 下書き</option>

                                <option value="published">🚀 公開済み</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-2">カテゴリ</label>
                            <select name="category_id" class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 outline-none">
                                <option value="">未選択</option>
                                <!-- categories配列の中身分繰り返す -->
                                @foreach($categories as $cat)
                                <!-- 配列〇〇のidを表示 -->
                                <option value="{{ $cat->id }}">
                                    <!-- 配列〇〇のnameを表示 -->
                                    {{ $cat->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-2">目次</label>
                            <textarea name="table_of_contents" id="create_table_of_contents" rows="2" placeholder="記事の目次を入力..."
                                class="fullscreen-target char-count-input w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 outline-none resize-none"></textarea>
                            <div class="text-sm text-slate-500 mt-1 text-right">
                                <span id="create_table_of_contents_count">0</span>文字
                            </div>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-2">本文</label>
                            <textarea name="content" id="create_content" rows="4" class="fullscreen-target char-count-input w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none resize-none"></textarea>
                            <div class="text-sm text-slate-500 mt-1 text-right">
                                <span id="create_content_count">0</span>文字
                            </div>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-2">メモ</label>
                            <textarea name="notes" id="create_notes" rows="2" placeholder="アイデア、参考リンクなど..."
                                class="fullscreen-target char-count-input w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 outline-none resize-none"></textarea>
                            <div class="text-sm text-slate-500 mt-1 text-right">
                                <span id="create_notes_count">0</span>文字
                            </div>
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
                                    {{ $article->status === 'published' ? '🚀 公開済み' :'📋 下書き'}}

                                </span>
                                @if($article->word_count > 0)
                                <span class="text-xs text-slate-500">{{ number_format($article->word_count) }}字</span>
                                @endif
                                <span class="text-xs text-slate-400">{{ $article->created_at->format('m/d') }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
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

                    @if($article->table_of_contents)
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 mb-3">
                        <p class="text-xs font-semibold text-slate-700 mb-1">📑 目次</p>
                        <p class="text-xs text-slate-600 whitespace-pre-wrap">{{ Str::limit($article->table_of_contents, 100) }}</p>
                    </div>
                    @endif

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
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-stretch justify-center z-50 p-0">
        <div class="bg-white shadow-2xl w-full modal-content">
            <div class="sticky top-0 bg-white border-b border-slate-200 px-4 sm:px-6 py-4 flex items-center justify-between z-10">
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
                        class="char-count-input w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                    <div class="text-sm text-slate-500 mt-1 text-right">
                        <span id="edit_title_count">0</span>文字
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-2">ステータス</label>
                    <select name="status" id="edit_status" class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 outline-none">
                        <option value="draft">📋 下書き</option>

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
                <div>
                    <label class="block font-semibold text-slate-700 mb-2">目次</label>
                    <textarea name="table_of_contents" id="edit_table_of_contents" rows="2" placeholder="記事の目次を入力..."
                        class="fullscreen-target char-count-input w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 outline-none resize-none"></textarea>
                    <div class="text-sm text-slate-500 mt-1 text-right">
                        <span id="edit_table_of_contents_count">0</span>文字
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-2">本文</label>
                    <textarea name="content" id="edit_content" rows="6" class="fullscreen-target char-count-input w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none resize-none"></textarea>
                    <div class="text-sm text-slate-500 mt-1 text-right">
                        <span id="edit_content_count">0</span>文字
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-2">メモ</label>
                    <textarea name="notes" id="edit_notes" rows="3" placeholder="アイデア、参考リンクなど..."
                        class="fullscreen-target char-count-input w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 outline-none resize-none"></textarea>
                    <div class="text-sm text-slate-500 mt-1 text-right">
                        <span id="edit_notes_count">0</span>文字
                    </div>
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

        function openEditModal(articleId) {
            const article = articles.find(a => a.id === articleId);
            // もしarticle変数でなかったら戻り値を返す
            if (!article) return;

            // フォームのaction属性を設定
            document.getElementById('editForm').action = `/articles/${articleId}`;

            // フォームの各フィールドに値を設定
            document.getElementById('edit_title').value = article.title || '';
            // 'edit_status'の値にarticle.statusが存在すればこれを代入、なければ'draft'を代入
            document.getElementById('edit_status').value = article.status || 'draft';
            // 'edit_category_id'の値にarticle.category_idが存在すればこちらを代入。なければ''を代入。
            document.getElementById('edit_category_id').value = article.category_id || '';
            document.getElementById('edit_table_of_contents').value = article.table_of_contents || '';
            document.getElementById('edit_content').value = article.content || '';
            document.getElementById('edit_notes').value = article.notes || '';

            // 文字数カウントを更新
            updateCharCount('edit_title');
            updateCharCount('edit_table_of_contents');
            updateCharCount('edit_content');
            updateCharCount('edit_notes');

            // モーダルを表示
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');

            // 背景のスクロールを防ぐ
            document.body.style.overflow = 'hidden';
        }

        // 文字数カウント関数
        function updateCharCount(inputId) {
            const input = document.getElementById(inputId);
            const countSpan = document.getElementById(inputId + '_count');
            if (input && countSpan) {
                const count = input.value.length;
                countSpan.textContent = count;
            }
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

            // 文字数表示要素を作成
            const charCountDiv = document.createElement('div');
            charCountDiv.className = 'text-sm text-slate-500 mt-2 text-right';
            const charCountSpan = document.createElement('span');
            charCountSpan.id = clone.id + '_count';
            charCountSpan.textContent = element.value.length;
            charCountDiv.appendChild(charCountSpan);
            charCountDiv.appendChild(document.createTextNode('文字'));

            // コンテナに追加
            const container = document.getElementById('fullscreenInputContainer');
            container.innerHTML = '';
            container.appendChild(clone);
            container.appendChild(charCountDiv);

            // モーダルを表示
            document.getElementById('fullscreenInputModal').classList.add('active');
            document.body.style.overflow = 'hidden';

            // フォーカス
            setTimeout(() => clone.focus(), 100);

            // 入力値を同期＆文字数更新
            clone.addEventListener('input', function() {
                element.value = clone.value;
                // 元のフィールドの文字数も更新
                updateCharCount(element.id);
                // 全画面モーダル内の文字数も更新
                charCountSpan.textContent = clone.value.length;
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

        // ページ読み込み時の処理
        document.addEventListener('DOMContentLoaded', function() {
            // 目次、本文、メモ（fullscreen-target）には全画面入力を適用
            const fullscreenTargets = document.querySelectorAll('.fullscreen-target');
            fullscreenTargets.forEach(input => {
                // フォーカス時に全画面表示
                input.addEventListener('focus', function(e) {
                    openFullscreenInput(this);
                });
            });

            // 文字数カウント機能を全ての入力フィールドに追加
            const charCountInputs = document.querySelectorAll('.char-count-input');
            charCountInputs.forEach(input => {
                // 初期値の文字数を表示
                updateCharCount(input.id);

                // input イベントで文字数を更新
                input.addEventListener('input', function() {
                    updateCharCount(this.id);
                });
            });
        });

        // Escキーで全画面入力を閉じる
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const fullscreenModal = document.getElementById('fullscreenInputModal');
                if (fullscreenModal && fullscreenModal.classList.contains('active')) {
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
