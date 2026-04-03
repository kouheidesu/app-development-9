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
                <p class="text-slate-600 mt-2">
                    「ブログ記事作成アシスト」アプリ（提供者: gyarmex / 連絡先: <a href="mailto:gyarmex@gmail.com" class="text-indigo-600 underline">gyarmex@gmail.com</a>）は、
                    モバイルアプリと Railway 上の API の双方に本ポリシーを適用します。
                </p>
            </header>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-semibold">1</span>
                    取得する情報
                </h2>
                <ul class="list-disc list-inside text-slate-600 leading-relaxed space-y-2">
                    <li>アカウント登録時に氏名・メールアドレス・パスワード（サーバーでハッシュ化保存）を取得し、ログイン用トークンは端末の flutter_secure_storage に暗号化保存します。</li>
                    <li>ユーザーが作成する記事・カテゴリ・メモ・SEO関連フィールドなどのコンテンツデータ。</li>
                    <li>サーバーログとして IP アドレス、User-Agent、アクセス日時、エラー内容などを Railway/HTTP サーバーが自動取得します。</li>
                </ul>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-semibold">2</span>
                    利用目的
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    アカウント認証、記事・カテゴリ管理機能の提供、サポート対応、セキュリティ監査・不正利用防止、法令遵守のためにのみ利用し、第三者への販売は行いません。
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-semibold">3</span>
                    情報の共有
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    上記目的の達成に必要な範囲で、インフラ/メール等の委託先（例: Railway、メール送信サービス）に提供する場合があります。
                    法令に基づく開示要請がある場合を除き、ユーザーの同意なく第三者へ提供しません。
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-semibold">4</span>
                    保存期間
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    アカウントが有効な間はデータを保持し、ユーザーがアカウント削除を行った場合は関連データを即時削除、バックアップ等も 30 日以内に消去します。
                    端末内トークンはログアウトまたは削除時にクリアします。
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-semibold">5</span>
                    ユーザーの権利
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    開示・訂正・利用停止・削除等の要望や同意撤回は <a href="mailto:gyarmex@gmail.com" class="text-indigo-600 underline">gyarmex@gmail.com</a> までご連絡ください。
                    本人確認のうえ、適切な期間内に対応します。
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-semibold">6</span>
                    セキュリティ対策
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    通信は HTTPS で暗号化し、API トークンは Bearer 認証 + 安全なストレージに保存、パスワードはハッシュ化するなど適切な対策を講じています。
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-semibold">7</span>
                    未成年の利用
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    本サービスは 13 歳未満を対象としておらず、該当する個人情報を意図的に取得しません。誤って取得した場合は速やかに削除しますのでご連絡ください。
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-semibold">8</span>
                    Cookie / トラッキング
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    アプリ自体は Cookie を利用せず、サーバーは標準的なアクセスログのみ収集します。広告目的のトラッキング SDK は組み込んでいません。
                </p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-semibold">9</span>
                    改定
                </h2>
                <p class="text-slate-600 leading-relaxed">
                    運用変更や法令改正に応じて本ポリシーを更新する場合があります。重要な変更はアプリ内告知またはメールでお知らせし、更新後のサービス利用をもって同意したものとみなします。
                    ご不明点はいつでも <a href="mailto:gyarmex@gmail.com" class="text-indigo-600 underline">gyarmex@gmail.com</a> までお問い合わせください。
                </p>
            </section>
        </div>
    </div>
</body>

</html>
