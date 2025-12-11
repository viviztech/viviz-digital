<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    /**
     * Download the invoice for the order.
     */
    public function downloadInvoice(Order $order)
    {
        Gate::authorize('view', $order);

        $pdf = Pdf::loadView('pdf.invoice', ['order' => $order]);

        return $pdf->download('invoice-' . $order->id . '.pdf');
    }
}
