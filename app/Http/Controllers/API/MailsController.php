<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Mail;
use App\Models\Pgsql\MailPgsql;
use App\Models\Utils\StoragePath;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MailsController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();

            if (request()->has('id')) {
                $mail = Mail::where('user_id', $user->id)
                    ->with(['receiver', 'sender'])
                    ->find(request('id'));

                if (!$mail) {
                    $mail = Mail::on('pgsql')->where('user_id', $user->id)
                        ->with(['receiver', 'sender'])
                        ->find(request('id'));
                }

                if ($mail && $mail->file_url != null) {
                    $mail->file_url = StoragePath::getStoragePath($mail->file_url);
                }

                return ResponseFormatter::success(
                    $mail,
                    'Mail data retrieved successfully'
                );
            }

            $mails = Mail::where('user_id', $user->id)
                ->with(['receiver', 'sender'])
                ->get();

            if ($mails->isEmpty()) {
                $mails = MailPgsql::where('user_id', $user->id)
                    ->with(['receiver', 'sender'])
                    ->get();
            }

            foreach ($mails as $mail) {
                if ($mail->file_url != null) {
                    $mail->file_url = StoragePath::getStoragePath($mail->file_url);
                }
                unset($mail->sender_id);
                unset($mail->user_id);
            }

            return ResponseFormatter::success(
                $mails,
                'All mail data retrieved successfully'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 'Error retrieving mail data');
        }
    }


    public function sent_message($user_id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required',
            'body' => 'required',
            'file' => 'nullable|mimes:pdf|max:2048',
            'mail_type' => 'required|in:official,non_official',
        ], [
            'subject.required' => 'Subject is required',
            'body.required' => 'Body is required',
            'file.mimes' => 'File must be a PDF',
            'file.max' => 'File size must be less than 2MB',
            'mail_type.required' => 'Mail type is required',
            'mail_type.in' => 'Mail type must be either official or non_official',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(
                $validator->errors(),
                'Validation Error',
                422
            );
        }

        try {
            $data = [
                'id' => Str::uuid(),
                'user_id' => auth()->user()->id,
                'sender_id' => $user_id,
                'subject' => $request->subject,
                'body' => $request->body,
                'mail_type' => $request->mail_type,
                'is_read' => 'unread',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($request->file('file')) {
                $file = $request->file('file');
                $fileContents = file_get_contents($file->getRealPath());
                $base64 = base64_encode($fileContents);

                $data['blob_file'] = $base64;
                $data['file_extention'] = $file->getClientOriginalExtension();
                $data['mime_type'] = $file->getMimeType();
                $data['original_name'] = $file->getClientOriginalName();
            }

            Mail::create($data);

            MailPgsql::create($data);

            return ResponseFormatter::success(
                'Sent Message Successfully'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error($error->getMessage(), 'Error');
        }
    }

    public function get_mails()
    {
        $filters = function ($query) {
            if (request()->has('senderId')) {
                $query->where('sender_id', request('senderId'));
            }

            if (request()->has('receiverId')) {
                $query->where('user_id', request('receiverId'));
            }

            if (request()->has('readStatue')) {
                $readStatus = request('readStatue') === 'true' || request('readStatue') === true ? 'read' : 'unread';
                $query->where('is_read', $readStatus);
            }

            if (request()->has('blobIsEmpty')) {
                if (request('blobIsEmpty') === 'true' || request('blobIsEmpty') === true) {
                    $query->whereNull('blob_file');
                } else {
                    $query->whereNotNull('blob_file');
                }
            }
        };

        $data = Mail::with(['receiver', 'sender'])->where(function ($query) use ($filters) {
            $filters($query);
        })->get();

        if ($data->isEmpty()) {
            $data = MailPgsql::with(['receiver', 'sender'])->where(function ($query) use ($filters) {
                $filters($query);
            })->get();

            return response()->json($data);
        }

        foreach ($data as $mail) {
            unset($mail->sender_id);
            unset($mail->user_id);
        }

        return response()->json($data);
    }
}
