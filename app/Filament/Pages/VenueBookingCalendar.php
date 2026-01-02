<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use App\Models\Venue;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;

class VenueBookingCalendar extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'venue-booking-calendar';

    protected string $view = 'filament.pages.venue-booking-calendar';

    public Venue $venue;
    
    public Carbon $selectedDate;
    
    public Collection $bookings;
    public Collection $grounds;
    public array $timeSlots = [];

    public function mount(): void
    {
        $venueId = request()->query('venue');
        if (!$venueId) {
            abort(404, 'Venue not found');
        }
        
        $this->venue = Venue::with('grounds')->findOrFail($venueId);
        $this->selectedDate = Carbon::parse(request()->query('date', now()->toDateString()));
        $this->grounds = $this->venue->grounds()->orderBy('name')->get();
        
        $this->loadBookings();
        $this->generateTimeSlots();
    }

    public function getTitle(): string
    {
        return "Lịch đặt sân - {$this->venue->name} ({$this->selectedDate->format('d/m/Y')})";
    }

    public function updatedSelectedDate(): void
    {
        $this->loadBookings();
    }

    private function loadBookings(): void
    {
        $this->bookings = Booking::whereHas('ground', function ($query) {
            $query->where('venue_id', $this->venue->id);
        })
            ->where('date', $this->selectedDate->toDateString())
            ->where('status', '!=', 'Cancelled')
            ->with(['user', 'ground', 'event'])
            ->get();
    }

    private function generateTimeSlots(): void
    {
        $this->timeSlots = [];
        $start = Carbon::createFromTimeString('06:00');
        $end = Carbon::createFromTimeString('22:00');
        
        while ($start <= $end) {
            $this->timeSlots[] = $start->format('H:i');
            $start->addMinutes(30);
        }
    }

    public function getBookingStatus($groundId, $timeSlot): ?array
    {
        $timeObj = Carbon::createFromTimeString($timeSlot);
        
        $booking = $this->bookings->first(function ($booking) use ($groundId, $timeObj) {
            if ($booking->ground_id !== $groundId) {
                return false;
            }
            
            $startTime = is_string($booking->start_time) 
                ? Carbon::createFromTimeString($booking->start_time)
                : Carbon::parse($booking->start_time);
            $endTime = is_string($booking->end_time)
                ? Carbon::createFromTimeString($booking->end_time)
                : Carbon::parse($booking->end_time);
            
            return $timeObj >= $startTime && $timeObj < $endTime;
        });

        if (!$booking) {
            return null;
        }

        $startTime = is_string($booking->start_time) 
            ? Carbon::createFromTimeString($booking->start_time)
            : Carbon::parse($booking->start_time);
        $endTime = is_string($booking->end_time)
            ? Carbon::createFromTimeString($booking->end_time)
            : Carbon::parse($booking->end_time);

        $isStart = $timeObj->format('H:i') === $startTime->format('H:i');
        $duration = $endTime->diffInMinutes($startTime);
        $slots = max(1, intval($duration / 30));

        return [
            'booking' => $booking,
            'is_start' => $isStart,
            'slots' => $slots,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
    }

    public function previousDay(): void
    {
        $this->selectedDate = $this->selectedDate->copy()->subDay();
        $this->loadBookings();
    }

    public function nextDay(): void
    {
        $this->selectedDate = $this->selectedDate->copy()->addDay();
        $this->loadBookings();
    }

    public function goToDate($date): void
    {
        $this->selectedDate = Carbon::parse($date);
        $this->loadBookings();
    }
}
