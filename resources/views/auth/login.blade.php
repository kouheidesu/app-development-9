<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ログイン - Blog Assistant</title>

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
                <p class="text-slate-600">ログインしてください</p>
            </div>

            <!-- エラーメッセージ -->
            @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-red-800 font-medium">{{ $errors->first() }}</p>
                </div>
            </div>
            @endif

            <!-- 成功メッセージ -->
            @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <p class="text-sm text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            <!-- ログインフォーム -->
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-2">メールアドレス</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-2">パスワード</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold py-4 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all shadow-md">
                    ログイン
                </button>
            </form>

            <!-- 登録リンク -->
            <div class="mt-6 text-center">
                <p class="text-sm text-slate-600">
                    アカウントをお持ちでない方は
                    <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-700">
                        新規登録
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>
