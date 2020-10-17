<?php

namespace App\Http\Controllers;

//モデル
use App\User;
use App\Profile;
use App\Group;
use App\Photo;
use App\Comment;
//リクエストクラス
use Illuminate\Http\Request;
use App\Http\Requests\CreateProfile;
use App\Http\Requests\EditProfile;
use App\Http\Requests\StoreComment;
//ファイル処理
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
//Authクラス
use Illuminate\Support\Facades\Auth;


class ProfileController extends Controller
{

    //プロフィールの作成処理
    public function create(CreateProfile $request) {
        // レコードを生成しデータをインサートしていく
        $my_profile = new Profile();
        $my_profile->name = $request->name;
        $my_profile->introduction = $request->introduction;
        $user = Auth::user();
        //保存
        $user->profiles()->save($my_profile);
        return response($user, 201);
    }

    //マイプロフィール詳細表示
    public function showMyProfile(User $user) {
      // 自分のプロフィール情報とそれに紐づく各データも一緒に取得する
      $myProfile = Profile::where('user_id', $user->id)->with(['photos', 'comments', 'comments.author', 'comments.likes'])->first();
      return $myProfile;
    }

    //プロフィール編集処理(マイページ）)
    public function editMyProfile(User $user, EditProfile $request) {

      $profile = $user->profiles()->first();

      //画像が送られてきた場合のみ画像の処理をする
      if($request->photo) {

        // 投稿写真の拡張子を取得する
        $extension = $request->photo->extension();

        $profile_photo = new Photo();

        // インスタンス生成時に割り振られたランダムなID値と
        // 本来の拡張子を組み合わせてファイル名とする
        $profile_photo->filename = $profile_photo->random_id . '.' . $extension;

        // S3にファイルを保存する
        // 第三引数の'public'はファイルを公開状態で保存するため
        $profile_photo->filename = Storage::cloud()->putFileAs('vue', $request->photo, $profile_photo->filename, 'public');

        // データベースエラー時にファイル削除を行うため
        // トランザクションを利用する
        DB::beginTransaction();
        try {
            $profile->photos()->delete();
            $profile->photos()->save($profile_photo);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            // DBとの不整合を避けるためアップロードしたファイルを削除
            Storage::cloud()->delete($profile_photo->filename);
            throw $exception;
        }
      }

      //名前と自己紹介の編集処理
      if($user->id === $profile->user_id){
        $profile->name = $request->textName;
        $profile->introduction = $request->textIntroduction;
        $profile->save();
      }

      return response([201]);
    }

    //コメントデータ保存（マイページ）
    public function addMyComment(User $user, StoreComment $request) {

        // コメントしたプロフィールを取得
        $activeProfile = $user->profiles()->first();
        // コメントを受けた側のプロフィールをわかりやすいように変換
        $passiveProfile = $activeProfile;

        $comment = new Comment();
        $comment->content = $request->get('content');
        //コメントテーブルに、コメントしたがわの情報をセット
        $comment->active_profile_id = $activeProfile->id;
        // 親から子へのリレーションの保存の仕方でなければならない
        // 次はコメントを受けた側のプロフィールをさらに紐付けてそのコメントを保存
        $passiveProfile->comments()->save($comment);

        //コメントしたプロフィールに紐づくコメントデータをその各ユーザーもまとめて取得する
        $new_comments = Comment::where('passive_profile_id', $passiveProfile->id)->with(['author', 'likes'])->get();
        return $new_comments;
    }

    //likesテーブルにいいね情報(いいね押した側と押された側の情報)を保存（マイページ）
    public function myLike(User $user, Comment $comment) {
      //まずいいねを押したプロフィールのオブジェクトを取得
      $profile = $user->profiles()->first();
      //いいね押されたコメントをそのコメントのいいね情報も同時に引っ張ってくる
      $comment = Comment::where('id', $comment->id)->with('likes')->first();

      if(!$comment) {
        abort(404);
      }

      //重複したいいね情報を中間テーブルに保存させない。つまり、いいねは１回しか押させない
      $comment->likes()->detach($profile->id);
      //改めていいねを付与する
      $comment->likes()->attach($profile->id);

      return ['comment_id' => $comment->id];
    }

    //likesテーブルにいいね情報(いいね押した側と押された側の情報)を保存
    public function myUnlike(User $user, Comment $comment) {
      //まずいいねを押したプロフィールのオブジェクトを取得
      $profile = $user->profiles()->first();
      //いいね押されたコメントをそのコメントのいいね情報も同時に引っ張ってくる
      $comment = Comment::where('id', $comment->id)->with('likes')->first();

      if(!$comment) {
        abort(404);
      }

      //いいね情報を削除
      $comment->likes()->detach($profile->id);

      return ['comment_id' => $comment->id];
    }

    //プロフィール詳細表示処理
    public function showProfile(User $user, Group $group, Profile $profile) {

      // profilesデーブルの中でをクリックされたプロフィールデータを返す
      $otherProfile = Profile::where('id', $profile->id)->with(['photos', 'comments', 'comments.author', 'comments.likes'])->first();
      return $otherProfile;
    }

    //コメントデータ保存
    public function addComment(User $user, Group $group, Profile $profile, StoreComment $request) {

        // コメントしたプロフィールを取得
        $activeProfile = Auth::user()->profiles()->first();
        // コメントを受けた側のプロフィールをわかりやすいように変換
        $passiveProfile = $profile;

        $comment = new Comment();
        $comment->content = $request->get('content');
        //コメントテーブルに、コメントしたがわの情報をセット
        $comment->active_profile_id = $activeProfile->id;
        // 親から子へのリレーションの保存の仕方でなければならない
        // 次はコメントを受けた側のプロフィールをさらに紐付けてそのコメントを保存
        $passiveProfile->comments()->save($comment);

        //コメントしたプロフィールに紐づくコメントデータをその各ユーザーもまとめて取得する
        $new_comments = Comment::where('passive_profile_id', $passiveProfile->id)->with(['author', 'likes'])->get();
        return $new_comments;
    }

    //likesテーブルにいいね情報(いいね押した側と押された側の情報)を保存
    public function like(User $user, Group $group, Profile $profile, Comment $comment) {
      //まずいいねを押したプロフィールのオブジェクトを取得
      $profile = $user->profiles()->first();
      //いいね押されたコメントをそのコメントのいいね情報も同時に引っ張ってくる
      $comment = Comment::where('id', $comment->id)->with('likes')->first();

      if(!$comment) {
        abort(404);
      }

      //重複したいいね情報を中間テーブルに保存させない。つまり、いいねは１回しか押させない
      $comment->likes()->detach($profile->id);
      //改めていいねを付与する
      $comment->likes()->attach($profile->id);

      return ['comment_id' => $comment->id];
    }

    //likesテーブルにいいね情報(いいね押した側と押された側の情報)を保存
    public function unlike(User $user, Group $group, Profile $profile, Comment $comment) {
      //まずいいねを押したプロフィールのオブジェクトを取得
      $profile = $user->profiles()->first();
      //いいね押されたコメントをそのコメントのいいね情報も同時に引っ張ってくる
      $comment = Comment::where('id', $comment->id)->with('likes')->first();

      if(!$comment) {
        abort(404);
      }

      //いいね情報を削除
      $comment->likes()->detach($profile->id);

      return ['comment_id' => $comment->id];
    }

}
