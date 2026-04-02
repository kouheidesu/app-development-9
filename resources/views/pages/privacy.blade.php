<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プライバシーポリシー - Blog Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <div class="max-w-3xl mx-auto px-4 py-10">
        <a href="{{ url()->previous() === url()->current() ? route('articles.index') : url()->previous() }}"
            class="inline-flex items-center gap-2 text-indigo-600 font-semibold mb-6">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            戻る
        </a>

        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8 space-y-8">
            <header>
                <p class="text-sm uppercase tracking-[0.25em] text-slate-500 mb-2">Policy</p>
                <h1 class="text-3xl font-black text-slate-900">Blog Assistant プライバシーポリシー</h1>
                <p class="text-slate-600 mt-2">アプリ/ウェブの両方で共通して適用されるデータの取り扱い方針です。</p>
            </header>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-semibold">1</span>
                    収集する情報
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    メールアドレス・表示名・作成した記事やカテゴリなど、サービス提供に必要な最小限のデータのみ保存します。
                    端末上には暗号化したトークンのみが保持され、パスワード等を平文で保管することはありません。
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-semibold">2</span>
                    利用目的
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    認証、記事管理、サポート対応を目的に利用し、第三者への販売や無断提供は行いません。
                    サービス改善に必要なアクセス状況の分析以外での用途には使用しません。
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-semibold">3</span>
                    保存期間と削除
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    アカウントを削除すると、関連する記事やカテゴリを含むすべてのデータを 30 日以内に完全削除します。
                    自己削除はアプリ内・Web 内の「アカウント削除」画面から手続きできます。
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-semibold">4</span>
                    お問い合わせ
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    データの開示・訂正・削除に関するお問い合わせは <a href="mailto:gyarmex@gmail.com" class="text-indigo-600 font-semibold">gyarmex@gmail.com</a>
                    までご連絡ください。法令および社内ポリシーに基づき適切に対応します。
                </p>
            </section>
        </div>
    </div>
</body>

</html>
