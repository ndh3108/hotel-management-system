<?php

namespace App\Models;

use App\Core\Model;

class ArticleCategory extends Model
{
    protected string $table = 'article_categories';

    protected array $fillable = [
        'name'
    ];
}

