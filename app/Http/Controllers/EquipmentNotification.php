<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;  
use Illuminate\Support\Facades\Auth;

class EquipmentNotification extends Controller
{
    public function showNotifications()
    {
        $user = Auth::user();

        if ($user->role_id === 2) {
            // Restrict employees to only see their assigned branch
            $branches = Branch::where('id', $user->branch_id)->get();
        } else {
            // Admin can see all branches
            $branches = Branch::all();
        }

        // Get notifications for the logged-in user
        $notifications = $user->notifications;

        // Filter notifications by type
        $equipmentNotifications = $notifications->where('type', 'App\Notifications\EquipmentNotification');
        $supplyNotifications = $notifications->where('type', 'App\Notifications\ConsumablesNotification');

        return view('components.inventory-notification', compact('equipmentNotifications', 'supplyNotifications', 'branches'));
    }
}
