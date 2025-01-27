<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class EquipmentNotification extends Controller
{
    public function showNotifications()
    {
        $notifications = Auth::user()->notifications; // Get all notifications for the user

        // Filter notifications by type
        $equipmentNotifications = $notifications->where('type', 'App\Notifications\EquipmentNotification');
        $supplyNotifications = $notifications->where('type', 'App\Notifications\ConsumablesNotification');

      
        return view('components.inventory-notification', compact('equipmentNotifications','supplyNotifications'));
    }
}

