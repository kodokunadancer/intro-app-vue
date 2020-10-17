<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//Arrクラスを使用するため
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class Photo extends Model
{
    protected $primaryKey = 'random_id';

    public $incrementing = false; 

    protected $appends = [
      'url'
    ];

    protected $visible = [
      'random_id', 'url'
    ];

    protected $kyeType = 'string';

    const ID_LENGTH = 12;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (! Arr::get($this->attributes, 'random_id')) {
            $this->setId();
        }
    }

    private function setId() {
        $this->attributes['random_id'] = $this->getRandomId();
    }

    private function getRandomId() {
        $characters = array_merge(
            range(0, 9), range('a', 'z'),
            range('A', 'Z'), ['-', '_']
        );

        $length = count($characters);

        $random_id = "";

        for ($i = 0; $i < self::ID_LENGTH; $i++) {
            $random_id .= $characters[random_int(0, $length - 1)];
        }

        return $random_id;
    }

    public function getUrlAttribute() {
      return Storage::cloud()->url($this->attributes['filename']);
    }
}
