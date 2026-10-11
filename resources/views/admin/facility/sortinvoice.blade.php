<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>

    <h1><a style="text-decoration: none" href="/orders"> MAKWA INVOICE</a></h1>
    <h3>
        {{$order->Customer["first_name"]}} {{$order->Customer["last_name"]}} #{{$order->Customer["id"]}}
    </h3>
    @foreach($order->OrderProducts->where('ProductPrice.service_id',$sid)->groupBy('ProductPrice.service_id') as $OrderProduct)
    <table>
        <thead>
            <tr class="text-capitalize">
                <th style="width: 100px; text-align: left">{{$OrderProduct[0]->ProductPrice->Service->name}}</th>
                <th>PREF:</th>
            </tr>
        </thead>
        <tbody>
            @foreach($OrderProduct->groupBy('product_price_id') as $OrderProd)
            <tr>
                <td style="width: 100px; text-align: left">({{$OrderProd->count()}}) {{$OrderProd[0]->ProductPrice->product->name}}</td>
                <td>
                    <!-- ----------------- Prefrences ------------------------- -->
                    <span style="color: red">
                        <?php
                        echo \App\Http\Helper\preferenceHelper::getProductPreference($OrderProd[0]->id);
                        ?>
                    </span>
                    <!-- ----------------- Prefrences ------------------------- -->

                </td>
            </tr>
            @endforeach
        </tbody>


    </table>
    @endforeach
    <h3> Total Items : {{$order->OrderProducts->where('ProductPrice.service_id',$sid)->count()}}</h3>

</body>

</html>