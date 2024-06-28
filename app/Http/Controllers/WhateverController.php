<?php

namespace App\Http\Controllers;

use App\Models\Convention;
use App\Models\Member;
use App\Models\Participation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WhateverController extends Controller
{
    public function listParticipants(Convention $convention) {
        // $parts = Participation::where('convention_id', $convention->id)->get();
        // $parts->load('member');

        //lazy loading

        // return view('list-participants',[
        //     'parts' => $parts
        // ]);

        //Query Builder approach
        $parts = DB::table('participations')
            ->join('members', 'members.id','=','participations.member_id')
            ->where('participations.convention_id', $convention->id)
            ->select('members.last_name','members.first_name')
            ->get();

        return response()->json($parts);
    }

    public function attended($memberId) {
        $convs = DB::table('conventions')
                ->join('participations','participations.convention_id','=','conventions.id')
                ->where('participations.member_id', $memberId)
                ->select('conventions.title','conventions.venue','conventions.date_from')
                ->get();

        return response()->json($convs);
    }

    public function listParticipationsForMember($memberId)
    {
        // Execute the SQL query
        $participations = DB::table('participations')
                            ->select('participations.type', 'conventions.id', 'participations.convention_id')
                            ->join('conventions', 'participations.convention_id', '=', 'conventions.id')
                            ->where('participations.member_id', $memberId)
                            ->get();
        
        // Initialize an empty array to store the formatted data
        $formattedParticipations = [];
        
        // Iterate over each participation entry
        foreach ($participations as $participation) {
            // Retrieve convention details for each participation entry
            $convention = Convention::find($participation->convention_id);
            
            // Create a new array with the desired structure
            $formattedParticipation = [
                'type' => $participation->type,
                'title' => $convention->title,
                'venue' => $convention->venue,
                'date_from' => $convention->date_from,
            ];
            
            // Append the formatted participation to the array
            $formattedParticipations[] = $formattedParticipation;
        }
        
        // Return the formatted data as JSON
        return response()->json($formattedParticipations);
    }

}
