Route::get('/watchlist', function () { 
    $watchlist = new \Illuminate\Pagination\LengthAwarePaginator(
        collect([]), // Empty data
        0, // Total count
        15, // Per page
        1, // Current page
        ['path' => request()->url()]
    );
    
    return view('watchlist.index', [
        'watchlist' => $watchlist
    ]); 
})->name('watchlist.index');
