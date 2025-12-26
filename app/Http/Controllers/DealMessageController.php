<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DealMessageController extends Controller
{
    /**
     * List all deals with messages (SMB view)
     */
    public function index()
    {
        // Get all deals that have messages and belong to the user
        $deals = Deal::where('user_id', Auth::id())
            ->whereHas('messages')
            ->with(['athlete', 'messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('deals.messages-index', [
            'deals' => $deals,
        ]);
    }

    /**
     * Show messages for a deal (SMB view)
     */
    public function show(Deal $deal)
    {
        $user = Auth::user();

        // Ensure deal belongs to user
        if ($deal->user_id !== $user->id) {
            abort(403, 'You do not have access to this deal.');
        }

        // Ensure deal is accepted (messaging only unlocked after acceptance)
        if ($deal->status === 'pending' || !$deal->athlete_id) {
            return redirect()->route('deals.index')->withErrors(['error' => 'Messaging is only available after the athlete accepts the deal.']);
        }

        // Eager load athlete relationship for deliverables display
        $deal->load('athlete');

        $messages = $deal->messages()->with(['sender', 'athleteSender'])->get();

        // Mark messages from athletes as read by this user
        foreach ($messages as $message) {
            if ($message->sender_type === 'athlete' && !$message->isReadByUser($user->id)) {
                $message->markAsReadByUser($user->id);
            }
        }

        return view('deals.messages', [
            'deal' => $deal,
            'messages' => $messages,
        ]);
    }

    /**
     * Store a new message (SMB)
     */
    public function store(Request $request, Deal $deal)
    {
        $user = Auth::user();

        // Ensure deal belongs to user
        if ($deal->user_id !== $user->id) {
            abort(403, 'You do not have access to this deal.');
        }

        // Ensure deal is accepted
        if ($deal->status === 'pending' || !$deal->athlete_id) {
            return redirect()->back()->withErrors(['error' => 'Messaging is only available after the athlete accepts the deal.']);
        }

        // Check if deal is completed/paid (read-only)
        if (in_array($deal->status, ['completed', 'paid']) && $deal->released_at) {
            return redirect()->back()->withErrors(['error' => 'This deal is completed. Messaging is read-only.']);
        }

        $validated = $request->validate([
            'content' => ['nullable', 'string', 'max:5000', 'required_without:attachment'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png,gif,mp4,mov,avi'],
        ]);

        // Filter content for email, phone, URLs
        $content = $this->filterContent($validated['content'] ?? '');

        $messageData = [
            'deal_id' => $deal->id,
            'sender_type' => 'user',
            'sender_id' => $user->id,
            'message_type' => $request->hasFile('attachment') ? 'attachment' : 'text',
            'content' => $content,
        ];

        // Handle attachment
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('deal-messages', 'public');
            
            $messageData['attachment_path'] = $path;
            $messageData['attachment_original_name'] = $file->getClientOriginalName();
            $messageData['attachment_mime_type'] = $file->getMimeType();
            $messageData['attachment_size'] = $file->getSize();
        }

        $message = Message::create($messageData);

        // Create notification for athlete
        if ($deal->athlete_id) {
            \App\Models\Notification::createForAthlete(
                $deal->athlete_id,
                'message',
                'New message from ' . ($user->business_name ?? $user->name ?? 'Business'),
                Str::limit($content ?: 'Sent an attachment', 100),
                route('athlete.deals.messages', $deal),
                $deal->id,
                $message->id
            );
        }

        return redirect()->back()->with('success', 'Message sent successfully.');
    }

    /**
     * Filter content to prevent off-platform communication
     */
    private function filterContent(string $content): string
    {
        // Remove or obfuscate email addresses
        $content = preg_replace('/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/', '[email protected]', $content);
        
        // Remove or obfuscate phone numbers (various formats)
        $content = preg_replace('/\b(\+?1[-.\s]?)?\(?([0-9]{3})\)?[-.\s]?([0-9]{3})[-.\s]?([0-9]{4})\b/', '[phone number]', $content);
        
        return $content;
    }
}
