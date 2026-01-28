<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranslatedMediaTranslation extends Model
{
    protected $fillable = [
        'locale',

        'title',
        'description',
    ];

    ## Relations

    public function translatedMedia(): BelongsTo
    {
        return $this->belongsTo(TranslatedMedia::class, 'translated_media_id');
    }
}
