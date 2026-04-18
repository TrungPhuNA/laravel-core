<?php

namespace Modules\CheatSheet\Domain\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class CheatSheet extends Model
{
    use SoftDeletes;

    protected $table = 'cheat_sheets';

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'visibility',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            related: CheatSheetTag::class,
            table: 'cheat_sheet_tag',
            foreignPivotKey: 'cheat_sheet_id',
            relatedPivotKey: 'tag_id',
        );
    }
}
