<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

# 0. アプリケーション名  
自己紹介アプリ

# 1. アプリケーションの概要

各々が自由にグループを作り、所属し、その中でプロフィールを共有する。（レスポンシブ対応）

# 2. アプリケーションの制作背景 

**問題点の発見**
  
僕は尾崎豊が好きで、それが高じて尾崎豊に関するイベントのようなものを開催するようになりました。  
インスタグラム（https://instagram.com/ozamiyo2018?igshid=1q30651t32rza）  

イベントを運営していく上で、ある問題点に気が付きました。  
それは、**参加者どうしがイベント当日まで相手の情報をほとんど何も知らない**ということです。これは様々なデメリットと紐付いています。  
  
例えば、  
・イベント参加の心理的ハードルが高くなる（特に初参加者は、顔や名前をほとんど知らない人たちの中に一人で参加しなければならず、参加するだけで一定の勇気やストレスがかかることが想像される）  
・当日、会話が進みにくい（イベント自体の盛り上がりが減少する）  
・既存の参加者は初参加者の詳細を知らないためサポートしずらい（初参加者が孤立する可能性がある）  
これが重なると、イベントの質は下降線をたどることが予想されます。  
  
**解決案**  
  
そこで、もし自由にグループを作成でき、その中でお互いのプロフィールを自由に共有することができるアプリがあればこの問題は一気に解決するのではないかとふと考えました。    
あらかじめ参加者にプロフィールを作成しておいてもらい、作っておいたイベントグループに所属してもらうことで、初参加者は事前に相手の顔や趣味などのプロフィールを詳細に確認でき、コメントやいいねなどを通して事前に繋がり合うこともできます。    
そうすることで、**初めての方でもイベント参加の心理的ハードルは低いものになり、相手のことをある程度知っているので会話も円滑に進み、既存の参加者が積極的に初参加者のカバーもでき、結果総じてイベント自体の質が自然と上がります。**   
  
**まとめ**
このような問題を解決するにあたって「自己紹介アプリ」の発見と作成をすることに至りました。    
  
**補足 LINEなどで一人ひとり自己紹介するのではダメなのか？**  
・イベントごとに一人ひとりが毎回自己紹介し合うのは、面倒だしやらない人も多数でてくる。その点自己紹介アプリは、一回プロフィールを作成しておきさえすればグループに参加するだけで自己紹介したことになる。また複数グループに参加する場合でも１つのプロフィールを使い回すことができる。      
・恐らく一言のみのため、詳細にプロフィールを確認することはできず、上に書いたデメリットは解決できない。  
・参加者が50人を超えるLINEグループでで一人ひとりが自己紹介するとトーク画面が忙しくなる。  
  
  
# 2. アプリケーション主要機能一覧

◯会員登録、ログイン、ログアウト  
◯プロフィールの作成  
◯ログイン、プロフィール作成状態に応じたホームページの切り替え  
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

**開発環境**
Docker /docker-compose  
データベース Postgres  

**本番環境**  
AWS(ECS, EC2, RDS for postgres, VPC,S3, ALB,)   
  
**アーキテクチャ図**    
![AWS アーキテクチャ図](https://introductionapp.s3-ap-northeast-1.amazonaws.com/vue/intro-app-vue_AWS_Architecture.jpg)     
    
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
・セッション管理は、AWS ALBによるトラフィックの負荷分散とスティッキーセッションによるユーザーごとにサーバーを固定する
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

# 4. アプリの使い方（一部分のみ抜粋）  

①まずログインする（すでにアカウントを作成済み）  
メール: engineer@email.com,  パスワード: test1234  
②グループ一覧画面に遷移し、すでに所属しているグループ（あらかじめ用意）が一覧で表示される。  
③グループをタップすると、グループに所属しているユーザーのプロフィール（自分も含めた）が一覧として表示される。   
④自分のプロフィールの編集上にある編集ボタンを押すことで編集できる（自由に変更可）。  
⑤ドロップダウンをタップし、自分のユーザー名をタップすると、マイページに遷移する。マイページには、他のユーザーから寄せられたコメント一覧といいねが表示される（自由にコメントやいいね可）。  
⑥グループの中のプロフィール一覧で、他ユーザーのプロフィールをタップすると、マイページと同じように、そのユーザーのプロフィール詳細が表示され、寄せられたコメントやいいねが一覧で表示される（自由にコメントやいいね可）。  
  
⑦会員登録し新たなユーザーを作成した場合、自由にアプリを操作可能です。（プロフィールの作成、グループの作成、グループの編集、グループへの参加、グループの退会、など）  
  
・会員登録する際は以下の形で登録できます。    
メール:　メールの形式になっていれば何でもよい。（例　hello@email.com）   
パスワード: 8文字以上であれば何でもよい。  
  
・グループへ参加するときは、以下から試せます（あらかじめテストグループを用意しておきました）    
グループ名　テストグループ  
パスワード　CfRsha  

# 5. 補足  
ユーザー名とプロフィール名を分けたのは、このアプリは最終的に一人のユーザーが多数のプロフィールを所有することができ、グループに応じてそのプロフィールを使い分けることができる仕様にしたいためです。

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
