<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $monthlyMindoroTransaction->month }} {{ $monthlyMindoroTransaction->year }} - Mindoro Transactions</title>
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

	<h2 style="font: 13px sans-serif; margin-bottom: 20px; color: red; text-transform: uppercase;" align="center">Mindoro - {{ $monthlyMindoroTransaction->month }} {{ $monthlyMindoroTransaction->year }}</h2>

	{{-- Lists --}}
	<div style="font: 9px sans-serif;" class="row">
		<div class="column">
			<h2 style="font-size: 9px; line-height: .8; text-transform: uppercase;">Summary</h2>
			<hr style="margin-right: 5px; margin-bottom: 25px;">

			<?php $overall = 0; ?>

			{{-- Transactions  --}}
			@foreach ($transactions as $transaction)
				<?php
					$total_transactions_quantity = 0;
					$total_transactions_amount = 0;
				?>

				@foreach($transaction['details'] as $detail)
					<?php
						$amount = $detail['quantity'] * $detail['unit_price'];
						$total_transactions_amount += $amount;
						$total_transactions_quantity += $detail['quantity'];
					?>
				@endforeach

				<?php $total_load_amount = 0; ?>

				{{-- TankerLoad --}}
				@foreach($transaction['tanker_loads'] as $load)
					@foreach($load['tanker_load_details'] as $detail)
						<?php
							$amount = $detail['quantity'] * $detail['unit_price'];
							$total_load_amount += $amount;
						?>
					@endforeach
				@endforeach

				<?php $output = $total_transactions_amount - $total_load_amount - $transaction['expense'] ?>
				<table class='table' width="60%" cellspacing='0' style="margin-top: 5px">
					<tr>
						<td style="background-color: #fff; color: #000;">
							{{$transaction['trip_no']}}- {{$transaction['driver']['name']}}
						</td>
						<td align="right">
							{{ number_format($output) }}
						</td>
					</tr>
				</table>

				<?php $overall += $output ?>
			@endforeach

			<table class='table' width="60%" cellspacing='0' cellpadding="4" style="margin-top: 30px;">
				<tr>
					<td>
						<b>Total:</b>
					</td>
					<td align="right" style="border-top: 1px solid #000; border-bottom: 1px solid #000; background-color: #eee;">
						<b>{{ number_format($overall) }}</b>
					</td>
				</tr>
			</table>

		</div>
		<div class="column">
			<h2 style="font-size: 9px; line-height: .8; text-transform: uppercase;">Trips</h2>
			<hr>
			{{-- Drivers --}}
			<table class='table' width="100%" cellpadding='2' style="margin-top: 25px">
				<?php
					$total_trips = 0;
				?>
				<thead>
					<tr>
						<th>Driver</th>
						<th>Trip No.</th>
						<th>Total</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($driverTrips as $driver => $trip)
					<?php
						$total_trips += count($trip);
					?>
					<tr>
						<td>{{ $driver }}</td>
						<td>
{{-- 						@foreach ($trip as $key => $value)
							{{ $value['trip_no'] }}
						@endforeach
						--}}
							{{ collect($trip)->implode('trip_no', '-') }}
						</td>
						<td align="center">{{ count($trip) }}</td>
					</tr>
					@endforeach
					<tr>
						<td></td>
						<td></td>
						<td align="center" style="font-weight: bold; border-top: 1px solid #000; border-bottom: 1px solid #000; background-color: #eee;">{{ $total_trips }}</td>
					</tr>
				</tbody>
			</table>

			{{-- Helpers --}}
			<table class='table' width="100%" cellpadding='2' style="margin-top: 25px">
				<?php
					$total_trips = 0;
				?>
				<thead>
					<tr>
						<th>Helper</th>
						<th>Trip No.</th>
						<th>Total</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($helperTrips as $helper => $trip)
					<?php
						$total_trips += count($trip);
					?>
					<tr>
						<td>{{ $helper }}</td>
						<td>
							{{ collect($trip)->implode('trip_no', '-') }}
						</td>
						<td align="center">{{ count($trip) }}</td>
					</tr>
					@endforeach
					<tr>
						<td></td>
						<td></td>
						<td align="center" style="font-weight: bold; border-top: 1px solid #000; border-bottom: 1px solid #000; background-color: #eee;">{{ $total_trips }}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

