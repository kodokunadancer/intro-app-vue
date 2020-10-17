<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $visible = [
      'id', 'user_id', 'name', 'introduction', 'photos', 'comments'
    ];
    //このプロフィールの保持者をたどる
    public function owner() {
      return $this->belongsTo('App\User', 'user_id', 'id', 'users');
    }
    //このプロフィールが所有する写真をたどる
    public function photos() {
      return $this->hasMany('App\Photo');
    }
    //このプロフィールが有するコメントをたどる
    public function comments() {
      return $this->hasMany('App\Comment', 'passive_profile_id', 'id', 'comments');
    }
}
