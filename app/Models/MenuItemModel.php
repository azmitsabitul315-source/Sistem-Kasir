<?php
namespace App\Models;

use CodeIgniter\Model;

class MenuItemModel extends Model
{
    protected $table = 'menu_items';
    protected $primaryKey = 'id';
    protected $allowedFields = ['category_id', 'name', 'price', 'status', 'image'];
    protected $useTimestamps = true;

    public function getActiveItems()
    {
        return $this->where('status', 'aktif')->findAll();
    }
}
?>
