<?php

namespace App\Http\Livewire;

use App\Models\Payment;
use Livewire\Component;
use Livewire\WithFileUploads;

class ManagePayments extends Component
{
    use WithFileUploads;

    private $payments;
    public $image;
    public $search;

    protected $queryString = [
        'search' => ['except' => ''],
    ];
    public $payment;

    public $confirmingPaymentAdd;

    protected $rules = [
        "payment.name" => "required|string|max:255",
    ];

    public function render()
    {
        $this->payments = Payment::when($this->search, function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%');
        })->paginate(10);

        return view('livewire.manage-payments', [
            'payments' => $this->payments,
        ]);
    }

    public function confirmPaymentEdit(Payment $payment)
    {
        $this->payment = $payment;
        $this->confirmingPaymentAdd = true;
    }

    public function savePayment()
    {
        $this->validate([
            'payment.name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048', // Validate the image
        ]);

        $imagePath = null;

        if ($this->image) {
            $imagePath = $this->image->store('payments', 'public'); // Save image to 'storage/app/public/categories'
        }

        if (isset($this->payment->id)) {
            $this->payment->update([
                'name' => $this->payment['name'],
                'image' => $imagePath ?: $this->payment->image,
            ]);

            $this->dispatchBrowserEvent('payment-saved', ['message' => 'Payment setup updated successfully!']);
        } else {
            Payment::create([
                'name' => $this->payment['name'],
                'image' => $imagePath,
            ]);

            $this->dispatchBrowserEvent('payment-saved', ['message' => 'Payment setup added successfully!']);
        }

        $this->confirmingPaymentAdd = false;
        $this->payment = null;
        $this->image = null; // Reset the image input
    }

    public function confirmPaymentAdd()
    {
        $this->confirmingPaymentAdd = true;
    }
}
