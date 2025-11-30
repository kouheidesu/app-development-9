<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>新規登録 - Blog Assistant</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        input {
            font-size: 18px !important;
            min-height: 48px !important;
        }

        button[type="submit"] {
            min-height: 56px !important;
            font-size: 18px !important;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-lg border border-slate-200 p-6 sm:p-8">
            <!-- ヘッダー -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">
                    ✍️ Blog Assistant
                </h1>
                <p class="text-slate-600">新規アカウント作成</p>
            </div>

            <!-- エラーメッセージ -->
            @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-red-800 font-medium">入力内容を確認してください</p>
                    </div>
                    <ul class="list-disc list-inside text-sm text-red-700 ml-7">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- 登録フォーム -->
            <form action="{{ route('register') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-2">名前</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-2">メールアドレス</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-2">パスワード（8文字以上）</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-2">パスワード（確認）</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold py-4 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all shadow-md">
                    アカウント作成
                </button>
            </form>

            <!-- ログインリンク -->
            <div class="mt-6 text-center">
                <p class="text-sm text-slate-600">
                    すでにアカウントをお持ちの方は
                    <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-700">
                        ログイン
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>
