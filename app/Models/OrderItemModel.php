<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_id', 'menu_item_id', 'item_name',
        'price', 'qty', 'note', 'item_subtotal'
    ];
    protected $useTimestamps = true;
}
