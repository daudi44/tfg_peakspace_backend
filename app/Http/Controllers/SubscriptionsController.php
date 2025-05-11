<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;

class SubscriptionsController extends Controller
{
    // add Subscription
    public function addSubscription(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'category_id' => 'required|exists:categories,id',
        ]);

        $subscription = new Subscription();
        $subscription->name = $request->name;
        $subscription->amount = $request->amount;
        $subscription->start_date = $request->start_date;
        $subscription->end_date = $request->end_date;
        $subscription->category_id = $request->category_id;
        $subscription->user_id = auth()->id();
        $subscription->save();
        
        return response()->json([
            'message' => 'Subscription created successfully',
            'subscription' => $subscription,
        ], 201);
    }
    // edit Subscription
    // delete Subscription
    public function deleteSubscription(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|integer|exists:subscriptions,id',
        ]);

        $subscription = Subscription::find($request->subscription_id);
        if ($subscription->user_id != auth()->id()) {
            return response()->json(['message' => 'You are not authorized to delete this subscription.'], 403);
        }

        $subscription->delete();

        return response()->json(['message' => 'Subscription deleted successfully'], 200);
    }
    // get Subscriptions
    public function getSubscriptions(Request $request)
    {
        $subscriptions = Subscription::where('user_id', auth()->id())
            ->with(['category'])
            ->get();

        return response()->json($subscriptions, 200);
    }
}
