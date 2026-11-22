# DWWM 版から CDA 版への作り替え

最終更新：2026-11-22

| 項目 | DWWM 版 | CDA 版 | 状態 | 関連 |
|---|---|---|---|---|
| データベース | MySQL のみ | MySQL ＋ MongoDB（閲覧履歴） | 一部完了（#5〜#8） | [001](decisions/001-mongodb-pour-historique.md) |
| API | なし（Twig で画面を生成） | `/api/history`（POST・GET） | 未着手（#9、#11） | |
| フロントエンド | Twig ＋ Stimulus | 履歴の表示に React コンポーネント | 未着手（#10） | |
| テスト | PHPUnit のテストなし | TDD で API を作る | 未着手 | |
| アーキテクチャ | Twig で画面を生成する Symfony アプリ | 授業で学習中。学んだ内容をもとに決める | 検討中 | |
| デプロイ | Docker Compose、README の手順 | 授業で学習中。学んだ内容をもとに決める | 検討中 | |
