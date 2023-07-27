<?php

namespace ThemeLooks\SecureLooks;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{

    protected $table = "license_keys";

    protected $fillable = ['item'];
}
