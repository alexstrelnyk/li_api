<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ContentItemArea
 *
 * @property int $focus_area_topics_id
 * @property int $content_item_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContentItemArea newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContentItemArea newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContentItemArea query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContentItemArea whereContentItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContentItemArea whereFocusAreaTopicsId($value)
 * @mixin \Eloquent
 */
class ContentItemArea extends Model
{
    protected $table = 'focus_areas_topics_content_items';

    public $timestamps = false;

    public $incrementing = false;
}
