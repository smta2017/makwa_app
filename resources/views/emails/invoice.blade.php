<!DOCTYPE html>
<html>

<head>
    <title>Makwa</title>
    <link rel="stylesheet" type="text/css" href="css/invoice/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

    <div class="header"></div>
    <div class="content">
        <div class="welcome">
            <h4>Hello {{$customer['first_name']}} {{$customer['last_name']}},</h4>
            <p>Your order has been processed and is being prepared for cleaning / ironing.<br>Please check your order summary below.</p>
        </div>
        <div class="invoice-info">
            <div class="box">
                <div class="title">Pickup Date</div>
                <p>{{date('d/m/Y',strtotime($customer['pickup_date']))}} {{ substr($customer['pickup_start'], 0, -3) }} - {{ substr($customer['pickup_end'], 0, -3) }}</p>
            </div>
            <div class="box">
                <div class="title">Address</div>
                <p>{{$customer['building_no']}},{{ $customer['street_address']  ?  $customer['street_address'].' street,' : '' }} {{ $customer['floor_no'] ? 'Floor '.$customer['floor_no'].',' : '' }} Apt. {{$customer['apartment_no']}}</p>
            </div>
            <div class="box">
                <div class="title">Delivery Date</div>
                <p> {{date('d/m/Y',strtotime($customer['delivery_date']))}} {{ substr($customer['delivery_start'], 0, -3) }} - {{ substr($customer['delivery_end'], 0, -3) }}</p>
            </div>
            <div class="box">
                <div class="title">Vendor</div>
                <p>Aly Clean </p>
            </div>
        </div>
        <div class="divider"></div>

        <?php
        if (!empty($serviceDetail[1][0])) {
        ?>
            <div class="section">
                <div class="title">
                    {{ucfirst(str_replace("_"," ",$serviceName[1]))}}
                </div>
                <div class="section-divider-one"></div>
                <div class="items">
                    <?php
                    foreach ($serviceDetail[1] as $serviceDetail[1]) {
                    ?>
                        <div class="item">
                            <div>{{$serviceDetail[1]['product_count']}} x {{$serviceDetail[1]['product_name']}}</div>
                            <div><?php if ($serviceDetail[1]['preference'] == 'folded') { ?>(Fold) <?php } else { ?> (Hang)<?php } ?></div>
                            <div class="price-box">EGP {{$serviceDetail[1]['product_price']}}</div>
                        </div>

                    <?php
                    }
                    ?>
                </div>
                <div class="section-divider-two"></div>
            </div>

            <div class="section">
                <div class="title">
                    {{ucfirst(str_replace("_"," ",$serviceName[2]))}}
                </div>
                <div class="section-divider-one"></div>
                <div class="items">
                    <?php
                    foreach ($serviceDetail[2] as $serviceDetail[2]) {
                    ?>
                        <div class="item">
                            <div>{{$serviceDetail[2]['product_count']}} x {{$serviceDetail[2]['product_name']}}</div>
                            <div><?php if ($serviceDetail[2]['preference'] == 'folded') { ?>(Fold) <?php } else { ?> (Hang)<?php } ?></div>
                            <div class="price-box">EGP {{$serviceDetail[2]['product_price']}}</div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <div class="section-divider-two"></div>
            </div>

        <?php } ?>

        <div class="totals">
            <div class="titles">
                <div class="title">Subtotal</div>
                @if($deliveryFee != 0) <div class="title">Delivery fee</div>@endif
                @if($discount != 0) <div class="title">Discount</div>@endif
                @if($promotionDiscount != 0) <div class="title">Promotion Discount</div> @endif
                @if($cancellation_fee != 0) <div class="title">Cancellation fee</div> @endif
                @if($reschedule_fee != 0) <div class="title">Reschedule fee</div> @endif
                @if($next_day_fee != 0) <div class="title">Next day fee</div> @endif
                <div class="section-divider-one"></div>
                <div class="total">Total Due</div>
            </div>
            <div class="prices">
                <div class="title">EGP {{$subTotal}}</div>
                @if($deliveryFee != 0) <div class="title">EGP {{$deliveryFee }}</div>@endif
                @if($discount != 0) <div class="title">EGP {{$discount }}</div>@endif
                @if($promotionDiscount != 0) <div class="title">{{promotionDiscount}}</div> @endif
                @if($cancellation_fee != 0) <div class="title">{{cancellation_fee}}</div> @endif
                @if($reschedule_fee != 0) <div class="title">{{reschedule_fee}}</div> @endif
                @if($next_day_fee != 0) <div class="title">{{next_day_fee}}</div> @endif
                <div class="section-divider-one"></div>
                <div class="total">EGP {{$total_amount}}</div>
            </div>
        </div>

        <div class="order-notes">
           
            <a href="/order-notes/{{$order->id}}" target="_blanck"> <button>Order Notes</button></a>
            <p>Check your detailed order notes by clicking on this button.</p>
        </div>

        <div class="footer">
            <div class="social-media">
                <a href="https://www.facebook.com/makwaapp">
                    <img src="images/invoice/facebook.png">
                </a>
                <a href="https://www.instagram.com/makwa.app">
                    <img src="images/invoice/instagram.png">
                </a>
                <a href="mailto:support@makwaapp.com">
                    <img src="images/invoice/mail.png">
                </a>
            </div>
            <div class="copyright">
                <p>Copyright © 2021 Makwa, All rights reserved.</p>
            </div>
        </div>

    </div>
</body>

</html>