<?php

namespace ThemeLooks\SecureLooks\Model;

use Illuminate\Database\Eloquent\Model;

class Key extends Model
{

    protected $table = "user_keys";

    protected $fillable = ['item'];
}
