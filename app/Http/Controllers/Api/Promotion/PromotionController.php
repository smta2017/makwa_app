<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Controller;
use App\Order;
use App\Promotion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $promotion = new Promotion();
        $promotion->cap = $request->cap;
        $promotion->discount_figure = $request->discount_figure;
        $promotion->start_date = $request->start_date;
        $promotion->name = $request->p_name;
        $promotion->end_date = $request->end_date;
        $promotion->min_order = $request->min_order;
        $promotion->type = $request->type;
        $promotion->show_in_app = $request->show_in_app;
        $promotion->usage_limit = $request->usage_limit;
        $promotion->applay_on_delivery = ($request->applay_on_delivery == "true") ? 1 : NULL;

        if ($promotion->save()) {
            return response()->json(['sucsess' => '1'], 200);
        } else {
            return response()->json(['sucsess' => '0'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $promotion = Promotion::find($id);
        return response()->json(['sucsess' => '1', "data" => $promotion], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $promotion = Promotion::find($id);
        $promotion->cap = $request->cap;
        $promotion->discount_figure = $request->discount_figure;
        $promotion->start_date = $request->start_date;
        $promotion->name = $request->p_name;
        $promotion->end_date = $request->end_date;
        $promotion->min_order = $request->min_order;
        $promotion->type = $request->type;
        $promotion->show_in_app = $request->show_in_app;
        $promotion->usage_limit = $request->usage_limit;
        $promotion->applay_on_delivery = ($request->applay_on_delivery == "true") ? 1 : NULL;

        if ($promotion->save()) {
            return response()->json(['sucsess' => '1'], 200);
        } else {
            return response()->json(['sucsess' => '0'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }



    //=================================
    public function getPromotionsForUserByUserId($id)
    {
        $dt = Carbon::now()->toDateString();
        $query = Promotion::query();
        $query->where([
            ['end_date', '>=', $dt],
            ['start_date', '<=', $dt],
            ['is_archive', '=', 0],
            ['show_in_app', '=', true]
        ]);
        $promotions = $query->get();

        $result = [];
        foreach ($promotions as $promotion) {
            $orders = Order::query()->where([
                ['customer_id', '=', $id],
                ['promotion_id', '=', $promotion->id]
            ])->get();
            if ($promotion->usage_limit > $orders->count()) {
                $result[] = $promotion;
            }
        }
        return $result;
    }

    public function isPromoValid($id, $code)
    {
        $code = strtolower($code);
        $dt = Carbon::now()->toDateString();
        $query = Promotion::query();
        $query->where([
            ['end_date', '>=', $dt],
            ['start_date', '<=', $dt],
            ['is_archive', '=', 0],
            ['name', '=', $code]
        ]);
        $promotion = $query->first();

        if (!$promotion) {
            return false;
        }
        $orders = Order::query()->where([
            ['customer_id', '=', $id],
            ['promotion_id', '=', $promotion->id]
        ])->get();
        if ($promotion->usage_limit <= $orders->count()) {
            return false;
        }
        return true;
    }


    public function getAvailablePromotions(Request $request)
    {
        // $this->authenticate($request);
        // return json_encode(array('status' => 0), JSON_NUMERIC_CHECK);

        if (isset($request['CustomerId'])) {
            $promotions = $this->getPromotionsForUserByUserId($request['customer_id']);
            $success_arr = array(
                'status' => 1,
                'message' => 'Successfully promotions orders against customer.',
                'promotions' => $promotions,

            );
            return json_encode($success_arr, JSON_NUMERIC_CHECK);
        } else {
            $failure_array = array(
                "status" => 0,
                'message' => 'No Customer found against this ID.',
            );
            return json_encode($failure_array, JSON_NUMERIC_CHECK);
        }




        // $success_arr = array(
        //     'status' => 1,
        //     'message' => 'Successfully promotions orders against customer.',
        //     'promotions'=> [[
        //             'id' => 1,
        //             'name' => "DMA30",
        //             'type' => "fixed",
        //             'discount_figure' => 30,
        //             'cap' => 0,
        //             'usage_limit' => 1,
        //             'min_order' => 0,
        //             'start_date' => "2019-07-20",
        //             'end_date' => "2020-01-01",
        //             'is_archive' => 0,
        //             'created_at' => "2019-06-03 10:06:10",
        //             'updated_at' => "2020-01-10 05:26:25",
        //             'title' => "Launch promotion",
        //             'message' => "Enjoy a fixed E£30 off your first Makwa",
        //             'show_in_app' => 0
        //         ]]
        // );
        // return json_encode($success_arr, JSON_NUMERIC_CHECK);
    }

    public function checkPromoCode(Request $request)
    {

        if (isset($request['code']) && isset($request['customer_id'])) {
            $is_promo_valid = $this->isPromoValid($request['customer_id'], $request['code']);
            $is_personal_code_valid = false; //$this->wr->isPersonalCodeValid($request['customer_id'], $request['code']);

            if ($is_promo_valid || $is_personal_code_valid) {
                $valid = true;
                $success_arr = array(
                    'status' => 1,
                    'message' => 'Successfully added.',
                    'valid' => $valid,
                );
            } else {
                $valid = false;
                $success_arr = array(
                    'status' => 1,
                    'message' => 'Not a valid Promo Code.',
                    'valid' => $valid,
                );
            }

            return json_encode($success_arr, JSON_NUMERIC_CHECK);
        } else {
            $failure_array = array(
                "status" => 0,
                'message' => 'No Personal Code found.',
            );
            return json_encode($failure_array, JSON_NUMERIC_CHECK);
        }
    }
}
