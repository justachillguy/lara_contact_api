<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        "name",
        "phone_number",
        "country_code",
        "email",
        "user_id",
        "company",
        "job_title",
        "birthday",
        "notes",
        "photo"
    ];
}
