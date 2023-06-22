<?php

namespace App\Bots\pozor_baraholka_bot\Http\Controllers;

use App\Bots\pozor_baraholka_bot\Http\Actions\SendAnnouncement;
use App\Bots\pozor_baraholka_bot\Http\DataTransferObjects\AnnouncementDTO;
use App\Bots\pozor_baraholka_bot\Http\Requests\AnnouncementRequest;
use App\Bots\pozor_baraholka_bot\Models\BaraholkaAnnouncement;
use App\Http\Controllers\Controller;
use App\Models\User;
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
    public function index(Telegram $telegram)
    {
        $announcements = BaraholkaAnnouncement::with('chat')->get();

        $announcements->map(function ($announcement) use ($telegram){
            $announcement->chat = $announcement->chat()->first();
            $announcement->chat->photo = $telegram::getPhoto(['file_id' => $announcement->chat->photo]);
            return $announcement;
        });

        return view('pozor_baraholka_bot::announcement.index', compact(
            'announcements'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pozor_baraholka_bot::announcement.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AnnouncementRequest $request)
    {
        $images = [];

        $announcement = BaraholkaAnnouncement::create(
            Arr::except($request->validated(), ['images'])
        );

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = ['url' => Storage::url($image->store('public/announcements'))];
            }
            $announcement->images()->createMany(
                $images
            );
        }

        return redirect()->route('pozor_baraholka_bot.announcement.index')->with([
            'ok' => true,
            'description' => "Announcement succesfuly created"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(BaraholkaAnnouncement $announcement, Telegram $telegram, SendAnnouncement $sendAnnouncement)
    {
        $admins = User::pluck('chat_id');

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
    public function edit(BaraholkaAnnouncement $announcement)
    {
        // $distributions = $advertisement->distributions()->orderByDesc('created_at')->paginate(20);

        // $distributions->each(function($distribution){
        //     $distribution->chat_ids = collect(json_decode($distribution->chat_ids));
        //     $distribution->results = collect(json_decode($distribution->results));
        //     $distribution->chats_count = $distribution->chat_ids->count();
        //     $distribution->ok_count = $distribution->results->where('ok', true)->count();
        //     $distribution->false_count = $distribution->results->where('ok', false)->count();
        // });

        return view('pozor_baraholka_bot::announcement.edit', compact(
            'advertisement',
            // 'distributions'
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
                $photo = $announcement->photos()->find($id)->delete();
            }
        }

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photos) {
                $photos[] = ['url' => Storage::url($photos->store('public/announcements'))];
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
