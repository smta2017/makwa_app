<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

</head>

<body>
    <div>
        <a style="text-decoration: none" href="/vendor/pickup-sort"><img src="/imgs/makwa2.png" width="50" alt=""><br>
            <span id="service-th" style=" font-weight: bold ; font-size: 24px; color: black;"></span> - <span style="font-weight: bold ; font-size: 26px; color: black;" id="order_id"></span></a>
        <br>
        <span id="bagsnumber"></span>
        <h3 id="customer-info"></h3>


        <table>

            <tbody id="product-items">

            </tbody>


        </table>
        <h3 id="total-items"></h3>
        <div id="qrcode" style="width:100px; height:100px; margin-top:15px;"></div>
        <br>
        <span style="font-size: 16px;" id="qrval"></span>
    </div>



</body>
<script src="{{ asset ('assets/js/vendor/jquery-2.2.4.min.js')}}"></script>
<script type="text/javascript" src="/js/qrcode.min.js"></script>

<script>
    let searchParams = window.location;
    var url_string = searchParams.href; //window.location.href
    var url = new URL(url_string);

    // Retrieve
    var token = localStorage.getItem("token");
    var order_bag_id = url.searchParams.get("order_bag_id");

    var qrcode = new QRCode(document.getElementById("qrcode"), {
        width: 100,
        height: 100
    });


    function makeCode(qr) {
        qrcode.makeCode(qr);
    }

    // makeCode();


    $(document).ready(function() {
        $.ajax({
            url: '/api/facility/order-invoice',
            type: 'post',
            headers: {
                Accept: 'application/json',
                Authorization: 'bearer ' + token,
            },
            data: {
                order_bag_id: order_bag_id
            },
            success: function(res) {
                $("#customer-info").html(res.customer.name + ' #' + res.customer.id);
                $("#service-th").html(res.serviceName);
                $("#order_id").html('#' + res.id);
                $("#total-items").html('اجمالى القطع : ' + res.totaItems);
                $('#bagsnumber').html('شنطة ' + res.sortedBags.length + ' - ' + res.orderBags.length);

                var prod_item = "";
                $.each(res["serviceDetail"], function(i, prod) {
                    prod_item += ` <tr style="border-:solid 1px black">
                    <td style="width: 100px; text-align: left">(` + prod.p_count + `) ` + prod.name_ar + `</td>
                        <td>
                            <span style="font:16px">
                            ` + prod.prfrence + `
                            </span>
                        </td>
                    </tr>`;
                });
                $("#product-items").html(prod_item);
                $('#qrval').html(res.qrcode);
                makeCode(res.qrcode);
                console.log(res);
            },
            error: function(msg) {
                console.error(msg);
            }
        });
    });
</script>

</html>