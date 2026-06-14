<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DOItem;
use App\Models\DeliveryOrder;
use Illuminate\Http\Request;

class DOItemController extends Controller
{
    public function getItems($doId)
    {
        $deliveryOrder = DeliveryOrder::find($doId);
        
        if (!$deliveryOrder) {
            return response()->json(['items' => []]);
        }
        
        $items = DOItem::where('do_id', $doId)->get();
        
        return response()->json([
            'items' => $items,
            'do_number' => $deliveryOrder->do_number
        ]);
    }
}