<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アカウント削除 - Blog Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <div class="max-w-4xl mx-auto px-4 py-10 space-y-6">
        <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 text-indigo-600 font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            ダッシュボードに戻る
        </a>

        <div class="bg-white border border-red-100 rounded-3xl shadow-xl p-8 space-y-6">
            <header class="space-y-2">
                <p class="text-sm uppercase tracking-[0.4em] text-red-500">Account</p>
                <h1 class="text-3xl font-black text-slate-900">アカウント削除手続き</h1>
                <p class="text-slate-600">{{ $userName }} さんのワークスペースから、記事・カテゴリを含むすべてのデータが削除されます。</p>
            </header>

            <div class="grid md:grid-cols-3 gap-4">
                <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">登録ユーザー</p>
                    <p class="text-xl font-semibold text-slate-800">{{ $userName }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">保管されている記事</p>
                    <p class="text-3xl font-black text-indigo-600">{{ number_format($articleCount) }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                    <p class="text-xs text-slate-500">削除予定</p>
                    <p class="text-sm text-slate-700">記事、カテゴリ、APIトークン、下書き等すべて</p>
                </div>
            </div>

            @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
                <p class="text-sm font-semibold text-red-700 mb-2">入力内容に誤りがあります。</p>
                <ul class="text-sm text-red-600 list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('account.destroy') }}" method="POST" class="space-y-5">
                @csrf
                @method('DELETE')

                <div class="space-y-3">
                    <label class="flex items-start gap-3 bg-slate-50 border border-slate-200 rounded-2xl p-4">
                        <input type="checkbox" name="ack_articles" value="1" class="mt-1 w-5 h-5" {{ old('ack_articles') ? 'checked' : '' }}>
                        <span class="text-sm text-slate-700">作成した記事・カテゴリがすべて削除され、復旧できないことを理解しました。</span>
                    </label>
                    <label class="flex items-start gap-3 bg-slate-50 border border-slate-200 rounded-2xl p-4">
                        <input type="checkbox" name="ack_irreversible" value="1" class="mt-1 w-5 h-5" {{ old('ack_irreversible') ? 'checked' : '' }}>
                        <span class="text-sm text-slate-700">削除処理は即時に実行され、取り消す方法がないことに同意します。</span>
                    </label>
                    <label class="flex items-start gap-3 bg-slate-50 border border-slate-200 rounded-2xl p-4">
                        <input type="checkbox" name="ack_support" value="1" class="mt-1 w-5 h-5" {{ old('ack_support') ? 'checked' : '' }}>
                        <span class="text-sm text-slate-700">困りごとがあれば <a href="mailto:gyarmex@gmail.com" class="text-indigo-600 font-semibold">gyarmex@gmail.com</a> に相談できることを確認しました。</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">確認のため「削除します」と入力してください</label>
                    <input type="text" name="phrase" value="{{ old('phrase') }}" placeholder="削除します"
                        class="w-full px-4 py-3 rounded-xl border-2 border-slate-300 focus:border-red-400 focus:ring-2 focus:ring-red-200" required>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-4 rounded-2xl shadow-lg">
                        すべてのデータを削除する
                    </button>
                    <a href="{{ route('articles.index') }}" class="flex-1 text-center bg-white border border-slate-200 text-slate-700 font-semibold py-4 rounded-2xl">
                        キャンセル
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
