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

    protected string $view = 'filament.pages.venue-booking-calendar';

    public Venue $venue;
    
    public Carbon $selectedDate;
    
    public Collection $bookings;
    public Collection $grounds;
    public array $timeSlots = [];

    public function mount(): void
    {
        $venueId = request()->query('venue');
        $this->venue = Venue::findOrFail($venueId);
        $this->selectedDate = Carbon::parse(request()->query('date', now()->toDateString()));
        $this->grounds = $this->venue->grounds()->get();
        
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
            ->get();
    }

    private function generateTimeSlots(): void
    {
        // Generate time slots from 6:00 to 23:00 in 30-minute intervals
        $start = Carbon::createFromTimeString('06:00');
        $end = Carbon::createFromTimeString('23:00');
        
        while ($start <= $end) {
            $this->timeSlots[] = $start->format('H:i');
            $start->addMinutes(30);
        }
    }

    public function getBookingForGroundAtTime($groundId, $time): ?Booking
    {
        $timeObj = Carbon::createFromTimeString($time);
        
        return $this->bookings->first(function ($booking) use ($groundId, $timeObj) {
            if ($booking->ground_id !== $groundId) {
                return false;
            }
            
            $startTime = is_string($booking->start_time) 
                ? Carbon::createFromTimeString($booking->start_time)
                : $booking->start_time;
            $endTime = is_string($booking->end_time)
                ? Carbon::createFromTimeString($booking->end_time)
                : $booking->end_time;
            
            // Check if current time slot falls within the booking period
            return $timeObj >= $startTime && $timeObj < $endTime;
        });
    }

    public function isBookingStart($groundId, $time): bool
    {
        $booking = $this->getBookingForGroundAtTime($groundId, $time);
        if (!$booking) {
            return false;
        }
        
        $startTime = is_string($booking->start_time) 
            ? Carbon::createFromTimeString($booking->start_time)
            : $booking->start_time;
        
        $timeObj = Carbon::createFromTimeString($time);
        return $timeObj->format('H:i') === $startTime->format('H:i');
    }

    public function previousDay(): void
    {
        $this->selectedDate = $this->selectedDate->copy()->subDay();
    }

    public function nextDay(): void
    {
        $this->selectedDate = $this->selectedDate->copy()->addDay();
    }

    public function goToDate($date): void
    {
        $this->selectedDate = Carbon::parse($date);
    }
}
