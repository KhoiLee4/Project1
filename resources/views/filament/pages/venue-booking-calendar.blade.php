<x-filament-panels::page>
    <style>
        .calendar-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .calendar-header {
            background: #16a34a;
            color: white;
            padding: 20px 24px;
        }

        .calendar-header h2 {
            font-size: 24px;
            font-weight: 600;
            margin: 0 0 8px 0;
        }

        .calendar-header p {
            margin: 0;
            opacity: 0.9;
        }

        .legend-bar {
            background: #f0fdf4;
            padding: 16px 24px;
            display: flex;
            gap: 24px;
            align-items: center;
            flex-wrap: wrap;
            border-bottom: 1px solid #e5e7eb;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .legend-box {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 1px solid #d1d5db;
        }

        .legend-box.available {
            background: white;
            border-color: #d1d5db;
        }

        .legend-box.booked {
            background: #ef4444;
            border: none;
        }

        .legend-box.locked {
            background: #9ca3af;
            border: none;
        }

        .legend-box.event {
            background: #a855f7;
            border: none;
        }

        .info-note {
            background: #dcfce7;
            padding: 12px 24px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
            color: #166534;
        }

        .date-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
        }

        .date-navigation button {
            padding: 8px 16px;
            background: #16a34a;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.2s;
        }

        .date-navigation button:hover {
            background: #15803d;
        }

        .current-date-display {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }

        .calendar-wrapper {
            overflow-x: auto;
            padding: 0;
        }

        .calendar-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1400px;
        }

        .calendar-table thead th {
            background: #e0f2fe;
            padding: 12px 8px;
            border: 1px solid #cbd5e1;
            font-size: 12px;
            font-weight: 600;
            color: #0c4a6e;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .calendar-table thead th.ground-header {
            background: #dcfce7;
            color: #166534;
            width: 80px;
            position: sticky;
            left: 0;
            z-index: 11;
        }

        .calendar-table tbody td {
            border: 1px solid #e5e7eb;
            height: 50px;
            padding: 0;
            position: relative;
            text-align: center;
        }

        .calendar-table tbody td.ground-name {
            background: #f0fdf4;
            font-weight: 600;
            color: #166534;
            position: sticky;
            left: 0;
            z-index: 10;
            padding: 12px;
        }

        .calendar-table tbody td.available {
            background: white;
        }

        .calendar-table tbody td.booked {
            background: #ef4444;
            color: white;
            font-size: 11px;
            font-weight: 500;
            cursor: help;
        }

        .calendar-table tbody td.locked {
            background: #9ca3af;
            color: white;
        }

        .calendar-table tbody td.event {
            background: #a855f7;
            color: white;
        }

        .booking-cell {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 4px;
        }

        .booking-tooltip {
            position: absolute;
            background: #1f2937;
            color: white;
            padding: 10px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 1000;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            margin-bottom: 8px;
            display: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .booking-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: #1f2937;
        }

        .calendar-table tbody td.booked:hover .booking-tooltip,
        .calendar-table tbody td.event:hover .booking-tooltip {
            display: block;
        }

        .summary-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            padding: 24px;
            background: #f9fafb;
        }

        .summary-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .summary-card h3 {
            font-size: 14px;
            color: #6b7280;
            margin: 0 0 8px 0;
            font-weight: 500;
        }

        .summary-card p {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            color: #1f2937;
        }

        .summary-card.available p {
            color: #16a34a;
        }
    </style>

    <div class="calendar-container">
        <div class="calendar-header">
            <h2>Lịch Đặt Sân - {{ $venue->name }}</h2>
            <p>{{ $selectedDate->format('d/m/Y') }}</p>
        </div>

        <div class="legend-bar">
            <div class="legend-item">
                <div class="legend-box available"></div>
                <span>Trống</span>
            </div>
            <div class="legend-item">
                <div class="legend-box booked"></div>
                <span>Đã đặt</span>
            </div>
            <div class="legend-item">
                <div class="legend-box locked"></div>
                <span>Khóa</span>
            </div>
            <div class="legend-item">
                <div class="legend-box event"></div>
                <span>Sự kiện</span>
            </div>
        </div>

        <div class="info-note">
            Lưu ý: Nếu bạn cần đặt lịch cố định vui lòng liên hệ: 0374.857.068 để được hỗ trợ
        </div>

        <div class="date-navigation">
            <button wire:click="previousDay" type="button">← Ngày trước</button>
            <div class="current-date-display">{{ $selectedDate->format('d/m/Y') }}</div>
            <button wire:click="nextDay" type="button">Ngày sau →</button>
        </div>

        <div class="calendar-wrapper">
            <table class="calendar-table">
                <thead>
                    <tr>
                        <th class="ground-header">Sân</th>
                        @foreach($timeSlots as $time)
                            <th>{{ $time }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($grounds as $ground)
                        <tr>
                            <td class="ground-name">{{ $ground->name }}</td>
                            @php
                                $skipSlots = 0;
                            @endphp
                            @foreach($timeSlots as $timeSlot)
                                @if($skipSlots > 0)
                                    @php $skipSlots--; @endphp
                                    @continue
                                @endif

                                @php
                                    $status = $this->getBookingStatus($ground->id, $timeSlot);
                                @endphp

                                @if($status && $status['is_start'])
                                    @php
                                        $booking = $status['booking'];
                                        $slots = $status['slots'];
                                        $skipSlots = $slots - 1;
                                        $startTime = $status['start_time'];
                                        $endTime = $status['end_time'];
                                        
                                        $cellClass = 'booked';
                                        if ($booking->is_event) {
                                            $cellClass = 'event';
                                        }
                                    @endphp
                                    <td class="{{ $cellClass }}" colspan="{{ $slots }}">
                                        <div class="booking-cell">
                                            <span>{{ $startTime->format('H:i') }} - {{ $endTime->format('H:i') }}</span>
                                        </div>
                                        <div class="booking-tooltip">
                                            <strong>{{ $booking->user->name ?? 'Khách hàng' }}</strong><br>
                                            {{ $startTime->format('H:i') }} - {{ $endTime->format('H:i') }}<br>
                                            Trạng thái: {{ $booking->status }}
                                            @if($booking->is_event)
                                                <br>Sự kiện
                                            @endif
                                        </div>
                                    </td>
                                @elseif($status)
                                    @php $skipSlots--; @endphp
                                @else
                                    <td class="available"></td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="summary-section">
            <div class="summary-card">
                <h3>Tổng số sân</h3>
                <p>{{ $grounds->count() }}</p>
            </div>
            <div class="summary-card">
                <h3>Đặt sân hôm nay</h3>
                <p>{{ $bookings->count() }}</p>
            </div>
            <div class="summary-card available">
                <h3>Sân còn trống</h3>
                <p>{{ $grounds->count() - $bookings->pluck('ground_id')->unique()->count() }}</p>
            </div>
        </div>
    </div>
</x-filament-panels::page>
