<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MovementsController extends Controller
{
    // add Movement
    public function addMovement(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'amount' => 'required|numeric',
            'category_id' => 'required|integer|exists:categories,id',
        ]);

        $movement = new Movement();
        $movement->name = $request->input('name');
        $movement->type = $request->input('type');
        $movement->amount = $request->input('amount');
        $movement->category_id = $request->input('category_id');
        $movement->user_id = $request->user()->id;
        $movement->save();

        if($movement->type == 1) {
            $request->user()->balance += $movement->amount;
            $request->user()->save();
        } else {
            $request->user()->balance -= $movement->amount;
            $request->user()->save();
        }

        return response()->json(['message' => 'Movement added successfully'], 201);
    }
    // edit Movement
    public function editMovement(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:movements,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'amount' => 'required|numeric',
            'category_id' => 'required|integer|exists:categories,id',
        ]);

        $movement = Movement::findOrFail($request->input('id'));
        $initialAmount = $movement->amount;
        $movement->name = $request->input('name');
        $movement->type = $request->input('type');
        $movement->amount = $request->input('amount');
        $movement->category_id = $request->input('category_id');
        $movement->save();

        if($movement->amount > $initialAmount) {
            $difference = $movement->amount - $initialAmount;
            if ($movement->type == 1) {
                $request->user()->balance += $difference;
                $request->user()->save();
            } else {
                $request->user()->balance -= $difference;
                $request->user()->save();
            }
        } else {
            $difference = $initialAmount - $movement->amount;
            if ($movement->type == 1) {
                $request->user()->balance -= $difference;
                $request->user()->save();
            } else {
                $request->user()->balance += $difference;
                $request->user()->save();
            }
        }

        return response()->json(['message' => 'Movement updated successfully'], 200);
    }
    // delete Movement
    public function deleteMovement(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:movements,id',
        ]);

        $movement = Movement::findOrFail($request->input('id'));
        $movement->delete();

        if($movement->type == 1) {
            $request->user()->balance -= $movement->amount;
            $request->user()->save();
        } else {
            $request->user()->balance += $movement->amount;
            $request->user()->save();
        }

        return response()->json(['message' => 'Movement deleted successfully'], 200);
    }
    // get movements by type
    public function getMovementsByType(Request $request)
    {
        $request->validate([
            'type' => 'required|integer',
        ]);

        $movements = Movement::where('user_id', $request->user()->id)
            ->where('type', $request->input('type'))
            ->get();

        return response()->json($movements, 200);
    }
    // get last 30 days movements
    public function getLast30DaysMovements(Request $request)
    {
        $movements = Movement::where('user_id', $request->user()->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        return response()->json($movements, 200);
    }
}
