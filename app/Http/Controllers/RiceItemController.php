<?php

namespace App\Http\Controllers;

use App\Contracts\RiceItemServiceInterface;
use App\Http\Requests\RiceItemStoreRequest;
use Inertia\Inertia;

class RiceItemController extends Controller
{
    public function __construct(
        private RiceItemServiceInterface $service
    ) {}

    public function index()
    {
        $riceItems = $this->service->getAllRiceItems();
        return Inertia::render('rice-items/rice-items', compact('riceItems'));
    }

    public function create()
    {
        return Inertia::render('rice-items/create-rice-item');
    }

    public function store(RiceItemStoreRequest $request)
    {
        $validatedData = $request->validated();
        
        $this->service->createRiceItem($validatedData);

        return redirect()->route('riceItems');
    }
}
