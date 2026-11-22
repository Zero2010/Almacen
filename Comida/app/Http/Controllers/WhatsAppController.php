<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function __construct(private WhatsAppService $whatsapp) {}

    public function sendTrackingCode(Order $order)
    {
        $phone = $order->client_phone;
        $message = "Hola {$order->client_name}! 👋\n\n";
        $message .= "Tu pedido fue recibido correctamente.\n";
        $message .= "Código de seguimiento: {$order->tracking_code}\n";
        $message .= "Estado: " . ucfirst(str_replace('_', ' ', $order->current_stage ?? 'recibido')) . "\n";
        $message .= "Total: $" . number_format($order->total, 0, ',', '.') . " CLP\n\n";
        $message .= "Puedes consultar el estado en cualquier momento respondiendo este mensaje.";

        $this->whatsapp->sendText($phone, $message);

        return back()->with('status', 'Mensaje enviado por WhatsApp.');
    }

    public function sendStatusUpdate(Order $order, Request $request)
    {
        $status = $request->input('status');
        $phone = $order->client_phone;

        $message = "Actualización de tu pedido #{$order->tracking_code} 🍔\n";
        $message .= "Nuevo estado: " . ucfirst(str_replace('_', ' ', $status)) . "\n";
        $message .= "Tipo: " . ($order->type === 'in_person' ? 'Presencial' : ($order->type === 'delivery' ? 'Delivery' : 'Retiro')) . "\n";
        $message .= "Total: $" . number_format($order->total, 0, ',', '.') . " CLP\n\n";
        $message .= "Te avisaremos cuando esté listo.";

        $this->whatsapp->sendText($phone, $message);

        return back()->with('status', 'Notificación enviada por WhatsApp.');
    }
}
