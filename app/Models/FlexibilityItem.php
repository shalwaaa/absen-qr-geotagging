<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FlexibilityItem extends Model {
    protected $fillable =['item_name', 'description', 'icon', 'item_type', 'value_minutes', 'point_cost', 'stock_limit', 'is_active'];
}