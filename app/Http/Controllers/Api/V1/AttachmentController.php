<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Models\JournalEntryAttachment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function index(JournalEntry $entry): JsonResponse
    {
        return response()->json($entry->attachments()->with('user:id,name')->get());
    }

    public function store(Request $request, JournalEntry $entry): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,webp,xlsx,csv,doc,docx',
        ]);

        $file = $request->file('file');
        $path = $file->store("attachments/{$entry->id}", 'public');

        $attachment = JournalEntryAttachment::create([
            'journal_entry_id' => $entry->id,
            'user_id'          => $request->user()->id,
            'file_path'        => $path,
            'file_name'        => $file->getClientOriginalName(),
            'mime_type'        => $file->getMimeType(),
            'file_size_bytes'  => $file->getSize(),
        ]);

        return response()->json($attachment, 201);
    }

    public function destroy(JournalEntry $entry, JournalEntryAttachment $attachment): JsonResponse
    {
        abort_if($attachment->journal_entry_id !== $entry->id, 404);
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();
        return response()->json(['message' => 'Attachment deleted.']);
    }
}
