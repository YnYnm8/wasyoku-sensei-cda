# veille 2026-11-25：composer audit が見つけた依存関係の脆弱性

- 見つけた方法：自動の veille（GitHub Actions の `composer audit`、`.github/workflows/security.yml`）
- 関連する CDA の能力：CP10（デプロイの準備）、CP11（DevOps）、各 CP の「veille の仕組み」
- 対応したブランチ：`chore/dependabot-composer-audit`

## かんたんに言うと

Washoku は、他の人が作った部品（ライブラリ）を組み合わせて動いている。部品は 6 月から交換していなかった。
CI に「部品の欠陥を調べる見張り役」を置いたら、その日のうちに 32 件の欠陥が見つかった。
部品を新しいものに交換して、0 件にした。途中で、Docker の設定の問題も見つかったので直した。

## 1. 何が見つかったか

`composer audit` の最初の実行（CI）：**15 パッケージ、32 件**のセキュリティ勧告。

主な例：

| パッケージ | 内容 |
|---|---|
| doctrine/mongodb-odm | 暗号化するフィールドが、条件によっては暗号化されずに保存される |
| symfony/config | XML の設定を読むときの XXE（ほかのファイルを読まれるおそれ） |
| symfony/expression-language | 深く入れ子になった式でアプリが落ちる |
| symfony/filesystem | パスの扱いの問題で、決められたフォルダの外にアクセスされるおそれ |
| symfony/framework-bundle | 信頼するプロキシの設定が、特殊な形の IP アドレスでは効かない |
| twig/twig | sandbox 機能の制限を回避できる問題など（2026-11-23・24 に公開） |

原因：`composer.lock` が古いままだった（Symfony 8.1.1、2026 年 6 月）。

## 2. 影響の判断

- 開発中で、まだ公開していないため、急いで対応しなければならない状況ではない。
- 多くは条件がそろったときだけ問題になる（外から受け取った XML を読む、Twig の sandbox を使う、など）。1 件ずつの詳しい確認はしていない。
- `composer.json` は `8.1.*` の範囲を指定しているので、同じ 8.1 の修正版に上げれば、大きな変更なしに直せると判断した。

## 3. 途中で起きた問題：Docker のビルドが失敗した

- 症状：`docker compose up -d --build` で `composer install` が失敗し、php コンテナが起動しない。
- 原因：`Dockerfile` が `FROM dunglas/frankenphp`（バージョン指定なし＝最新版）だった。最新版が PHP 8.6 になり、`laminas/laminas-code 4.17.0`（PHP 8.5 まで対応）と合わなかった。
- 解決：`FROM dunglas/frankenphp:1-php8.4` に固定した（`composer.json` の `"php": ">=8.4"` と同じ）。
- わかったこと：バージョンを固定しないと、同じ `Dockerfile` でもビルドした日によって結果が変わる。固定すると、Dependabot もこのイメージの更新を見張れる。

## 4. 対応

```bash
# 1 回目：Symfony と MongoDB のライブラリを更新（82 パッケージ）→ 9 件に減った
docker compose exec php composer update "symfony/*" doctrine/mongodb-odm --with-dependencies

# 2 回目：残りは twig/twig だけ（composer.json に直接書かれているので、名前の指定が必要だった）
docker compose exec php composer update twig/twig    # 3.28.0 → 3.31.1

# 確認
docker compose exec php composer audit               # No security vulnerability advisories found.
```

主な更新：symfony/* 8.1.1 → 8.1.12、doctrine/mongodb-odm 2.17.1 → 2.17.4、laminas/laminas-code 4.17.0 → 4.20.0、twig/twig 3.28.0 → 3.31.1。
composer の設定により、`composer.json` の下限のバージョンも自動で引き上げられた。

## 5. まだ残っていること

- [ ] ブラウザでの動作確認（トップ・検索、レシピの詳細、調味料、ログイン、お気に入り、買い物リスト）
- [ ] `composer.json`、`composer.lock`、`symfony.lock` をコミットしてプッシュし、CI が通ることを確認する
- [ ] GitHub の設定で Dependabot alerts と Dependabot security updates を有効にする

## 6. 気づいたこと

- テストがないので、部品を交換したあとの確認を手で行うしかない。テストがあれば、CI が自動で確認できる（1 月のテスト計画につなげる。OI-003）。
- 自動の見張り（CI）と、人の判断（影響の確認、更新、再確認）の両方があって、veille として機能する。
