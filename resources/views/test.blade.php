<div id="custinfo" style="text-align: center;">

    <span style="font-family:arial; font-size:18px">#{{$res["customer_id"]}}</span> <br> <br>
    <span style="font-family:arial;  margit-top:10px;font-size:16px">{{$res["customer_name"]}}</span> <br> <br>
    <span style="font-family:arial; margit-top:10px; font-size:14px">clean</span> <br>
    <img src="https://chart.googleapis.com/chart?cht=qr&chl={{$res['qrc']}}&chs=160x160&chld=L|0" class="qr-code img-thumbnail img-responsive" />
    <br> <br>
    <span style="font-family:arial; font-size:18px">{{$res["qrc"]}}</span>
</div>


<script>
    // Function to HTML encode the text
    // This creates a new hidden element,
    // inserts the given text into it 
    // and outputs it out as HTML
    function htmlEncode(value) {
        return $('<div/>').text(value)
            .html();
    }

    $(function() {

        // Specify an onclick function
        // for the generate button
        $('#generate').click(function() {

            // Generate the link that would be
            // used to generate the QR Code
            // with the given data 
            let finalURL = 'https://chart.googleapis.com/chart?cht=qr&chl=' + htmlEncode($('#content').val()) + '&chs=160x160&chld=L|0'

            // Replace the src of the image with
            // the QR code image
            $('.qr-code').attr('src', finalURL);
        });
    });
</script>