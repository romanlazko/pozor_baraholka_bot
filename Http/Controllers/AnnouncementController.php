<?php

namespace App\Bots\pozor_baraholka_bot\Http\Controllers;

use App\Bots\pozor_baraholka_bot\Http\Actions\SendAnnouncement;
use App\Bots\pozor_baraholka_bot\Http\DataTransferObjects\AnnouncementDTO;
use App\Bots\pozor_baraholka_bot\Http\Requests\AnnouncementRequest;
use App\Bots\pozor_baraholka_bot\Models\BaraholkaAnnouncement;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Romanlazko\Telegram\App\Telegram;
use Romanlazko\Telegram\Exceptions\TelegramException;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Telegram $telegram, Request $request)
    {
        $search = strtolower($request->search);
        
        $announcements = BaraholkaAnnouncement::orderByDesc('created_at')
            ->when(!empty($request->input('search')), function($query) use($search) {
                return $query->where(function ($query) use ($search) {
                    $query->whereRaw('LOWER(title) LIKE ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(caption) LIKE ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(category) LIKE ?', ['%' . $search . '%'])
                        ->orWhereHas('chat', function ($query) use ($search) {
                            $query->whereRaw('LOWER(first_name) LIKE ?', ['%' . $search . '%'])
                                ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . $search . '%'])
                                ->orWhereRaw('LOWER(username) LIKE ?', ['%' . $search . '%'])
                                ->orWhereRaw('LOWER(chat_id) LIKE ?', ['%' . $search . '%']);
                        });
                });
            })
            ->with('chat')
            ->paginate(50);

        $announcements_collection = $announcements->map(function ($announcement) use ($telegram){
            $announcement->chat = $announcement->chat()->first();
            $announcement->chat->photo = $telegram::getPhoto(['file_id' => $announcement->chat->photo]);
            return $announcement;
        });

        return view('pozor_baraholka_bot::announcement.index', compact(
            'announcements',
            'announcements_collection'
        ));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(BaraholkaAnnouncement $announcement, Telegram $telegram, SendAnnouncement $sendAnnouncement)
    {
        $admins = $telegram->getAdmins();

        foreach ($admins as $admin) {
            try {
                if ($admin) {
                    $response[$admin] = $sendAnnouncement($telegram, $announcement, $admin);
                }
            }
            catch (TelegramException $e) {
                $response[$admin] = $e->getMessage();
            }
        }
        dd($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(BaraholkaAnnouncement $announcement, Telegram $telegram)
    {
        $announsement = $announcement->photos->map(function ($photo) use ($telegram){
            $photo->url = file_exists(public_path($photo->file_id))
                ? asset($photo->file_id) 
                : $telegram::getPhoto(['file_id' => $photo->file_id]);
        });

        return view('pozor_baraholka_bot::announcement.edit', compact(
            'announcement',
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AnnouncementRequest $request, BaraholkaAnnouncement $announcement)
    {
        $photos = [];

        $announcement->update(
            Arr::except($request->validated(), ['photos'])
        );

        if ($request->has('delete_photos')){
            foreach ($request->delete_photos as $id) {
                $announcement->photos()->find($id)->delete();
            }
        }

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photos[] = ['file_id' => asset(Storage::url($photo->store('public/announcements')))];
            }
            $announcement->photos()->createMany(
                $photos
            );
        }

        return redirect()->route('pozor_baraholka_bot.announcement.index')->with([
            'ok' => true,
            'description' => "Announcement succesfuly updated"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(BaraholkaAnnouncement $announcement)
    {
        $photos = $announcement->photos;

        foreach ($photos as $photo) {
            $photo->delete();
        }

        $announcement->delete();

        return redirect()->route('pozor_baraholka_bot.announcement.index')->with([
            'ok' => true,
            'description' => "Announcement succesfuly deleted"
        ]);
    }
}
