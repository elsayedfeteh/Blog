<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\Cast;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'permissions'
    ];

    protected $casts = [
        "permissions" => "array",
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
