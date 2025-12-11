<div>
    <x-filament-panels::page>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 16px;
        }
        
        .legend {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px 30px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .legend-box {
            width: 24px;
            height: 24px;
            border-radius: 4px;
        }
        
        .booked {
            background: #ef4444;
        }
        
        .available {
            background: #dcfce7;
            border: 2px solid #22c55e;
        }
        
        .date-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px;
            background: white;
            border-bottom: 1px solid #e9ecef;
        }
        
        .date-nav button {
            padding: 10px 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .date-nav button:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }
        
        .date-nav .current-date {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
        }
        
        .calendar-wrapper {
            padding: 30px;
            overflow-x: auto;
        }
        
        .calendar {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
        }
        
        .calendar th {
            background: #f8f9fa;
            padding: 12px 8px;
            border: 1px solid #e9ecef;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .calendar th.ground-col {
            width: 80px;
            background: #667eea;
            color: white;
        }
        
        .calendar td {
            border: 1px solid #e9ecef;
            height: 60px;
            position: relative;
        }
        
        .calendar td.ground-name {
            background: #f8f9fa;
            font-weight: 600;
            text-align: center;
            color: #1f2937;
        }
        
        .calendar td.available {
            background: #dcfce7;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .calendar td.available:hover {
            background: #bbf7d0;
            transform: scale(1.02);
        }
        
        .calendar td.booked {
            background: #ef4444;
            color: white;
            text-align: center;
            font-size: 11px;
            font-weight: 600;
            cursor: help;
            padding: 4px;
        }
        
        .booking-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
        }
        
        .tooltip {
            position: absolute;
            background: #1f2937;
            color: white;
            padding: 12px;
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
        
        .calendar td.booked:hover .tooltip {
            display: block;
        }
        
        .tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: #1f2937;
        }
        
        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 30px;
            background: #f8f9fa;
        }
        
        .summary-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .summary-card h3 {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 8px;
        }
        
        .summary-card p {
            font-size: 32px;
            font-weight: bold;
            color: #1f2937;
        }
        
        .summary-card.available p {
            color: #22c55e;
        }
    </style>

    <div class="container">
        <div class="header">
            <h1>Lịch Đặt Sân - Badminton Club Cau Giay</h1>
            <p id="currentDate">{{ $selectedDate->format('d/m/Y') }}</p>
        </div>
        
        <div class="legend">
            <div class="legend-item">
                <div class="legend-box booked"></div>
                <span>Đã đặt</span>
            </div>
            <div class="legend-item">
                <div class="legend-box available"></div>
                <span>Trống</span>
            </div>
            <div class="legend-item">
                <span style="font-weight: 600;">📞 Liên hệ: 0374.857.068</span>
            </div>
        </div>
        
        <div class="date-nav" wire:ignore.self>
            <button wire:click="previousDay">← Ngày trước</button>
            <div class="current-date" id="displayDate">{{ $selectedDate->format('d/m/Y') }}</div>
            <button wire:click="nextDay">Ngày sau →</button>
        </div>

        <div class="calendar-wrapper">
            <table class="calendar">
                <thead>
                    <tr>
                        <th class="ground-col">Sân</th>
                        @foreach($timeSlots as $time)
                            <th>{{ $time }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($grounds as $ground)
                        <tr>
                            <td class="ground-name">{{ $ground->name }}</td>
                            @php $skip = 0; @endphp
                            @foreach($timeSlots as $time)
                                @if($skip > 0)
                                    @php $skip--; continue; @endphp
                                @endif

                                @php
                                    $booking = $this->getBookingForGroundAtTime($ground->id, $time);
                                @endphp

                                @if($booking && $this->isBookingStart($ground->id, $time))
                                    @php
                                        $start = is_string($booking->start_time) ? \Carbon\Carbon::parse($booking->start_time) : $booking->start_time;
                                        $end = is_string($booking->end_time) ? \Carbon\Carbon::parse($booking->end_time) : $booking->end_time;
                                        $slots = max(1, intval($end->diffInMinutes($start) / 30));
                                        $skip = $slots - 1;
                                    @endphp
                                    <td class="booked" colspan="{{ $slots }}">
                                        <div class="booking-info">
                                            <div>{{ $start->format('H:i') }} - {{ $end->format('H:i') }}</div>
                                        </div>
                                        <div class="tooltip">
                                            <strong>{{ $booking->customer_name ?? ($booking->user->name ?? 'Khách hàng') }}</strong><br>
                                            {{ $start->format('H:i') }} - {{ $end->format('H:i') }}<br>
                                            Trạng thái: {{ $booking->status }}
                                        </div>
                                    </td>
                                @elseif($booking)
                                    {{-- part of a spanning booking, skip rendering here --}}
                                @else
                                    <td class="available"></td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="summary">
            <div class="summary-card">
                <h3>Tổng số sân</h3>
                <p id="totalGrounds">{{ $grounds->count() }}</p>
            </div>
            <div class="summary-card">
                <h3>Đặt sân hôm nay</h3>
                <p id="totalBookings">{{ $bookings->count() }}</p>
            </div>
            <div class="summary-card available">
                <h3>Sân còn trống</h3>
                <p id="availableGrounds">{{ $grounds->count() - $bookings->pluck('ground_id')->unique()->count() }}</p>
            </div>
        </div>
    </div>

    <script>
        // The calendar is rendered server-side using the Page's properties/methods.
        // JavaScript is kept minimal since the page is powered by Filament/Livewire.
        document.addEventListener('DOMContentLoaded', function () {
            // Optional: add client-side interactivity here later.
        });
    </script>
    </x-filament-panels::page>
</div>