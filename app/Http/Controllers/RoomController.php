<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;


class RoomController extends Controller
{
    public function index()
    {
        return view('rooms.index');
    }
    public function create()
    {
        return view('rooms.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'total_teams' => 'required|integer',
        ]);

        $room = Room::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'total_teams' => $request->total_teams,
        ]);

        return redirect()->route('rooms.setup', $room)
            ->with('success', 'ルームを作成しました。');
    }
    public function setup(Room $room)
    {
        $teams = $room->teams ?? collect();

        return view('rooms.setup', compact('room', 'teams'));
    }

    public function storeTeams(Request $request)
    {
        $room = Room::findOrFail($request->route('room'));

        $request->validate([
            'teams' => 'required|array',
            'teams.*' => 'required|string|max:255',
        ]);

        foreach ($request->teams as $teamName) {
            $room->teams()->create([
                'name' => $teamName,
            ]);
        }
        return redirect()->route('rooms.index')->with('success', 'チームを作成しました。');
    }
}
