<?php
namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'invoice_no', 'subtotal', 'discount', 'total',
        'payment_method', 'amount_paid', 'change_amount',
        'status', 'void_reason'
    ];
    protected $useTimestamps = true;
}
?>
