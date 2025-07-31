<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Muestra las notificaciones del usuario autenticado (admin o normal)
    public function index()
    {
        return view('notifications.index'); // Usa una única vista
    }

    // Marca una notificación como leída por ID
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);

        $notification->markAsRead();

        return back()->with('success', 'Notification marquée comme lue.');
    }
}
