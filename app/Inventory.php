<?php

namespace App;
use App\Products;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable=['user_id', 'product_id', 'amount', 'equipped'];
}
