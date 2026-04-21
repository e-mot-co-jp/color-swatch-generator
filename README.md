# Color Swatch Generator

WordPress プラグイン：カラースウォッチGIF画像生成ツール

## 概要

Color Swatch Generator は、WooCommerce やバリエーション商品向けのカラースウォッチ（色見本）を簡単に生成・管理できる WordPress プラグインです。複数色の縦分割GIF画像を250×250px のサイズで生成し、メディアライブラリに自動登録できます。

## 機能

- **複数色対応** - 1色、2色、3色の縦分割GIF生成
- **カラーピッカー** - WordPress 標準のカラーピッカーを統合
- **Hex入力** - 16進数カラーコード（#RRGGBB）での直接入力
- **カラー名検索** - 英語、カタカナ、全角半角対応の色名検索
- **リアルタイムプレビュー** - 生成前にプレビュー表示
- **メディアライブラリ統合** - 生成後の自動メディアライブラリ登録
- **標準サイズ** - 250×250px の最適化されたサイズ

## インストール

1. プラグインフォルダに `color-swatch-generator` ディレクトリをコピー
2. WordPress管理画面 → プラグイン
3. 「Color Swatch Generator」を有効化

## 使用方法

### 管理画面

1. **ダッシュボード** → **Color Swatches** にアクセス
2. **Number of Colors** で色数を選択（1、2、または3色）
3. 色を入力：
   - **カラーピッカー** をクリックして色選択
   - または **Hex コード** (#FF0000) を直接入力
4. **Color Name Search** で色名を検索：
   - 英語: `Red`, `Blue`, `Green`
   - カタカナ: `レッド`, `ブルー`, `グリーン`
   - 全角・半角対応
5. **Preview** でプレビュー確認
6. **Generate & Upload Swatch** をクリック
7. 生成されたGIF画像がメディアライブラリに登録されます

### 色名検索対応色

デフォルトで以下のカラーに対応：

- **赤系**: Red, Pink, Crimson, Salmon など
- **橙系**: Orange, Coral, Tomato など
- **黄系**: Yellow, Gold, Goldenrod など
- **緑系**: Green, Lime, Forest Green など
- **青系**: Blue, Navy, Sky Blue, Cyan など
- **紫系**: Purple, Violet, Magenta など
- **茶系**: Brown, Chocolate, Peru など
- **グレー**: Gray, Silver, White, Black など

## 技術仕様

### ファイル構成

```
color-swatch-generator/
├── color-swatch-generator.php       # メインプラグインファイル
├── includes/
│   ├── class-gif-generator.php      # GIF生成ロジック
│   ├── class-color-database.php     # カラーデータベース
│   ├── class-admin-page.php         # 管理画面UI
│   └── class-ajax-handler.php       # AJAXリクエスト処理
├── assets/
│   ├── js/
│   │   └── color-swatch-generator.js # フロントエンド JS
│   └── css/
│       └── color-swatch-generator.css # スタイルシート
└── README.md                         # このファイル
```

### 必要な WordPress 機能

- WP_Ajax（AJAXハンドラー）
- Media Library（メディア機能）
- GD Library（画像生成）
- Color Picker（カラーピッカーUI）

### GIF生成方式

- **ライブラリ**: PHP GD Library
- **サイズ**: 250×250ピクセル（固定）
- **フォーマット**: GIF（非アニメーション）
- **色分割**: 複数色を縦方向で等分割

## カスタマイズ

### 新しいカラーを追加

`includes/class-color-database.php` の `get_colors()` メソッドを編集：

```php
array(
    'hex' => '#XXXXXX',
    'names' => array( 'English Name', 'カタカナ名', 'lowercase name' )
)
```

### フィルタフック

```php
// カラーリストをカスタマイズ
add_filter( 'csg_colors', function( $colors ) {
    // カラーを追加・削除
    return $colors;
} );
```

### セキュリティ機能

- **Nonce検証** - AJAX リクエストの正当性確認
- **権限チェック** - `upload_files` 権限が必要
- **入力サニタイゼーション** - すべてのユーザー入力を検証
- **ファイル検証** - 生成されたGIFファイルの形式確認

## トラブルシューティング

### GIF生成に失敗する

1. GD Library が有効か確認（phpinfo.php で確認）
2. uploads ディレクトリの書き込み権限を確認
3. PHP メモリ制限を確認（最小 32MB 推奨）

### メディアライブラリに登録されない

1. メディアのアップロード権限を確認
2. WordPress ログを確認（/wp-content/debug.log）
3. プラグインとテーマの競合を確認

### カラーピッカーが表示されない

1. jQuery が読み込まれているか確認
2. ブラウザキャッシュをクリア
3. ブラウザコンソールでエラーを確認

## 対応環境

- **WordPress**: 5.0 以上
- **PHP**: 7.4 以上
- **ブラウザ**: 最新の主要ブラウザ（Chrome, Firefox, Safari, Edge）
- **GD Library**: 必須

## ライセンス

GPL v2 or later

## サポート

バグ報告やサポートリクエストは、プラグイン管理画面から報告してください。

## 変更履歴

### Version 1.0.0
- 初回リリース
- GIF生成機能
- カラーピッカー統合
- カラー名検索機能
- メディアライブラリ統合
