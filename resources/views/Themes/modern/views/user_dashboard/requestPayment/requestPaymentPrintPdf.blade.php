<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>{{ __('Print') }}</title>
</head>
<style>
body{ font-family: 'Lato', sans-serif; color:#121212;}

hr { border-top:1px solid #f0f0f0;}
table { border-collapse:collapse;}
.code td{ padding:5px;}

</style>
<body>
	<div style="width:900px; margin:auto; top:20px; position:relative;">
		<table style="margin-bottom:40px;">
			<tr>
				<td>
					{!! getSystemLogo() !!}
				</td>
			</tr>
		</table>

		@if ($transactionDetails->transaction_type_id == Request_From)
			<table>
				<tr>
					<td>
						<table>
							<tr>
								<td style="font-size: 16px; color:#000000; line-height:25px; font-weight:bold;">@lang('message.form.name')</td>
							</tr>
							<tr>
								<td style="font-size:15px; color:#4e5c6e; line-height:22px;">
									{{ $transactionDetails->user_type == 'registered' ? $transactionDetails->end_user->first_name.' '.$transactionDetails->end_user->last_name : $transactionDetails->email }}
								</td>
							</tr>
							<br><br>
							<tr>
								<td style="font-size:16px; color:#000000; line-height:25px; font-weight:bold;">@lang('message.dashboard.left-table.transferred.paid-with')</td>
							</tr>
							<tr>
								<td style="font-size:15px; color:#4e5c6e; line-height:22px;">{{$transactionDetails->currency->code}}</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<td>
						<table style="margin-top:20px;">
							<tr>
								<td style="font-size:16px; color:#000000; line-height:25px; font-weight:bold;">@lang('message.dashboard.left-table.transaction-id')</td>
							</tr>
							<tr>
								<td style="font-size:15px; color:#4e5c6e; line-height:22px;">{{$transactionDetails->uuid}}</td>
							</tr>
							<br><br>
							<tr>
								<td style="font-size:16px; color:#000000; line-height:25px; font-weight:bold;">@lang('message.dashboard.left-table.transaction-date')</td>
							</tr>
							<tr>
								<td style="font-size:15px; color:#4e5c6e; line-height:22px;">{{ dateFormat($transactionDetails->created_at) }}</td>
							</tr>
							<br><br>
							<tr>
								<td style="font-size:16px; color:#000000; line-height:25px; font-weight:bold;">@lang('message.form.status')</td>
							</tr>
							<tr>
								<td style="font-size:15px; color:#4e5c6e; line-height:22px;">{{ (($transactionDetails->status == 'Blocked') ? __("Cancelled") :(($transactionDetails->status == 'Refund') ? __("Refunded") : __($transactionDetails->status))) }}</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<td>
						<table style="margin-top:20px; width:300px;">
							<tr>
								<td colspan="2" style="font-size:16px; color:#000000; font-weight:bold;">@lang('message.dashboard.left-table.details')</td>
							</tr>
							<tr>
								<td style="font-size:15px; color:#000000;">@lang('message.dashboard.send-request.request.confirmation.requested-amount')</td>
								<td style="font-size:15px; color:#4e5c6e; text-align:right;">{{ moneyFormat(optional($transactionDetails->currency)->symbol, formatNumber($transactionDetails->subtotal, $transactionDetails->currency->id)) }}</td>
							</tr>
							<tr>
								<td colspan="2" style="border-top:1px solid #eaeaea; padding-top:0; margin-bottom:3px;"></td>
							</tr>
							<tr>
								<td style="font-size:15px; color:#000000; font-weight:bold;">@lang('message.dashboard.left-table.total')</td>
								<td style="font-size:15px; color:#4e5c6e; text-align:right; font-weight:bold;">
									{{ moneyFormat(optional($transactionDetails->currency)->symbol, (str_replace("-",'', formatNumber($transactionDetails->total, $transactionDetails->currency->id))) ) }}
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<td>
						<table style="margin-top:40px;">
							<tr>
								<td style="font-size:16px; color:#000000; line-height:25px; font-weight:bold;">@lang('message.dashboard.left-table.transferred.note')</td>
							</tr>
							<tr>
								<td style="font-size:15px; color:#4e5c6e; line-height:22px;">{{$transactionDetails->note}}</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		@else
			<table>
				<tr>
					<td>
						<table>
							<tr>
								<td style="font-size: 16px; color:#000000; line-height:25px; font-weight:bold;">@lang('message.form.name')</td>
							</tr>
							<tr>
								<td style="font-size:15px; color:#4e5c6e; line-height:22px;">
									{{($transactionDetails['user_type']=='registered'?$transactionDetails->end_user->first_name.' '.$transactionDetails->end_user->last_name:$transactionDetails['email'])}}
								</td>
							</tr>
							<br><br>

							<tr>
								<td style="font-size:16px; color:#000000; line-height:25px; font-weight:bold;">@lang('message.dashboard.left-table.transferred.paid-with')</td>
							</tr>
							<tr>
								<td style="font-size:15px; color:#4e5c6e; line-height:22px;">{{$transactionDetails->currency->code}}</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<td>
						<table style="margin-top:20px;">
							<tr>
								<td style="font-size:16px; color:#000000; line-height:25px; font-weight:bold;">@lang('message.dashboard.left-table.transaction-id')</td>
							</tr>
							<tr>
								<td style="font-size:15px; color:#4e5c6e; line-height:22px;">{{$transactionDetails->uuid}}</td>
							</tr>
							<br><br>
							<tr>
								<td style="font-size:16px; color:#000000; line-height:25px; font-weight:bold;">@lang('message.dashboard.left-table.transaction-date')</td>
							</tr>
							<tr>
								<td style="font-size:15px; color:#4e5c6e; line-height:22px;">{{ dateFormat($transactionDetails->created_at) }}</td>
							</tr>

							<br><br>

							<tr>
								<td style="font-size:16px; color:#000000; line-height:25px; font-weight:bold;">@lang('message.form.status')</td>
							</tr>
							<tr>
								<td style="font-size:15px; color:#4e5c6e; line-height:22px;">{{ (($transactionDetails->status == 'Blocked') ? __("Cancelled") :(($transactionDetails->status == 'Refund') ? __("Refunded") :
								__($transactionDetails->status))) }}</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr>
					<td>
						<table style="margin-top:20px; width:300px;">
							<tr>
								<td colspan="2" style="font-size:16px; color:#000000; font-weight:bold;">@lang('message.dashboard.left-table.details')</td>
							</tr>
							<tr>
								<td style="font-size:15px; color:#000000;">@lang('message.dashboard.send-request.request.confirmation.requested-amount')</td>
								<td style="font-size:15px; color:#4e5c6e; text-align:right;">{{ moneyFormat(optional($transactionDetails->currency)->symbol, formatNumber($transactionDetails->subtotal, $transactionDetails->currency->id)) }}</td>
							</tr>

							@if (abs($transactionDetails->total) - abs($transactionDetails->subtotal) > 0)
								<tr style="padding-bottom:10px;">
									<td style="font-size:15px; color:#000000;">@lang('message.dashboard.left-table.fee')</td>
									<td style="font-size:15px; color:#4e5c6e; text-align:right;">
										{{ moneyFormat(optional($transactionDetails->currency)->symbol, formatNumber($transactionDetails->charge_percentage + $transactionDetails->charge_fixed, $transactionDetails->currency->id)) }}
									</td>
								</tr>
							@endif

							<tr>
								<td colspan="2" style="border-top:1px solid #eaeaea; padding-top:0; margin-bottom:3px;"></td>
							</tr>
							<tr>
								<td style="font-size:15px; color:#000000; font-weight:bold;">@lang('message.dashboard.left-table.total')</td>
								<td style="font-size:15px; color:#4e5c6e; text-align:right; font-weight:bold;">
									{{ moneyFormat(optional($transactionDetails->currency)->symbol, str_replace("-",'', formatNumber($transactionDetails->total, $transactionDetails->currency->id))) }}
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<br><br>

				<tr>
					<td>
						<table>
							<tr>
								<td style="font-size:16px; color:#000000; line-height:25px; font-weight:bold;">@lang('message.dashboard.left-table.transferred.note')</td>
							</tr>
							<tr>
								<td style="font-size:15px; color:#4e5c6e; line-height:22px;">{{$transactionDetails->note}}</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		@endif
	</div>
</body>
</html>
