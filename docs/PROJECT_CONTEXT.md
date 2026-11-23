# Washoku Sensei — プロジェクト引き継ぎ資料

最終更新：2026-11-22

> このファイルだけを読めば、AI でも人でも作業を引き継げるようにまとめた資料です。
> 作業を始める前に、このファイル、`docs/open-issues.md`、最新の `docs/devlog/` を読んでください。

## 1. 概要

- フランスで手に入る食材を使って和食を作れるようにする Web アプリ。
- DWWM の課題として作ったアプリを、**CDA（Concepteur Développeur d'Applications, RNCP37873）の課題として作り替えている**。
- リポジトリ：`YnYnm8/wasyoku-sensei-cda`（作業ブランチ `dev`、Issue ごとにブランチを作り、Pull Request で `dev` にマージする）
- 開発者：Meiko（CDA 版は1人で開発）
- CDA の授業資料（原本）：非公開リポジトリ `YnYnm8/formation-cda` の `cours/`（目次は README.md）。このリポジトリには授業資料を入れない。

## 2. 主な機能と進み具合

| 機能 | 状態 | 主な関連ファイル |
|---|---|---|
| 食材からのレシピ検索（ホームの検索フォーム、クイックフィルター） | 実装済み（DWWM 版） | `src/Controller/HomeController.php`、`templates/home/` |
| レシピの一覧・詳細（写真・手順） | 実装済み（DWWM 版） | `src/Controller/RecipeController.php`、`templates/recipe/` |
| 調味料ガイドと代用品 | 実装済み（DWWM 版） | `src/Controller/CondimentController.php`、`src/Entity/CondimentSubstitute*.php` |
| アジア食材店の一覧 | 実装済み（DWWM 版） | `src/Controller/AsianShopController.php` |
| 会員登録・ログイン | 実装済み（DWWM 版） | `src/Controller/RegistrationController.php`、`src/Security/LoginFormAuthenticator.php` |
| お気に入り（ROLE_USER） | 実装済み（DWWM 版） | `src/Controller/FavoriteController.php` |
| 買い物リストの作成とメール送信（Symfony Mailer / Gmail） | 実装済み（DWWM 版） | `src/Controller/ShoppingListController.php` |
| 管理画面（レシピ・調味料、ROLE_ADMIN） | 実装済み（DWWM 版） | `config/packages/security.yaml` の access_control |
| **閲覧履歴（Épic 9、MongoDB）** | **CDA 版で実装中** | 下記参照 |

### 閲覧履歴（Épic 9）の進み具合

| Issue | 内容 | 状態 |
|---|---|---|
| #5 | MongoDB サービスを docker compose に追加 | 完了 |
| #6 | MongoDB の基本コマンド（mongosh）の練習 | 完了 |
| #7 | doctrine/mongodb-odm-bundle の導入 | 完了 |
| #8 | Document クラス `ViewHistory` の作成 | 完了（`src/Document/ViewHistory.php`） |
| #9 | Endpoint POST /api/history（TDD） | 未着手 |
| #11 | Endpoint GET /api/history（TDD） | 未着手 |
| #10 | 履歴を表示する React コンポーネント | 未着手 |

ユーザーストーリー：#2（US 9.1 履歴の記録）、#3（US 9.2 履歴の表示：新しい順、写真・名前・日時、詳細へのリンク、最新 20 件）

## 3. 技術スタック

| 分類 | 内容 |
|---|---|
| 言語・フレームワーク | PHP >= 8.4、Symfony 8.1 |
| ORM / ODM | Doctrine ORM 3.6（MySQL）、Doctrine MongoDB ODM bundle 5.6（MongoDB） |
| データベース | MySQL 8.0、MongoDB 7 |
| フロントエンド | Twig、Tailwind CSS（symfonycasts/tailwind-bundle）、AssetMapper、Stimulus |
| メール | Symfony Mailer（google-mailer） |
| テスト | PHPUnit 13（テストはまだ `tests/bootstrap.php` のみ） |
| インフラ | Docker / Docker Compose、FrankenPHP（`Dockerfile`）、phpMyAdmin |

## 4. 環境構築と起動方法

```bash
git clone https://github.com/YnYnm8/wasyoku-sensei-cda.git
cd wasyoku-sensei-cda
docker compose up -d --build          # 開発時は compose.override.yaml も読み込まれる（Mailpit など）
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec php php bin/console doctrine:fixtures:load       # テストデータ（任意）
```

- アプリ：http://localhost:8080
- phpMyAdmin：http://localhost:8081
- 本番用の手順、DB のバックアップ（`scripts/backup-db.sh`）と復元（`scripts/restore-db.sh`）は `README.md` を参照。
- 秘密情報は `.env.local`（Git に入れない）に書く。

## 5. ディレクトリ構成とアーキテクチャ

```
src/
├── Controller/    # 画面ごとのコントローラー（Home, Recipe, Condiment, AsianShop, Favorite, ShoppingList, Security, Registration）
├── Entity/        # Doctrine ORM エンティティ（MySQL）
├── Repository/    # ORM のリポジトリ
├── Document/      # Doctrine ODM ドキュメント（MongoDB）— ViewHistory
├── Form/          # フォーム（RecipeType, CondimentType, RegistrationFormType）
├── Enum/          # RecipeMainCategory, RecipeLevel, RecipeSeason, RoleEnum
├── Security/      # LoginFormAuthenticator
└── DataFixtures/  # テストデータ
templates/         # Twig テンプレート
migrations/        # Doctrine マイグレーション
scripts/           # DB バックアップ・復元
```

主なエンティティ：User、Recipe、Ingredient、RecipeIngredient、Condiment、RecipeCondiment、CondimentSubstitute、CondimentSubstituteGroup、Media、Favorite、ShoppingList、RecipeShoppingList、ShoppingListSubstitute、AsianShop

データの使い分け：アプリの主なデータは MySQL、閲覧履歴は MongoDB（理由は `docs/decisions/001-*.md`）。

## 6. コーディング規約

- コミットメッセージ：`feat:` / `fix:` / `style:` / `perf:` / `chore:` / `docs:` のあとに内容（主にフランス語）、関連 Issue 番号を付ける（例：`feat: création de la classe Document ViewHistory (#8)`）
- ブランチ：Issue 番号から始まる名前（例：`8-créer-la-classe-document-viewhistory`）
- コード内のコメント：英語

## 7. 主な設計判断

| 番号 | 内容 | 状態 |
|---|---|---|
| [001](decisions/001-mongodb-pour-historique.md) | 閲覧履歴を MongoDB に保存する | 採用（授業の進み方によって見直す可能性あり） |

## 8. DWWM 版からの変更

`docs/dwwm-to-cda.md` を参照。

## 9. 未解決の問題

`docs/open-issues.md` を参照。

## 10. 現在の状態と次のステップ

- 閲覧履歴の土台（MongoDB、ODM、ViewHistory）ができた。
- 次は #9 POST /api/history を TDD で作る。

## 11. AI への作業依頼の仕方

1. まずこのファイル、`docs/open-issues.md`、最新の `docs/devlog/` を読む。
   授業の内容を確かめるときは、`formation-cda` の該当する章を読む（アクセスできる場合）。
2. 記録は `.claude/skills/washoku-sensei-devlog/SKILL.md` のルールに従う（承認なしに書かない、理解の段階や判断の出どころを区別する）。
3. `.env.local` などの秘密情報は共有しない・記録しない。
