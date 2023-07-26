<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    use HasFactory;

    protected $fillable = ["user_id", "contact_id"];

    public function contact()
    {
        $this->belongsTo(Contact::class);
    }
}


