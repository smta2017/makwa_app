<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style type="text/css">
        @media print {
            .print {
                page-break-after: always;
            }
        }
    </style>
</head>

<body>

    <h3 id="customer-info">

    </h3>
    <div class="print" id="product-items-hang">

        <span style="font-weight: bold; font-size: 20;"></span>
    </div>

    <div class="print" id="product-items-fold">

    </div>
    <h3 id="total-items"></h3>

</body>
<script src="{{ asset ('assets/js/vendor/jquery-2.2.4.min.js')}}"></script>

<script>
    let searchParams = window.location;
    var url_string = searchParams.href; //window.location.href
    var url = new URL(url_string);

    // Retrieve
    var token = localStorage.getItem("token");
    // var order_id = url.searchParams.get("id");
    var order_bag_id = url.searchParams.get("order_bag_id");

    $(document).ready(function() {
        $.ajax({
            url: '/api/facility/delivery-order-invoice',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                order_bag_id: order_bag_id
            },
            success: function(res) {
                console.log(res);


                // $("#customer-info").html(res.customer.name + ' #' + res.customer.id);
                // $("#service-th").html(res.serviceName);
                // $("#total-items").html('اجمالى القطع : ' + res.totaItems);
                var prod_item = "";
                // $.each(res["serviceDetail"], function(i, prod) {
                // var hangs = res.data.hang
                // var folds = res.data.fold
                // console.log(hangs);
                console.log(res['data']['customer']["first_name"]);
                if (res.data.hang.length != 0) {
                    prod_item += ` 
                    <a style="text-decoration: none" href="/vendor/delivery-sort"><img src="/imgs/makwa2.png" width="50" alt=""> <span style="font-size:40px">H</span></a><br>
                    

                        <table>
                            <tbody>
                                <tr>
                                    <td><span style="font-weight: bold; font-size: 16;"> ` + res['data']['service']+ ` #` + res['data']['order']['id'] + ` </span></td>
                                </tr>
                                <tr>
                                    <td> <span style="font-weight: bold; font-size: 20px;">` + res['data']['customer']['first_name'] + ' ' + res['data']['customer']['last_name'] + ` # ` + res['data']['customer']['id'] + `</span></td>
                                </tr>
                                <tr>
                                <td>  <span style="font-weight: bold; font-size: 20px;">` + res['data']['order']['delivery']['day'] + ` - ` + res['data']['order']['delivery']['full_date'] + `----` + res['data']['order']['delivery']['start'] + `->` + res['data']['order']['delivery']['end'] + `</span></td>
                                </tr>
                                
                                <tr>
                                    <td> <span style="font-weight: bold; font-size: 20px;">` + res.data.hang.length + ` Items </span></td>
                                </tr>
        
                            </tbody>
                        </table>
                        `;
                    $("#product-items-hang").html(prod_item);
                }

                if (res.data.fold.length != 0) {
                    prod_item = ` 
                    
                    <hr>
                    <a style="text-decoration: none" href="/vendor/delivery-sort"><img src="/imgs/makwa2.png" width="50" alt=""> <span style="font-size:40px">F</span></a><br>
                        <table>
                            <tbody>
                            <tr>
                                    <td><span style="font-weight: bold; font-size: 16;"> ` + res['data']['service']+ ` #` + res['data']['order']['id'] + ` </span></td>
                                </tr>
                                <tr>
                                    <td> <span style="font-weight: bold; font-size: 20px;">` + res['data']['customer']['first_name'] + ' ' + res['data']['customer']['last_name'] + ` # ` + res['data']['customer']['id'] + `</span></td>
                                </tr>
                                <tr>
                                    <td> <span style="font-weight: bold; font-size: 20px;">` + res['data']['order']['delivery']['day'] + ` - ` + res['data']['order']['delivery']['full_date'] + `----` + res['data']['order']['delivery']['start'] + `->` + res['data']['order']['delivery']['end'] + `</span></td>
                                </tr>
                                
                                <tr>
                                    <td> <span style="font-weight: bold; font-size: 20px;">` + res.data.fold.length + ` Items </span></td>
                                </tr>
        
                            </tbody>
                        </table>
                        `;
                    $("#product-items-fold").html(prod_item);
                }
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    });
</script>

</html>