<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Hiển thị danh sách game
     */
    public function index()
    {
        $games = Game::where('is_active', true)
            ->orderBy('display_order')
            ->get();
            
        return view('games.index', compact('games'));
    }
    
    /**
     * Hiển thị chi tiết một game và danh sách tài khoản của game đó
     */
    public function show($id)
    {
        $game = Game::findOrFail($id);
        
        $accounts = $game->availableAccounts()
            ->latest()
            ->paginate(12);
            
        return view('games.show', compact('game', 'accounts'));
    }
}
