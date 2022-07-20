<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\CalendarEvent;
use App\Holiday;
use App\WorkingShift;
use App\WorkingShiftItem;
use Calendar;

class StaffTimetableController extends Controller
{
    public function getView()
    {
        $events = [];

        $calendarEvents = CalendarEvent::all();
        foreach($calendarEvents as $calendarEvent) {
            $events[] = Calendar::event(

            );
        }

        $calendar = \Calendar::addEvents($events) //add an array with addEvents
        ->setOptions([ //set fullcalendar options
            'firstDay' => 1
        ]);


        return view('staff-timetable', compact('calendar'));
    }

    public function syncWorkingShiftCalendarEvent(WorkingShift $workingShift)
    {
        $workingShiftItems = $workingShift->workingShiftItems();

        if($workingShiftItems) {
            foreach($workingShiftItems as $workingShiftItem) {
                $calendarEvent = CalendarEvent::firstOrNew([
                        'working_shift_item_id', $workingShiftItem->id
                    ]);
                $calendarEvent->title = $workingShiftItem->label;
                $calendarEvent->start_date = $workingShiftItem->start_date;
                $calendarEvent->end_date = $workingShiftItem->end_date;
                $calendarEvent->save();
            }
        }
    }


}
