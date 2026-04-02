<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function destroyConfirm(Request $request)
    {
        $user = $request->user();

        return view('account.delete', [
            'articleCount' => $user?->articles()->count() ?? 0,
            'userName' => $user?->name,
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate(
            [
                'ack_articles' => ['accepted'],
                'ack_irreversible' => ['accepted'],
                'ack_support' => ['accepted'],
                'phrase' => ['required', 'regex:/^(削除します|delete)$/i'],
            ],
            [
                'ack_articles.accepted' => '記事とカテゴリが削除されることへの同意が必要です。',
                'ack_irreversible.accepted' => '削除は取り消せないことへの同意が必要です。',
                'ack_support.accepted' => 'サポートへの相談が可能であることを確認してください。',
                'phrase.required' => '確認用のフレーズを入力してください。',
                'phrase.regex' => '確認用のフレーズは「削除します」または「delete」のみ利用できます。',
            ]
        );

        $user = $request->user();

        if (!$user) {
            return redirect()
                ->route('login')
                ->with('error', 'ユーザー情報を取得できませんでした。');
        }

        DB::transaction(function () use ($user) {
            $user->articles()->delete();
            $user->delete();
        });

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('register')
            ->with('success', 'アカウントを削除しました。ご利用ありがとうございました。');
    }
}
