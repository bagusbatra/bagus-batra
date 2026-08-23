<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->trim()->value() ?: 'all';

        $query = ContactMessage::query();

        if ($status === 'unread') {
            $query->where('is_read', false);
        }

        $messages = $query->latest()->paginate(10)->withQueryString();
        $unreadCount = ContactMessage::where('is_read', false)->count();

        return view('admin.messages.index', [
            'messages' => $messages,
            'status' => $status,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Opening a message marks it read automatically (per
     * docs/RENCANA-PENGEMBANGAN.md Iterasi 8 — "tandai dibaca otomatis
     * saat dibuka"), no separate "mark as read" button needed.
     */
    public function show(ContactMessage $message): View
    {
        if (! $message->is_read) {
            $message->update(['is_read' => true]);
        }

        return view('admin.messages.show', compact('message'));
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return redirect()->route('admin.messages')->with('success', 'Pesan berhasil dihapus.');
    }
}
