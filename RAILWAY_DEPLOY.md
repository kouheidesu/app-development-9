# Railway デプロイ設定 - Blog Assistant App

## 📝 環境変数の設定

Railwayのダッシュボード > Variables タブで以下を設定してください：

```
APP_NAME=blog-assistant-app
APP_ENV=production
APP_KEY=base64:（以下のコマンドで生成）
APP_DEBUG=false
APP_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}
ASSET_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}
DB_CONNECTION=sqlite
LOG_CHANNEL=stack
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

## 🔑 APP_KEYの生成

ローカルで以下のコマンドを実行してAPP_KEYを生成：

```bash
cd /Users/kajiharakouhei/program/blog-assistant-app
php artisan key:generate --show
```

出力された値（例: `base64:xxxxx...`）をRailwayの`APP_KEY`に設定してください。

## ⚠️ 重要なポイント

1. **環境変数の形式**
   - `APP_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}`（二重波括弧 `${{}}`）
   - `ASSET_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}`（二重波括弧 `${{}}`）

2. **デプロイ後の確認**
   - デプロイログで`npm run build`が成功しているか確認
   - `public/build/manifest.json`が生成されているか確認
   - ブラウザで強制リロード（Cmd+Shift+R / Ctrl+Shift+R）

3. **CSSが表示されない場合**
   - Railwayの環境変数が正しいか再確認
   - ブラウザのキャッシュをクリア
   - 開発者ツールのNetworkタブで`/build/assets/*.css`が200 OKか確認

## 🚀 デプロイ手順

1. GitHubにリポジトリを作成
2. コードをプッシュ
3. Railway.appで「New Project」→「Deploy from GitHub repo」
4. 上記の環境変数を設定
5. 自動デプロイ完了を待つ

## 📊 アプリの機能

- ブログ記事の作成・管理
- ステータス管理（下書き、執筆中、準備完了、公開済み）
- カテゴリ分類
- タグ付け
- メモ機能
- 文字数自動カウント
