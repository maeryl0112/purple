<?php

namespace App\Http\Controllers;

use App\Jobs\SendAppointmentConfirmationMailJob;
use App\Notifications\NewAppointmentNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\Payment;
use App\Models\User;
use App\Models\Appointment;
use App\Services\TwilioService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        // Get the cart of the user that is not paid
        $cart = auth()->user()->cart()->where('is_paid', false)->first();
        return view('web.cart', compact('cart'));
    }

    public function removeItem($cart_service_id)
    {
        // Get the cart of the user that is not paid
        $cart = auth()->user()->cart()->where('is_paid', false)->first();

        // If the cart is not found, redirect back
        if (!$cart) {
            return redirect()->back();
        }

        // Get the cart_service with id = cart_service_id
        $cart_service = DB::table('cart_service')->where('id', $cart_service_id)->where('cart_id', $cart->id)->first();

        // If the cart service is not found, redirect back
        if (!$cart_service) {
            return redirect()->back();
        }

        // Delete the cart service
        DB::table('cart_service')->where('id', $cart_service_id)->where('cart_id', $cart->id)->delete();

        // Update the total
        $cart->total = $cart->services()->sum('cart_service.price');
        $cart->save();

        return redirect()->back();
    }


    public function checkout(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,online',
            'last_four_digits' => 'required_if:payment_method,online|digits:4',
        ]);
    
        // Get the user's cart that is not paid
        $cart = auth()->user()->cart()->where('is_paid', false)->first();
    
        if (!$cart) {
            return redirect()->back()->withErrors(['error' => 'Cart not found.']);
        }
    
        // Check for employee availability
        $is_employees_available = true;
        $unavailable_employees = collect();
    
        foreach ($cart->services as $service) {
            $is_available = DB::table('appointments')
                ->where('date', $service->pivot->date)
                ->where('time', $service->pivot->time)
                ->where('employee_id', $service->pivot->employee_id)
                ->where('status', 1) // Only consider active appointments
                ->doesntExist();
    
            if (!$is_available) {
                $is_employees_available = false;
    
                $first_name = DB::table('employees')->where('id', $service->pivot->employee_id)->value('first_name');
                $service_name = $service->name;
    
                $unavailable_employees->push([
                    'service_name' => $service_name,
                    'date' => $service->pivot->date,
                    'time' => $service->pivot->time,
                    'first_name' => $first_name,
                ]);
            }
        }
    
        if (!$is_employees_available) {
            return redirect()->back()->with('unavailable_employees', $unavailable_employees);
        }
    
        // Fetch the QR code image for online payments
        $qrImage = null;
        if ($request->payment_method === 'online') {
            $qrImage = Payment::latest()->value('image');
        }
    
        // Create appointments for the available services
        foreach ($cart->services as $service) {
            Appointment::create([
                'cart_id' => $cart->id,
                'user_id' => $cart->user_id,
                'service_id' => $service->id,
                'time' => $service->pivot->time,
                'date' => $service->pivot->date,
                'first_name' => $service->pivot->first_name,
                'employee_id' => $service->pivot->employee_id,
                'total' => $service->pivot->price,
                'payment' => $request->payment_method,
                'last_four_digits' => $request->payment_method === 'online' ? $request->last_four_digits : null,
            ]);
        }
    
        // Mark the cart as paid
        $cart->is_paid = true;
        $cart->save();
    
        // Dispatch confirmation emails
        $appointments = Appointment::where('cart_id', $cart->id)->get();
        $customer = auth()->user();
        foreach ($appointments as $appointment) {
            SendAppointmentConfirmationMailJob::dispatch($customer, $appointment);
        }
    
        // Notify admins
        $admins = User::whereHas('role', function ($query) {
            $query->where('name', 'Admin')->orWhere('name', 'Employee');
        })->get();
    
        Notification::send($admins, new NewAppointmentNotification($appointments->first()));
    
        // Redirect with QR code (for online payments)
        if ($request->payment_method === 'online') {
            return redirect()->route('dashboard')->with([
                'success' => 'Your appointment has been booked successfully. Please wait for the email confirmation.',
                'qrImage' => $qrImage,
            ]);
        }
    
        return redirect()->route('dashboard')->with('success', 'Your appointment has been booked successfully. Please wait for the email confirmation.');
    }

}
