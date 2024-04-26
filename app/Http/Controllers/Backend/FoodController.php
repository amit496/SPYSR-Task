<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Food;

class FoodController extends Controller
{
    public function place(Request $request)
    {
        try {
            $order= new Food();
            $order->date= $request->order_date;
            $order->food_timing = implode(', ',$request->schedule);
            $order->save();
            return ['status'=>true, 'output'=> $order, 'message'=>'Order Placed!'];
        } catch (\Throwable $th) {
            return ['status'=>false, 'message'=> $th->getMessage(),'trace'=>$th->getTrace() ];
        }
    }

    public function getEventData()
    {
        $data = \DB::table('foodorders')->get(['date as start','food_timing as title', \DB::raw("('#6d943d') as color")]);
        return $data;
    }
}
