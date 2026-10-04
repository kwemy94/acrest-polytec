<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterAbonne extends Model
{
    protected $fillable = [
        'email',
    ];

    protected $table = 'newsletter_abonnes';
}