<div class="page-break"></div>

{{-- Tables --}}
<div style="font: 9px sans-serif;">
	@foreach ($transactions as $transaction)
	<div style="margin-bottom: 25px;">
		{{-- Transaction --}}
		<table width="40%" cellspacing="0" cellpadding="5">
			<tr style="background-color: red; color: #fff;">
				<td>
					<b>
						<span style="padding: 5px;">{{$transaction['tanker']['plate_no']}}</span>
						<span style="background-color: #fff; color: #000; padding: 5px;">{{$transaction['trip_no']}} - {{$transaction['driver']['name']}}</span>
						<span style="padding: 5px;">{{$transaction['helper']['name']}}</span>
					</b>
				</td>
			</tr>
		</table>

		{{-- Transaction Details --}}
		<?php
		$total_transactions_quantity = 0;
		$total_transactions_amount = 0;
		?>
		<table class='table' width="100%" cellspacing='0' cellpadding='5' style="margin-top: 10px">
			@foreach($transaction['details'] as $detail)
			<?php
			$amount = $detail['quantity'] * $detail['unit_price'];
			$total_transactions_amount += $amount;
			$total_transactions_quantity += $detail['quantity'];
			?>
			<tr>
				<td align="left">{{ $detail['date'] }}</td>
				<td align="left">{{ $detail['client']['name'] }}</td>
				<td align="left">{{ $detail['product']['name'] }}</td>
				<td align="right">{{ number_format($detail['quantity']) }}</td>
				<td align="right">{{ $detail['unit_price'] }}</td>
				<td align="right">{{ number_format($amount) }}</td>
				<td align="right" style="color: red;"><b>{{ $detail['dr_no'] }}</b></td>
			</tr>
			@endforeach
			<tr style="background-color: #eee;">
				<td></td>
				<td></td>
				<td></td>
				<td align="right" style="border-top: 1px solid black; color: green;"><b>{{ number_format($total_transactions_quantity) }}</b></td>
				<td></td>
				<td align="right" style="border-top: 1px solid black;"><b>{{ number_format($total_transactions_amount) }}</b></td>
				<td></td>
			</tr>
		</table>

		<div class="row" style="margin-top: 5px">
			<?php $total_load_amount = 0; ?>
			{{-- TankerLoad --}}
			<div class="column">
				@foreach($transaction['tanker_loads'] as $load)
				<div style="margin: 10px 0 5px 0; color: red;">
					<b>{{ $load['purchase'] }}</b>
				</div>

				{{-- TankerLoadDetail --}}
				<table class='table' width="50%" cellspacing='0' cellpadding='5'>
					@foreach($load['tanker_load_details'] as $detail)
					<?php
					$amount = $detail['quantity'] * $detail['unit_price'];
					$total_load_amount += $amount;
					?>
					<tr>
						<td>{{ $detail['product']['name'] }}</td>
						<td>{{ number_format($detail['quantity']) }}</td>
						<td>{{ $detail['unit_price'] }}</td>
						<td>{{ number_format($amount) }}</td>
					</tr>
					@endforeach
				</table>
				@endforeach
			</div>

			<div class="column" style="margin-top: 15px;">
				<table class='table' width="40%" cellspacing='0' cellpadding='5' style="margin-top: 5px">
					<tr>
						<td align="right"> {{ number_format($total_transactions_amount) }} </td>
					</tr>
					<tr>
						<td align="right"> {{ number_format($total_load_amount) }} </td>
					</tr>
					<tr>
						<td align="right"> {{ number_format($transaction['expense']) }} </td>
					</tr>
					<tr>
						<td align="right" style="font-weight: bold; background-color: #eee;"> {{ number_format($total_transactions_amount - $total_load_amount - $transaction['expense']) }} </td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	@endforeach
</div>

</body>
</html>