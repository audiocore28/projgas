<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Purchases</title>
	{{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}

	<style>
		img {
			display: block;
			max-width:230px;
			max-height:95px;
			width: auto;
			height: auto;
		}

		.column {
			float: left;
			width: 50%;
		}

		/* Clear floats after the columns */
		.row:after {
			content: "";
			display: table;
			clear: both;
		}

		.page-break {
			page-break-after: always;
		}
	</style>
</head>
<body>
	{{-- Company Logo --}}
	<div style="width: 50%; margin: 0 auto 40px auto; font-size: 12px" align="center">
		<img src="img/ibbtrading_logo.png">
		<div style="margin-top: -20px;" align="center">
			<small>Labangan Poblacion, San Jose, Occidental Mindoro</small> <br>
			<small>(043) 732-1642 / ibbfuels@gmail.com</small>
		</div>
	</div>

	{{-- Tables --}}
	<div style="font: 12px sans-serif;">
		@foreach($purchases as $purchase)
		<div style="margin-bottom: 35px;">
			{{-- Purchase --}}
			<table width="85%" cellspacing="0" cellpadding="5">
				<tr style="background-color: red; color: #fff;">
					<td align="left"><b><span>{{ $purchase->date }}</span></b></td>
					<td align="center"><b><span>{{ $purchase->supplier ? $purchase->supplier->name : '' }}</span></b></td>
					<td align="center"><b><span>{{ $purchase->depot ? $purchase->depot->name : '' }}</span></b></td>
					<td align="center"><b><span>{{ $purchase->account ? $purchase->account->name : '' }}</span></b></td>
					<td align="right"><b><span>{{ $purchase->purchase_no }}</span></b></td>
				</tr>
			</table>

			{{-- PurchaseDetail --}}
			<?php
				$total_purchase_amount = 0;
				$total_purchase_quantity = 0;
				$total_diesel_purchase_quantity = 0;
				$total_regular_purchase_quantity = 0;
				$total_premium_purchase_quantity = 0;
			?>
			<table class="table" width="85%" cellspacing="0" cellpadding="5" style="margin-top: 10px; font-size: 11px;">
				@foreach($purchase->purchaseDetails as $detail)
				<?php
					$amount = $detail->quantity * $detail->unit_price;
					$total_purchase_amount += $amount;
					$total_purchase_quantity += $detail->quantity;
				?>

					@switch($detail->product->name)
					   @case('Diesel')
							<?php $total_diesel_purchase_quantity += $detail->quantity; ?>
					      @break

					   @case('Premium')
							<?php $total_premium_purchase_quantity += $detail->quantity; ?>
					      @break

					   @default
							<?php $total_regular_purchase_quantity += $detail->quantity; ?>
					@endswitch

				<tr>
					<td align="left">{{ $detail->product->name }}</td>
					<td align="right">{{ number_format($detail->quantity) }}</td>
					<td align="right">{{ $detail->unit_price }}</td>
					<td align="right">{{ number_format($amount) }}</td>
					<td align="left" style="color: red;"><b>{{ $detail->remarks }}</b></td>
				</tr>
				@endforeach
				<tr style="background-color: #eee;">
					<td></td>
					<td align="right" style="border-top: 1px solid black; color: green;"><b>{{ number_format($total_purchase_quantity) }}</b></td>
					<td></td>
					<td align="right" style="border-top: 1px solid black;"><b>{{ number_format($total_purchase_amount) }}</b></td>
					<td></td>
				</tr>
			</table>

			{{-- TankerLoad --}}
			<table class="table" width="75%" cellspacing="0" cellpadding="5" style="margin-top: 20px; font-size: 11px;">
				<thead>
					<tr>
						<th align="center"></th>
						<th align="center">Diesel</th>
						<th align="center">Regular</th>
						<th align="center">Premium</th>
					</tr>
				</thead>
				<tbody>

					{{-- Batangas --}}
					<?php
						$total_batangas_diesel = 0;
						$total_batangas_regular = 0;
						$total_batangas_premium = 0;
					?>
					@foreach($purchase->toBatangasLoads as $load)
					<tr>
						<?php
							$batangas_diesel = 0;
							$batangas_regular = 0;
							$batangas_premium = 0;
						?>
						@foreach($load->toBatangasLoadDetails as $detail)
							@if($detail['product']['name'] === 'Diesel')
								<?php
									$batangas_diesel += $detail['quantity'];
									$total_batangas_diesel += $detail['quantity'];
								?>
							@endif

							@if($detail['product']['name'] === 'Regular')
								<?php
									$batangas_regular += $detail['quantity'];
									$total_batangas_regular += $detail['quantity'];
								?>
							@endif

							@if($detail['product']['name'] === 'Premium')
								<?php
									$batangas_premium += $detail['quantity'];
									$total_batangas_premium += $detail['quantity'];
								?>
							@endif
						@endforeach

						<td align="left">
							{{ \App\Models\BatangasTransaction::where('id', $load->batangas_transaction_id)->exists() ? $load->batangasTransaction->trip_no .'- '. $load->batangasTransaction->driver->name : null }}
						</td>
						<td align="center">{{ number_format($batangas_diesel / 1000) }}</td>
						<td align="center">{{ number_format($batangas_regular / 1000) }}</td>
						<td align="center">{{ number_format($batangas_premium / 1000) }}</td>
					</tr>
					@endforeach

					{{-- Mindoro --}}
					<?php
						$total_mindoro_diesel = 0;
						$total_mindoro_regular = 0;
						$total_mindoro_premium = 0;
					?>
					@foreach($purchase->toMindoroLoads as $load)
					<tr>
						<?php
							$mindoro_diesel = 0;
							$mindoro_regular = 0;
							$mindoro_premium = 0;
						?>
						@foreach($load->toMindoroLoadDetails as $detail)
							@if($detail['product']['name'] === 'Diesel')
								<?php
									$mindoro_diesel += $detail['quantity'];
									$total_mindoro_diesel += $detail['quantity'];
								?>
							@endif

							@if($detail['product']['name'] === 'Regular')
								<?php
									$mindoro_regular += $detail['quantity'];
									$total_mindoro_regular += $detail['quantity'];
								?>
							@endif

							@if($detail['product']['name'] === 'Premium')
								<?php
									$mindoro_premium += $detail['quantity'];
									$total_mindoro_premium += $detail['quantity'];
								?>
							@endif
						@endforeach

						<td align="left">
							{{ \App\Models\MindoroTransaction::where('id', $load->mindoro_transaction_id)->exists() ? $load->mindoroTransaction->trip_no .'- '. $load->mindoroTransaction->driver->name : null }}
						</td>
						<td align="center">{{ number_format($mindoro_diesel / 1000) }}</td>
						<td align="center">{{ number_format($mindoro_regular / 1000) }}</td>
						<td align="center">{{ number_format($mindoro_premium / 1000) }}</td>
					</tr>
					@endforeach

					<?php
						$total_load_diesel = $total_batangas_diesel + $total_mindoro_diesel;
						$total_load_regular = $total_batangas_regular + $total_mindoro_regular;
						$total_load_premium = $total_batangas_premium + $total_mindoro_premium;
					?>

					<tr style="background-color: #eee; font-weight: bold;">
						<td>Total Load:</td>
						<td align="center" style="border-top: 1px solid #000; border-bottom: 1px double #000;">
							{{ number_format($total_load_diesel / 1000) }}
						</td>
						<td align="center" style="border-top: 1px solid #000; border-bottom: 1px double #000;">
							{{ number_format($total_load_regular / 1000) }}
						</td>
						<td align="center" style="border-top: 1px solid #000; border-bottom: 1px double #000;">
							{{ number_format($total_load_premium / 1000) }}
						</td>
					</tr>

					<?php
						$unlifted_diesel = $total_diesel_purchase_quantity - $total_load_diesel;
						$unlifted_regular = $total_regular_purchase_quantity - $total_load_regular;
						$unlifted_premium = $total_premium_purchase_quantity - $total_load_premium;
					?>

					@if ($unlifted_diesel !== 0 || $unlifted_regular !== 0 || $unlifted_premium !== 0)
						<tr style="color: red; font-style: italic;">
							<td>Unlifted:</td>
							<td align="center">
								{{ number_format($unlifted_diesel / 1000) }}
							</td>
							<td align="center">
								{{ number_format($unlifted_regular / 1000) }}
							</td>
							<td align="center">
								{{ number_format($unlifted_premium / 1000) }}
							</td>
						</tr>
					@endif
				</tbody>
			</table>

		</div>
		@endforeach
	</div>

</body>
</html>