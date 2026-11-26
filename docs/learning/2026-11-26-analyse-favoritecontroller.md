# 学習記録 2026-11-26：FavoriteController の分析（FavoriteService の準備）

- 出典：コードの読解（Claude Web 版と一緒に確認）。授業との対応は「次に確認すること」を参照
- 関連する CDA の能力：CP3（業務コンポーネント）、CP6（アーキテクチャ）
- 対象：`src/Controller/FavoriteController.php`（2026-11-26 時点の `dev`）

## 要点

FavoriteController には3つのメソッドがある。

| メソッド | 役割 |
|---|---|
| `index()` | 今ログインしているユーザー自身のお気に入りを一覧にする |
| `addFavorite()` | お気に入りに登録する |
| `removeFavorite()` | お気に入りを解除する |

メソッドの中の仕事を、2種類に分けた。

- **Controller の仕事**：画面や HTTP に関すること（リクエストの受け取り、CSRF の確認、画面の表示、ページの移動、ポップアップのメッセージ）
- **業務ルール（Service に移す仕事）**：画面がなくても成り立つ Washoku のルール

### addFavorite()

| 行 | していること | どちらの仕事？ |
|---|---|---|
| 47〜50 | すでにお気に入りか調べる | 業務ルール（二重登録を防ぐ） |
| 52〜55 | CSRF トークンを確かめる | Controller |
| 56 | フォームから人数を受け取る | Controller |
| 58〜63 | Favorite を作って保存する | 業務ルール |
| 66〜74 | 写真を探して、ポップアップの情報を用意する | Controller |
| 77 | レシピ一覧へ移動する | Controller |

### removeFavorite()

ユーザー自身の説明：「どのユーザーがどのレシピをお気に入りにしているかを確認し、登録されていたらトークンを確認して解除する」。

| 行 | していること | どちらの仕事？ |
|---|---|---|
| 92〜95 | このユーザーがこのレシピをお気に入りにしているか調べる | 業務ルール |
| 97〜100 | CSRF トークンを確かめる | Controller |
| 103〜104 | お気に入りを削除する | 業務ルール |
| 107 | レシピ一覧へ移動する | Controller |

### 2つのメソッドの共通点

どちらも「調べる → 保存または削除する」という同じ形をしている（add は「ないこと」、remove は「あること」を調べる）。

## 理解の段階

| 項目 | 授業で学んだ | 理論として理解 | まだ不明 | プロジェクトで使った（証拠） |
|---|---|---|---|---|
| Controller と業務ルールの区別 | ✓（アーキテクチャの授業） | ✓ | | 未実装 |
| SRP（責任を分ける） | ✓ | ✓ | | 未実装 |
| Symfony の Service と DI | | | 具体的な書き方 | 未実装 |

## Washoku Sensei との関係

- すでにあるもの：CSRF の確認（`addFavorite()` L.52、`removeFavorite()` L.97）、二重登録の確認（L.47）
- これから取り入れられそうなもの（提案）：業務ルールを `FavoriteService` に移す

```text
FavoriteService
├── add(ユーザー, レシピ, 人数)   … 二重登録を確かめて、人数を検証して、保存する
└── remove(ユーザー, レシピ)      … 登録されているか確かめて、削除する
```

Controller に残るのは、CSRF の確認、画面のメッセージ、ページの移動だけになる。

## FavoriteService を作るときに直すこと

1. **人数が検証されていない（L.56）**：`getInt('person_count', 1)` は整数に変えるだけなので、`-5` や `9999` も保存される。買い物リストの修正（#29）と同じように、1〜20 の範囲を確かめる。
2. **コメントの誤り（L.91）**：`removeFavorite()` のコメントが「二重登録を防ぐ」のままになっている（`addFavorite()` からのコピー）。正しくは「お気に入りに登録されているか確認する」。
3. **CSRF の確認とデータベースの検索の順番（L.47・L.92）**：先に CSRF を確かめれば、不正なリクエストのときにデータベースを調べずに済む。順番を入れ替えるか検討する。

## 次に確認すること

- 授業 `formation-cda/cours/25-symfony-avance/03-symfony-base-symfo-revision-concept-de-base/`（005 Services Symfony）：Service の作り方と依存性注入
- 授業 `formation-cda/cours/12-introduction-programmation/05-bases-de-la-conception-orientee-objet/`（002 SOLID）：SRP を自分の言葉で説明できるようにする
- Service の中で「すでにお気に入り」のときに、何を返すか、例外を投げるか（授業 `21-php-avance/02-...exceptions` で確認）
