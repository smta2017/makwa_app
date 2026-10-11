<!DOCTYPE html>
<html>

<head>
	<title>Makwa</title>
	<link rel="stylesheet" type="text/css" href="/css/order_notes/style.css">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
	<script src="/js/invoice/script.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

	<div class="header"></div>
	<div class="content">
		<div class="welcome">
			<h4>Order # <span class="order-number">{{$order['id']}}</span></h4>
			<img class="logo" src="/images/logo.png">
		</div>
		<div class="invoice-info">
			<div class="box">
				<div class="title">Pickup Date</div>
				<p>{{date('d/m/Y',strtotime($order['pickup_date']))}} {{$order['pickup_start']}} - {{$order['pickup_end']}}</p>
			</div>
			<div class="box">
				<div class="title">Address</div>
				<p>Apartment#3, 2nd Floor, Rehab - Cairo</p>
			</div>
			<div class="box">
				<div class="title">Delivery Date</div>
				<p>{{date('d/m/Y',strtotime($order['delivery_date']))}} {{$order['delivery_start']}} - {{$order['delivery_end']}}</p>
			</div>
			<div class="box">
				<div class="title">Vendor</div>
				<p>{{$order['Vendor']["name"]}} </p>
			</div>
		</div>
		<div class="divider"></div>

		<div class="section">
			<div class="title">
				Iron
			</div>

			<div class="items">
				<div class="section-divider-one"></div>
				<div class="item heading">
					<div>Item</div>
					<div>Stains</div>
					<div>Wears & Tears</div>
					<div>Preferences</div>
				</div>

				@foreach ($orderProducts_iron as $orderProduct)
				<div class="section-divider-one"></div>
				<div class="item">
					<div class="first">
						<!-- <img width="60" src="https://makwa-order.s3.amazonaws.com/{{$orderProduct["OrderProductImage"][0]["image_name"]}}"> -->
						<img width="60" src="/images/invoice/product1.png">
						<div class="title">
							<div class="name">{{$orderProduct['ProductPrice']["Product"]["name"]}}</div>
							<div class="color-box" style="border:gray solid 1px;background-color: {{$orderProduct['item_color']}};"></div>
						</div>
					</div>
					<div>@if (count($orderProduct->OrderProductImage->where('image_type','stain')))
						<div>
							<div class="item-to-view view-image" href="#" data-image="img/product1.png" data-name="T-shirt" data-order="{{$order['id']}}">
								Click to view
								<ul class="slider_imgs" style="display: none;">
									@foreach ($orderProduct->OrderProductImage->where('image_type','stain') as $stain)
									<li><img src="https://makwa-order.s3.amazonaws.com/{{ $stain['image_name']}}"></li>
									@endforeach
									<li><img src="/images/logo.png"></li>

								</ul>
							</div>
						</div>
						@else
						N/A
						@endif
					</div>
					<div>
						@if (count($orderProduct->OrderProductImage->where('image_type','cut')))
						<div>
							<div class="item-to-view view-image" href="#" data-image="img/product1.png" data-name="T-shirt" data-order="{{$order['id']}}">
								Click to view
								<ul class="slider_imgs" style="display: none;">
									@foreach ($orderProduct->OrderProductImage->where('image_type','cut') as $cut)
									<li><img src="https://makwa-order.s3.amazonaws.com/{{ $cut['image_name']}}"></li>
									@endforeach
									<li><img src="/images/logo.png"></li>
								</ul>
							</div>
						</div>

						@else
						N/A
						@endif</a>
					</div>
					<div> <?php
							echo \App\Http\Helper\preferenceHelper::getProductPreference($orderProduct["id"]);

							?></div>
				</div>

				@endforeach
			</div>
			<div class="section-divider-two"></div>
		</div>




		<div class="section">
			<div class="title">
				Clean & Iron
			</div>

			<div class="items">
				<div class="section-divider-one"></div>
				<div class="item heading">
					<div>Item</div>
					<div>Stains</div>
					<div>Wears & Tears</div>
					<div>Preferences</div>
				</div>

				@foreach ($orderProducts_clean as $orderProduct)
				<div class="section-divider-one"></div>
				<div class="item">
					<div class="first">
						<!-- <img width="60" src="https://makwa-order.s3.amazonaws.com/{{$orderProduct["OrderProductImage"][0]["image_name"]}}"> -->
						<img width="60" src="/images/invoice/product1.png">
						<div class="title">
							<div class="name">{{$orderProduct['ProductPrice']["Product"]["name"]}}</div>
							<div class="color-box" style="border:gray solid 1px;background-color: {{$orderProduct['item_color']}};"></div>
						</div>
					</div>
					<div>@if (count($orderProduct->OrderProductImage->where('image_type','stain')))
						<div>
							<div class="item-to-view view-image" href="#" data-image="img/product1.png" data-name="T-shirt" data-order="{{$order['id']}}">
								Click to view  
								<ul class="slider_imgs" style="display: none;">
									@foreach ($orderProduct->OrderProductImage->where('image_type','stain') as $stain)
									<li><img src="https://makwa-order.s3.amazonaws.com/{{ $stain['image_name']}}"></li>
									@endforeach
									<li><img src="/images/logo.png"></li>

								</ul>
							</div>
						</div>

						@else
						N/A
						@endif
					</div>
					<div>
						@if (count($orderProduct->OrderProductImage->where('image_type','cut')))
						<div>
							<div class="item-to-view view-image" href="#" data-image="img/product1.png" data-name="T-shirt" data-order="{{$order['id']}}">
								Click to view
								<ul class="slider_imgs" style="display: none;">
									@foreach ($orderProduct->OrderProductImage->where('image_type','cut') as $cut)
									<li><img src="https://makwa-order.s3.amazonaws.com/{{ $cut['image_name']}}"></li>
									@endforeach
									<li><img src="/images/logo.png"></li>
								</ul>
							</div>
						</div>

						@else
						N/A
						@endif</a>
					</div>
					<div> <?php
							echo \App\Http\Helper\preferenceHelper::getProductPreference($orderProduct["id"]);

							?></div>
				</div>

				@endforeach
			</div>
			<div class="section-divider-two"></div>
		</div>



		<div class="footer">
			<div class="social-media">
				<a href="https://www.facebook.com/makwaapp">
					<img src="/images/Invoice/facebook.png">
				</a>
				<a href="https://www.instagram.com/makwa.app">
					<img src="/images/Invoice/instagram.png">
				</a>
				<a href="mailto:support@makwaapp.com">
					<img src="/images/Invoice/mail.png">
				</a>
			</div>
			<div class="copyright">
				<p>Copyright © 2021 Makwa, All rights reserved.</p>
			</div>
		</div>
		<div class="image-modal">
		<div class="img-container">
			<span class="close_btn">X</span>

			<div class="info">
				<div class="name"></div>
				<div class="order">Order # <span class="order-number"></span></div>
			</div>

			<div id="slider" class="modal_slider">
			  <a href="#" class="control_next">></a>
			  <a href="#" class="control_prev"><</a>
			</div>
		</div>
	</div>
	</div>
</body>

</html>