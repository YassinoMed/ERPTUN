<?php

namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use Illuminate\Http\Request;

class NotificationPreferenceController extends Controller
{
    public function index()
    {
        $preferences = NotificationPreference::query()
            ->where('user_id', auth()->id())
            ->orderBy('notification_type')
            ->get();

        return response()->json([
            'items' => $preferences,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'preferences' => 'required|array',
            'preferences.*.notification_type' => 'required|string|max:191',
            'preferences.*.in_app' => 'nullable|boolean',
            'preferences.*.email' => 'nullable|boolean',
            'preferences.*.sms' => 'nullable|boolean',
            'preferences.*.whatsapp' => 'nullable|boolean',
        ]);

        foreach ($validated['preferences'] as $preference) {
            NotificationPreference::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'notification_type' => $preference['notification_type'],
                ],
                [
                    'in_app' => (bool) ($preference['in_app'] ?? true),
                    'email' => (bool) ($preference['email'] ?? false),
                    'sms' => (bool) ($preference['sms'] ?? false),
                    'whatsapp' => (bool) ($preference['whatsapp'] ?? false),
                    'created_by' => auth()->user()->creatorId(),
                ]
            );
        }

        return response()->json(['ok' => true]);
    }
}
