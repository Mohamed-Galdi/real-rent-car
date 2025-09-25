<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reservation {{ $reservation->reservation_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: #111827; font-size: 12px; }
        .container { width: 100%; padding: 16px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .title { font-size: 18px; font-weight: bold; }
        .badge { display: inline-flex; align-items: center; gap: 6px; border-radius: 9999px; padding: 4px 10px; font-size: 11px; }
        .section { border: 1px solid #E5E7EB; border-radius: 6px; margin-bottom: 12px; }
        .section-header { border-bottom: 1px solid #E5E7EB; padding: 8px 12px; font-weight: 600; background: #F9FAFB; }
        .section-body { padding: 12px; }
        .grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
        .row { display: flex; justify-content: space-between; align-items: center; margin: 6px 0; }
        .label { color: #6B7280; font-size: 11px; }
        .value { font-weight: 600; }
        .mt-2 { margin-top: 8px; }
        .mt-4 { margin-top: 16px; }
        .text-right { text-align: right; }
        .totals .row { border-top: 1px solid #E5E7EB; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="title">Reservation {{ $reservation->reservation_number }}</div>
            @php
                $statusMap = collect($statusMeta)->keyBy('value');
                $meta = $statusMap[$reservation->status->value] ?? null;
                $hex = $meta['color'] ?? '#6B7280';
                $rgb = [
                    hexdec(substr(ltrim($hex, '#'), 0, 2)),
                    hexdec(substr(ltrim($hex, '#'), 2, 2)),
                    hexdec(substr(ltrim($hex, '#'), 4, 2)),
                ];
            @endphp
            <span class="badge" style="background: rgba({{ $rgb[0] }}, {{ $rgb[1] }}, {{ $rgb[2] }}, 0.1); color: {{ $hex }};">
                <span style="display:inline-block; width:6px; height:6px; background: {{ $hex }}; border-radius: 9999px;"></span>
                {{ $meta['label'] ?? ucfirst(str_replace('_',' ',$reservation->status->value)) }}
            </span>
        </div>

        <div class="section">
            <div class="section-header">Client</div>
            <div class="section-body">
                <div class="row"><span class="label">Name</span><span class="value">{{ $reservation->user->name ?? '—' }}</span></div>
                <div class="row"><span class="label">Email</span><span class="value">{{ $reservation->user->email ?? '—' }}</span></div>
            </div>
        </div>

        <div class="section">
            <div class="section-header">Car</div>
            <div class="section-body">
                <div class="row"><span class="label">Car</span><span class="value">@if($reservation->car) {{ $reservation->car->year }} {{ $reservation->car->make }} {{ $reservation->car->model }} @else — @endif</span></div>
                <div class="row"><span class="label">Plate</span><span class="value">{{ $reservation->car->license_plate ?? '—' }}</span></div>
            </div>
        </div>

        <div class="section">
            <div class="section-header">Reservation Details</div>
            <div class="section-body grid">
                <div>
                    <div class="label">Start</div>
                    <div class="value">{{ 
                        optional($reservation->start_date)->format('Y-m-d')
                    }} {{ optional($reservation->pickup_time)->format('H:i') }}</div>
                </div>
                <div>
                    <div class="label">End</div>
                    <div class="value">{{ 
                        optional($reservation->end_date)->format('Y-m-d')
                    }} {{ optional($reservation->return_time)->format('H:i') }}</div>
                </div>
                <div>
                    <div class="label">Duration</div>
                    <div class="value">{{ $reservation->total_days }} days</div>
                </div>
                <div>
                    <div class="label">Pickup Location</div>
                    <div class="value">{{ $reservation->pickup_location ?? '—' }}</div>
                </div>
                <div>
                    <div class="label">Return Location</div>
                    <div class="value">{{ $reservation->return_location ?? '—' }}</div>
                </div>
                @if($reservation->status === \App\Enums\ReservationStatus::CANCELLED)
                <div>
                    <div class="label">Cancelled At</div>
                    <div class="value">{{ optional($reservation->cancelled_at)->format('Y-m-d H:i') ?? '—' }}</div>
                    <div class="label mt-2">Reason</div>
                    <div class="value">{{ $reservation->cancellation_reason ?? '—' }}</div>
                </div>
                @endif
            </div>
        </div>

        <div class="section">
            <div class="section-header">Payments</div>
            <div class="section-body">
                @if($reservation->payments->count() === 0)
                    <div class="label">No payments recorded.</div>
                @else
                    <table style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th style="text-align:left; border-bottom: 1px solid #E5E7EB; padding: 6px 4px;">#</th>
                                <th style="text-align:left; border-bottom: 1px solid #E5E7EB; padding: 6px 4px;">Amount</th>
                                <th style="text-align:left; border-bottom: 1px solid #E5E7EB; padding: 6px 4px;">Method</th>
                                <th style="text-align:left; border-bottom: 1px solid #E5E7EB; padding: 6px 4px;">Status</th>
                                <th style="text-align:left; border-bottom: 1px solid #E5E7EB; padding: 6px 4px;">Processed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservation->payments as $p)
                                <tr>
                                    <td style="padding: 6px 4px; border-bottom: 1px solid #F3F4F6;">{{ $p->payment_number }}</td>
                                    <td style="padding: 6px 4px; border-bottom: 1px solid #F3F4F6;">{{ $currency }}{{ number_format((float)$p->amount, 2) }}</td>
                                    <td style="padding: 6px 4px; border-bottom: 1px solid #F3F4F6;">{{ $p->payment_method->value ?? $p->payment_method }}</td>
                                    <td style="padding: 6px 4px; border-bottom: 1px solid #F3F4F6;">{{ ucfirst(str_replace('_',' ', $p->status->value ?? $p->status)) }}</td>
                                    <td style="padding: 6px 4px; border-bottom: 1px solid #F3F4F6;">{{ optional($p->processed_at)->format('Y-m-d H:i') ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <div class="section">
            <div class="section-header">Totals</div>
            <div class="section-body">
                <div class="row"><span class="label">Daily Rate</span><span class="value">{{ $currency }}{{ number_format((float)$reservation->daily_rate, 2) }}</span></div>
                <div class="row"><span class="label">Subtotal</span><span class="value">{{ $currency }}{{ number_format((float)$reservation->subtotal, 2) }}</span></div>
                <div class="row"><span class="label">Tax</span><span class="value">{{ $currency }}{{ number_format((float)$reservation->tax_amount, 2) }}</span></div>
                <div class="row"><span class="label">Discount</span><span class="value">-{{ $currency }}{{ number_format((float)$reservation->discount_amount, 2) }}</span></div>
                <div class="row totals"><span class="label">Total</span><span class="value">{{ $currency }}{{ number_format((float)$reservation->total_amount, 2) }}</span></div>
            </div>
        </div>
    </div>
</body>
</html>
