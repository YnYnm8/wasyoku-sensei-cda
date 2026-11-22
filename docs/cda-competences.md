# CDA の能力と証拠

最終更新：2026-11-22
能力の名前と番号は、2023 年版 CDA（RNCP37873）をもとにした日本語訳。ENLEARN の資料と照らし合わせて確認する。〈要確認〉

## 1. 安全なアプリケーションを開発する

| 能力 | 証拠 | 状態 |
|---|---|---|
| CP1 開発環境の導入と設定 | `compose.yaml`、`compose.override.yaml`、`Dockerfile`、MongoDB の追加（#5） | 証拠あり |
| CP2 ユーザーインターフェースの開発 | `templates/`（Tailwind、RGAA 対応のコミット）／React コンポーネント（#10）は未着手 | 一部 |
| CP3 業務コンポーネントの開発 | `src/Controller/`、`src/Entity/`、買い物リスト・お気に入り | 証拠あり（DWWM 版） |
| CP4 プロジェクト管理への参加 | GitHub Issues（Épic・US・技術タスク #1〜#11）、Issue ごとのブランチと PR | 証拠あり |

## 2. レイヤー構造の安全なアプリケーションを設計・開発する

| 能力 | 証拠 | 状態 |
|---|---|---|
| CP5 要件分析とモックアップ | US 9.1・9.2 の受け入れ条件（#2、#3）／モックアップは〈要確認〉 | 一部 |
| CP6 ソフトウェアアーキテクチャの定義 | — | 未着手 |
| CP7 リレーショナル DB の設計と構築 | `src/Entity/`、`migrations/` | 証拠あり（DWWM 版） |
| CP8 SQL／NoSQL のデータアクセスコンポーネント | `src/Repository/`（SQL）、`src/Document/ViewHistory.php`（NoSQL） | 一部 |

## 3. 安全なアプリケーションのデプロイを準備する

| 能力 | 証拠 | 状態 |
|---|---|---|
| CP9 テスト計画の準備と実施 | — | 未着手 |
| CP10 デプロイの準備と文書化 | `README.md` のデプロイ手順、`scripts/backup-db.sh`・`restore-db.sh` | 一部 |
| CP11 DevOps によるリリースへの参加 | — | 未着手 |
