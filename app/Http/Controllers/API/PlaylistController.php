<?php

namespace App\Http\Controllers\API;

use App\Models\Playlist;
use App\Models\Playlist_song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Mockery\Exception;
use Tymon\JWTAuth\Exceptions\JWTException;

class PlaylistController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $playlist = new Playlist();
            $playlist->name = $request->name;
            $playlist->description = $request->description;
            $playlist->category_id = $request->category_id;
            $playlist->user_id = $request->user_id;
            $playlist->views = 0;
            $playlist->save();
            DB::commit();
            $data = [
                'status' => 'success',
                'message' => 'Thêm playlist thành công'
            ];
            return response()->json($data);
        } catch (JWTException $exception) {
            DB::rollBack();
            $data = [
                'status' => 'error',
                'message' => 'Thêm playlist thất bại'
            ];
            return response()->json($data);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $playlist = Playlist::findOrFail($id);
            $playlist->name = $request->name;
            $playlist->description = $request->description;
            $playlist->category_id = $request->category_id;
            $playlist->save();
            DB::commit();
            $data = [
                'status' => 'success',
                'message' => 'Sửa playlist thành công'
            ];
            return response()->json($data);
        } catch (JWTException $exception) {
            DB::rollBack();
            $data = [
                'status' => 'error',
                'message' => 'Sửa playlist thất bại'
            ];
            return response()->json($data);
        }
    }

    public function myPlaylist($id)
    {
        $playList = DB::table('playlists')->where('user_id', $id)->get();
        return response()->json($playList);
    }

    public function addSong(Request $request)
    {
        $count = DB::table('playlist_song')->where('playlist_id', $request->playlist_id)->count('playlist_id');
        $songs = DB::table('playlist_song')->where('playlist_id', $request->playlist_id)->get('song_id');
        $check = true;
        for ($i = 0; $i < count($songs); $i++) {
            if ($request->song_id == $songs[$i]->song_id) {
                $check = false;
            }
        }
        if ($count <= 20 && $check) {
            $playlistSong = new Playlist_song();
            $playlistSong->playlist_id = $request->playlist_id;
            $playlistSong->song_id = $request->song_id;
            $playlistSong->save();
            $data = [
                'status' => 'success',
                'message' => 'Thêm bài hát vào playlist thành công',
            ];
            return response()->json($data);
        } else if ($count > 20) {
            $data = [
                'status' => 'errorLimit',
                'message' => 'Playlist chỉ thêm được 20 bài hát vui lòng tạo playlist mới'
            ];
            return response()->json($data);
        } else {
            $data = [
                'status' => 'errorMatch',
                'message' => 'Bài hát đã có trong playlist vui lòng chọn bài khác'
            ];
            return response()->json($data);
        }
    }

    public function getById($id)
    {
        $playlist = Playlist::find($id);
        return response()->json($playlist);
    }

    public function getSong($id)
    {
        $listSongs = DB::table('songs')
            ->join('playlist_song', 'songs.id', '=', 'playlist_song.song_id')
            ->where('playlist_id', $id)->orderByDesc('playlist_song.id')
            ->get();
        return response()->json($listSongs);
    }

    public function search($name)
    {
        $playlists = Playlist::where('name', 'LIKE', '%' . $name . '%')->get();
        if ($playlists) {
            return response()->json($playlists);
        }
        $playlists = [];
        return response()->json($playlists);
    }

    public function delete($id)
    {
        $song = Playlist_song::find($id);
        $song->delete();
        return response()->json('Xóa thành công');
    }

    public function getSongId($id)
    {
        $songId = DB::table('playlist_song')->where('id', $id)->get();
        return response()->json($songId);
    }

    public function delete_playlist($id)
    {
        $song = Playlist::find($id);
        $song->delete();
        return response()->json('Xóa thành công');
    }

    public function playPlaylist($id)
    {
        $songs = DB::table('songs')
            ->join('playlist_song', 'songs.id', '=', 'playlist_song.song_id')
            ->where('playlist_id', $id)->get();
        return response()->json($songs);
    }
}
