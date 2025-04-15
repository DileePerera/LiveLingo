<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MeetingController extends Controller
{
    public function create()
    {
        return view('meeting.create');
    }

    public function store(Request $request)
    {
        try{
            $validated = $request->validate([
                'host_id' => 'required',
                'start_date' => 'required|date',
                'start_time' => 'required',
                'description' => 'nullable|string',
                'password' => 'nullable',
            ]);
    
            $meetingLink = 'meeting-' . uniqid(); // Generate a unique meeting link
    
            $meeting = Meeting::create([
                'meeting_link' => $meetingLink,
                'host_id' => $validated['host_id'],
                'start_date' => $validated['start_date'],
                'start_time' => $validated['start_time'],
                'description' => $validated['description'],
                'password' => $validated['password'],
            ]);
    
            return redirect()->route('dashboard')->with(['success' => 'Meeting created', 'meeting_link' => $meetingLink]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            return redirect()->back()->withErrors($errors)->withInput()->with('error', $errors);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['stack' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function start($id)
    {
        $meeting = Meeting::where('meeting_link', $id)->first();   
        // dd($meeting);
        return view('meeting.start', compact('meeting'));
    }
}
