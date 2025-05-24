<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Enums\PengumumanStatus;
use Illuminate\Http\Request;

class PengumumanPublikController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $showAll = $request->input('show_all', false); // Ambil parameter show_all, default false

        $query = Pengumuman::where('status', PengumumanStatus::PUBLISHED)
                            ->whereNotNull('published_at')
                            ->where('published_at', '<=', now());

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('judul', 'like', "%{$keyword}%")
                  ->orWhere('konten', 'like', "%{$keyword}%");
            });
        }

        $query->orderBy('published_at', 'desc');

        if ($showAll) {
            // Jika show_all=true, ambil semua data tanpa paginasi
            $pengumumans = $query->get();
        } else {
            // Jika tidak, gunakan paginasi seperti biasa
            $pengumumans = $query->paginate(5); // Atau jumlah item per halaman Anda
        }

        return view('pengumuman', compact('pengumumans', 'keyword', 'showAll'));
    }

    public function show(string $slug)
    {
        $pengumuman = Pengumuman::where('slug', $slug)
                               ->where('status', PengumumanStatus::PUBLISHED)
                               ->whereNotNull('published_at')
                               ->where('published_at', '<=', now())
                               ->firstOrFail();
        
        return view('pengumuman-detail', compact('pengumuman'));
    }
}