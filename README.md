<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

# 1. アプリケーションの概要

各々が自由にグループを作り、所属し、その中でプロフィールを共有する。（レスポンシブ対応）


# 2. アプリケーション主要機能一覧

◯会員登録、ログイン、ログアウト  
◯プロフィールの作成  
◯ログイン、プロフィール作成状態に応じた◯ホームページの切り替え  
◯グループの作成、参加、退出、検索  
◯グループ作成者のみができる、グループの削除、強制退出処理  
◯モデルにて6桁のグループのパスワードと12桁の画像IDの自動生成  
◯グループのパスワード確認、パスワードのコピー  
◯グループサムネイル、プロフィールサムネイルの画像アップロード（S3へ保存）  
◯グループ、プロフィールの編集処理  
◯モーダルウインドウ  
◯処理中のロード表示  
◯処理後のフラッシュメッセージ  
◯各マイページにコメント投稿、いいね機能  
◯エラーの際、ステータスコードに応じたエラーページに切り替える  


# 3. 使用技術一覧

**◯インフラ**

開発環境
Docker /docker-compose
データベース Postgres

本番環境
AWS(ECS, EC2, RDS for postgres, VPC,S3, ALB,)

**◯使用言語**
PHP,JavaScript, Sass

**◯フレームワーク**
Laravel, Vue.js

**◯ライブラリ**
Vuex（状態管理）  
micromodal（モーダル）  
vue-click-outside（要素以外のクリック時にイベント発火）  
vue-clipboard2（クリップボードへデータを保存）  

**◯プラグイン**
VueRouter（ルーティングの制御）

**◯デプロイ方法**
イメージをECRへプッシュし、プッシュしたイメージURIをECSのコンテナのイメージとして登録する

**◯その他の技術**
・セッション管理は、AWS ALBによるトラフィックの負荷分散とスティッキーセッションによるユーザーごとにサーバーを固定することで実現  
・Vue.js と Laravel を組み合わせたSPAの構築  
・SPA におけるクッキー認証と CSRF 対策  
・Vue Router を使用した画面遷移  
・Vuex を使用した状態管理  
・Vue でのタブやローディング UI の表現  
・SPA におけるエラー処理  
・ミドルウェアによるページ認証  
・PolicyとGateの使用  
・LaravelからS3へファイルの保存  
・レスポンシブデザイン  

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[British Software Development](https://www.britishsoftware.co)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- [UserInsights](https://userinsights.com)
- [Fragrantica](https://www.fragrantica.com)
- [SOFTonSOFA](https://softonsofa.com/)
- [User10](https://user10.com)
- [Soumettre.fr](https://soumettre.fr/)
- [CodeBrisk](https://codebrisk.com)
- [1Forge](https://1forge.com)
- [TECPRESSO](https://tecpresso.co.jp/)
- [Runtime Converter](http://runtimeconverter.com/)
- [WebL'Agence](https://weblagence.com/)
- [Invoice Ninja](https://www.invoiceninja.com)
- [iMi digital](https://www.imi-digital.de/)
- [Earthlink](https://www.earthlink.ro/)
- [Steadfast Collective](https://steadfastcollective.com/)
- [We Are The Robots Inc.](https://watr.mx/)
- [Understand.io](https://www.understand.io/)
- [Abdel Elrafa](https://abdelelrafa.com)
- [Hyper Host](https://hyper.host)
- [Appoly](https://www.appoly.co.uk)
- [OP.GG](https://op.gg)
- [云软科技](http://www.yunruan.ltd/)

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
