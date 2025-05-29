<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserThemeSetting extends Model
{
    protected $fillable = [
        'user_id',
        'theme',
        'font_family',
        'font_size',
        'primary_color',
        'secondary_color',
        'background_color',
        'text_color',
        'compact_mode',
        'custom_css'
    ];

    protected $casts = [
        'compact_mode' => 'boolean',
        'custom_css' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getThemeStyles()
    {
        return [
            'theme' => $this->theme,
            'fontFamily' => $this->font_family,
            'fontSize' => $this->font_size,
            'primaryColor' => $this->primary_color,
            'secondaryColor' => $this->secondary_color,
            'backgroundColor' => $this->background_color,
            'textColor' => $this->text_color,
            'compactMode' => $this->compact_mode,
            'customCss' => $this->custom_css
        ];
    }
}
