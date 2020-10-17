<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
//モデル
use App\Group;
use App\User;
use App\Profile;
use App\Photo;
//フォームリクエスト
use App\Http\Requests\CreateGroup;
use App\Http\Requests\ReserchGroup;
use App\Http\Requests\EditGroup;
//ファイル処理
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
//Authクラス
use Illuminate\Support\Facades\Auth;
//Gateクラス
use Gate;
use Carbon\Carbon;

class GroupController extends Controller
{

    //該当グループ取得
    public function getGroup(Group $group) {
      $editGroup = Group::where('id', $group->id)->with('photo')->first();
      return $editGroup;
    }

    //グループ作成処理
    public function create(User $user, CreateGroup $request) {
      //まずグループテーブルに保存
      $group = new Group();
      $group->name = $request->name;
      $group->password = $group->password;
      $group->author_id = $user->id;
      $group->save();

      //中間テーブルに作成者（参加者）のプロフィールとグループの紐づきを保存
      Auth::user()->groups()->attach($group);

      return response($group, 201);
    }

    //グループ検索処理
    public function reserch(User $user, ReserchGroup $request) {
      //検索したグループ名とパスワードの両方が一致するグループを抽出する
      $group = Group::where([
        ['name', $request->group_name],
        ['password', $request->password],
      ])->with('photo')->first();

      if($group) {
        return response($group, 200);
      }
      return false;
    }

    //グループ参加処理
    public function join(User $user, Group $group) {
      //中間テーブルに保存する
      //すでに同じグループに参加している場合は、リダイレクトする
      try {
        //中間テーブルにログインユーザーと参加するグループの関係性を保存
        $user->groups()->attach($group);
        //参加するグループ内へ
        return $group;
      }
      catch(\Exception $e) {
        DB::rollback();
        return abort(404);
      }
    }

    //グループ一覧
    public function index(User $user) {
      if($user->groups) {
        $my_groups = $user->groups()->with('photo')->get();
        return $my_groups;
      }
      return response(200);
    }


    //グループ編集処理
    public function edit(User $user, Group $group, EditGroup $request) {

      //画像がサーバーへ送られてきた場合のみ画像処理をする
      if($request->photo) {

        // 投稿写真の拡張子を取得する
        $extension = $request->photo->extension();

        $group_photo = new Photo();

        // インスタンス生成時に割り振られたランダムなID値と
        // 本来の拡張子を組み合わせてファイル名とする
        $group_photo->filename = $group_photo->random_id . '.' . $extension;

        // S3にファイルを保存する
        // 第三引数の'public'はファイルを公開状態で保存するため
        $group_photo->filename = Storage::cloud()->putFileAs('vue', $request->photo, $group_photo->filename, 'public');

        // データベースエラー時にファイル削除を行うため
        // トランザクションを利用する
        DB::beginTransaction();

        try {
            $group->photo()->delete();
            $group->photo()->save($group_photo);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            // DBとの不整合を避けるためアップロードしたファイルを削除
            Storage::cloud()->delete($group_photo->filename);
            throw $exception;
        }
      }

      //グループ名の変更
      $group->name = $request->name;
      $group->save();

      return response(201);
    }

    //グループ詳細
    public function showGroup(User $user, Group $group) {
      // 参加するグループと紐付いている複数のユーザーを配列に格納
      foreach($group->users as $users) {
        $group_users[] = $users;
      }
      //参加するグループに紐付いている複数ユーザーにさらに紐付いているプロフィールをひとつずつ取り出す
      foreach($group_users as $group_user) {
        $profiles[] = Profile::where('user_id', $group_user->id)->with('photos')->first();
      }
      // プロフィールの配列を名前の五十音順を軸に並び替える
      // プロフィールのnameキーと値のセットを別の配列へ一旦まとめて格納。idについてもまとめておく。
      foreach($profiles as $value) {
        $name_array[] = $value['name'];
        $id_array[] = $value['id'];
      }
      // profiles多次元配列をnameの五十音順を軸に並び替える
      array_multisort($name_array, SORT_ASC, SORT_STRING, $id_array, SORT_ASC, SORT_NUMERIC, $profiles);
      //自分のプロフィールを取得
      $my_profile = Profile::where('user_id', $user->id)->with('photos')->first();
      return [$group, $my_profile, $profiles];
    }

    //グループから退会
    public function exitGroup(User $user, Group $group) {
      $group->users()->detach($user);
      return response(200);
    }

    //グループの削除
    public function delete(User $user, Group $group) {
      $group->delete();
      return response(200);
    }

    //グループから強制退会
    public function force(User $user, Group $group, Profile $profile) {
      $exit_user = $profile->owner()->first();
      $group->users()->detach($exit_user);
      return response(200);
    }
}
