<?php

namespace Modules\CheatSheet\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class CheatSheetTag extends Model
{
    protected $table = 'cheat_sheet_tags';

    protected $fillable = [
        'user_id',
        'name',
        'slug',
    ];

    public function cheatSheets(): BelongsToMany
    {
        return $this->belongsToMany(
            related: CheatSheet::class,
            table: 'cheat_sheet_tag',
            foreignPivotKey: 'tag_id',
            relatedPivotKey: 'cheat_sheet_id',
        );
    }
}
