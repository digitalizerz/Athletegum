<?php

namespace App\Http\Controllers\Athlete;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DealMessageController extends Controller
{
    /**
     * List all deals with messages (athlete view)
     */
    public function index()
    {
        // Get all deals that have messages and belong to the athlete
        $deals = Deal::where('athlete_id', Auth::guard('athlete')->id())
            ->whereHas('messages')
            ->with(['user', 'messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('athlete.deals.messages-index', [
            'deals' => $deals,
        ]);
    }

    /**
     * Show messages for a deal (athlete view)
     */
    public function show(Deal $deal)
    {
        $athlete = Auth::guard('athlete')->user();

        // Ensure deal belongs to athlete
        if ($deal->athlete_id !== $athlete->id) {
            abort(403, 'You do not have access to this deal.');
        }

        // Ensure deal is accepted (messaging only unlocked after acceptance)
        if ($deal->status === 'pending') {
            return redirect()->route('athlete.deals.index')->withErrors(['error' => 'Messaging is only available after accepting the deal.']);
        }

        $messages = $deal->messages()->with(['sender', 'athleteSender'])->get();

        return view('athlete.deals.messages', [
            'deal' => $deal,
            'messages' => $messages,
        ]);
    }

    /**
     * Store a new message (athlete)
     */
    public function store(Request $request, Deal $deal)
    {
        $athlete = Auth::guard('athlete')->user();

        // Ensure deal belongs to athlete
        if ($deal->athlete_id !== $athlete->id) {
            abort(403, 'You do not have access to this deal.');
        }

        // Ensure deal is accepted
        if ($deal->status === 'pending') {
            return redirect()->back()->withErrors(['error' => 'Messaging is only available after accepting the deal.']);
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
            'sender_type' => 'athlete',
            'athlete_sender_id' => $athlete->id,
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

        // Create notification for SMB
        if ($deal->user_id) {
            \App\Models\Notification::createForUser(
                $deal->user_id,
                'message',
                'New message from ' . ($athlete->name ?? 'Athlete'),
                Str::limit($content ?: 'Sent an attachment', 100),
                route('deals.messages', $deal),
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
