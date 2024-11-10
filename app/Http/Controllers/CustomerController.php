<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Event;

class CustomerController extends Controller
{
    //customer
    public function addProject(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255'
        ]);

        $project = Project::create([
            'project_name' => $validated['project_name']
        ]);

        return response()->json(['message' => 'Project created successfully'], 201);
    }
    public function editProject(Request $request, $id)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255'
        ]);
        $project = Project::findOrFail($id);
        $project->project_name = $validated['project_name'];

        $project->save();
        return response()->json(['message' => 'Project updated successfully'], 200);
    }
    public function deleteProject($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json(['message' => 'Project Delete Successfully']);
    }
    public function addEvent(Request $request, $projectId)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        // dd($request->all());

        $event=Event::Create([
            'project_id' => $projectId,
            'event_name' => $validated['event_name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date']
        ]);

        return response()->json(['message' => 'Event created successfully'], 201);
    }
    public function editEvent(Request $request, $projectId ,$id)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        $project = Project::findOrFail($projectId);
        $event = Event::findOrFail($id);

        $event -> event_name = $validated['event_name'];
        $event -> start_date = $validated['start_date'];
        $event -> end_date = $validated['end_date'];

        $event->save();

        return response()->json(['message'=>'Event Update Successfully'],200);
    }
    public function deleteEvent($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json(['message'=>'Delete Event Successfully']);
    }
}
