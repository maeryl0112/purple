<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function redirectToAppointment($id)
    {
        $notification = auth()->user()->unreadNotifications->find($id);
    
        if ($notification) {
            $notification->markAsRead();
    
            // Determine the notification type
            if ($notification->type === 'App\Notifications\NewAppointmentNotification' && isset($notification->data['appointment_id'])) {
                return redirect()->route('manageappointments', ['appointment' => $notification->data['appointment_id']]);
            } 
            
            if ($notification->type === 'App\Notifications\ConsumablesNotification' && isset($notification->data['supply_id'])) {
                return redirect()->route('managesupplies', ['supply' => $notification->data['supply_id']]);
            }

            if ($notification->type === 'App\Notifications\EquipmentNotification' && isset($notification->data['equipment_id'])) {
                return redirect()->route('manageequipments', ['equipments' => $notification->data['equipment_id']]);
            }
    
            return back()->with('error', 'Invalid notification data.');
        }
    
        return back()->with('error', 'Notification not found.');
    }
    
   



    public function markAsRead($id)
    {
        $notification = auth()->user()->unreadNotifications->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return back();
    }

   
  
}
