<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
//Arrクラスを使用するため
use Illuminate\Support\Arr;

class Group extends Model
{

  //レスポンスに含めるもの。限定する。
  protected $visible = [
    'id', 'name', 'password', 'author_id', 'photo', 'users'
  ];

  //passwordの桁数
  const PASSWORD_LENGTH = 6;

  //インスタンス化された瞬間に実行される
  //引数でpasswordの値を受け取る
  public function __construct(array $attributes = [])
  {
      //親クラスであるModelクラスの呼び出し
      parent::__construct($attributes);

      //attributes配列に、passwordが含まれない場合、setPasswordメソッドを実行
      if (! Arr::get($this->attributes, 'password')) {
          $this->setPassword();
      }
  }

  //attributes配列のpasswordキーに、getRandomIdで取得したパスワードを代入
  private function setPassword()
  {
    $this->attributes['password'] = $this->getRandomPassword();
  }

  //実際にパスワード生成処理
  private function getRandomPassword()
  {
    //使用する文字列の用意
    $characters = array_merge(
        range(0, 9), range('a', 'z'),
        range('A', 'Z'), ['-', '_']
    );

    //配列の中の要素の数を数える
    $length = count($characters);

    //空の変数を用意
    $password = "";

    //ランダムな文字列を生成
    //配列から一文字ずつランダムに取り出し、６巡目でストップさせる
    for ($i = 0; $i < self::PASSWORD_LENGTH; $i++) {
      $password .= $characters[random_int(0, $length - 1)];
    }

    return $password;
  }

  public function photo() {
    return $this->hasOne('App\Photo');
  }

  //groupsテーブルのあるインスタンスとそれに紐付いている複数のプロフィールインスタンスの関係性を中間テーブルを介して認識させる
  public function users() {
    return $this->belongsToMany('App\User');
  }
}
