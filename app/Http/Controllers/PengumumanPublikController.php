<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman; // Import model Pengumuman Anda
use App\Enums\PengumumanStatus; // Import Enum Status Pengumuman Anda
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder; // Untuk scope

class PengumumanPublikController extends Controller
{
    public function index(Request $request) 
    {
        $keyword = $request->input('keyword'); 

        $query = Pengumuman::where('status', PengumumanStatus::PUBLISHED)
                            ->whereNotNull('published_at')
                            ->where('published_at', '<=', now());

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('judul', 'like', "%{$keyword}%")
                  ->orWhere('konten', 'like', "%{$keyword}%");
            });
        }

        $pengumumans = $query->orderBy('published_at', 'desc')
                             ->paginate(9); // Misalnya 9 pengumuman per halaman untuk grid 3 kolom

        
        return view('pengumuman', compact('pengumumans', 'keyword'));
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