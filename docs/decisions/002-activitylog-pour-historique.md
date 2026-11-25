# 002. 閲覧履歴をアクティビティ履歴（ActivityLog）に置き換える

- 日付：2026-11-25
- 状態：採用（001 を置き換える）
- 判断した人：AI の提案をユーザーが採用（Claude Web 版。A／B の2案からユーザーが B を選んだ）
- DWWM 版との関係：新規（CDA 版の Épic 9 の設計変更）
- 関連する授業：アーキテクチャ（層構造、SRP、イベント駆動、DI、SOLID）。原本：`formation-cda/cours/13-architecture-applicative/02-conception-et-optimisation/`。学習記録は `docs/learning/` に追加したらリンクする

## 背景

001 では、閲覧履歴を MongoDB の `viewHistory` コレクションに保存すると決めた（`src/Document/ViewHistory.php`：userId、recipeId、viewedAt）。

その後、CDA の授業でイベント駆動のアーキテクチャを学び、閲覧とお気に入り登録を1つの「アクティビティ履歴」として記録する設計を考えた。`ViewHistory` はまだどこからも使われていないので、今なら既存の機能に影響なく変更できる。

## 検討した選択肢

| 選択肢 | 長所 | 短所 |
|---|---|---|
| A：`ViewHistory` のまま（閲覧だけを記録） | 作業が少ない。すでにクラスがある | 項目が決まっているので、「MySQL のテーブルではなく MongoDB を選んだ理由」を説明しにくい。イベント駆動との関係が弱い |
| B：`ActivityLog` に置き換える（閲覧とお気に入り登録を記録） | イベントの種類によって `details` の中身が変わるので、ドキュメント型を選ぶ理由がはっきりする。新しいイベントを、既存のコードを変えずに追加できる（OCP） | クラスの変更とイベントの追加で、作業が少し増える |

## 決定

B を2段階で行う。

1. **第1段階**：`ViewHistory` を `ActivityLog`（type、userId、occurredAt、details）に置き換え、閲覧（`recipe_viewed`）だけを記録する。`GET /api/history` は `type = recipe_viewed` のものだけを返すので、US 9.1・9.2（#2、#3）はそのまま実現できる。
2. **第2段階**：FavoriteController の業務ロジックを FavoriteService に分けたあとで、お気に入り登録（`favorite_created`）のイベントを追加する。

保存するデータの例：

```json
{ "type": "recipe_viewed",    "userId": 3, "occurredAt": "...", "details": { "recipeId": 12 } }
{ "type": "favorite_created", "userId": 3, "occurredAt": "...", "details": { "recipeId": 12, "source": "recipe_page" } }
```

予定している流れ：`RecipeViewedEvent` / `FavoriteCreatedEvent` → `ActivityLogListener` → `ActivityLogService` → MongoDB。Listener で例外が起きても、メインの処理（閲覧、お気に入り登録）は止めない。

## 理由

ユーザーは Claude の提案（B を2段階で進める）を採用した。提案で挙げた理由：

- イベントごとに `details` の構造が違うため、MongoDB（ドキュメント型）を選んだ理由を説明できる。001 の理由（書き込みが速い）だけでは弱い。
- 2つのイベントが同じ Listener に流れる構成は、アーキテクチャ（CP6）と SOLID（SRP、OCP、DIP）の説明になる。
- 「最初の判断を、授業で学んだことをもとに見直した」こと自体を、設計判断の例として説明できる。

## 影響

- `src/Document/ViewHistory.php` を `ActivityLog` に置き換える（第1段階の作業。この記録ではコードを変更しない）。
- #9（POST /api/history）と #11（GET /api/history）は、`ActivityLog` を前提に進める。
- userId と recipeId は整数で持つので、MySQL の User や Recipe との関係はデータベースでは保証されない（001 と同じ）。
- Symfony Messenger による非同期処理は行わない。同期処理の限界は、今後の改善点として説明する。

## CDA の能力との対応

- CP3 業務コンポーネントの開発（FavoriteService、SOLID）
- CP6 ソフトウェアアーキテクチャの定義（イベント駆動、層の役割）
- CP8 SQL／NoSQL のデータアクセスコンポーネント（MySQL と MongoDB の使い分け）
