# 未解決の問題

| ID | 内容 | 見つけた日 | 影響範囲 | 状態 | メモ |
|---|---|---|---|---|---|
| OI-001 | README のクローン手順が旧リポジトリ（`wasyoku-sensei`）を指している | 2026-11-22 | 文書 | 未対応 | `wasyoku-sensei-cda` に直す |
| OI-002 | README は本番で `compose.yaml` だけを使うとしているが、`compose.yaml` に phpMyAdmin（ポート 8081、パスワード固定）が入っている | 2026-11-22 | セキュリティ・デプロイ | 未対応 | 開発用なら `compose.override.yaml` に移すか検討 |
| OI-003 | PHPUnit のテストがまだない（`tests/bootstrap.php` のみ） | 2026-11-22 | テスト（CP9） | 未対応 | #9・#11 の TDD から始める予定 |

## 解決済み

（なし）
