<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InboxController extends Controller
{
    public function index(): View
    {
        $messages = ContactMessage::latest()->paginate(15, ['*'], 'messages_page');
        $subscribers = NewsletterSubscriber::latest()->paginate(20, ['*'], 'subs_page');

        return view('admin.inbox.index', compact('messages', 'subscribers'));
    }

    public function markRead(ContactMessage $message)
    {
        $message->is_read = ! $message->is_read;
        $message->save();

        return back()->with('success', 'Message status updated.');
    }

    public function exportMessages(): StreamedResponse
    {
        $fileName = 'messages_export_' . date('Y_m_d_His') . '.csv';
        $messages = ContactMessage::latest()->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($messages) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Date', 'Name', 'Email', 'Phone', 'Message', 'Is Read']);

            foreach ($messages as $m) {
                fputcsv($handle, [
                    $m->id,
                    $m->created_at->format('Y-m-d H:i'),
                    $m->name,
                    $m->email,
                    $m->phone,
                    $m->message,
                    $m->is_read ? 'Yes' : 'No',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function exportSubscribers(): StreamedResponse
    {
        $fileName = 'subscribers_export_' . date('Y_m_d_His') . '.csv';
        $subscribers = NewsletterSubscriber::latest()->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($subscribers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Email', 'Subscribed At']);

            foreach ($subscribers as $s) {
                fputcsv($handle, [
                    $s->id,
                    $s->email,
                    $s->subscribed_at ? $s->subscribed_at->format('Y-m-d H:i') : $s->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
