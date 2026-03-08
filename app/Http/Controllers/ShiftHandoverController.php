<?php

namespace App\Http\Controllers;

use App\Contracts\ShiftHandoverServiceInterface;
use App\Http\Requests\ShiftHandoverStoreRequest;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class ShiftHandoverController extends Controller
{
    public function __construct(
        private ShiftHandoverServiceInterface $service
    ) {}

    public function index()
    {
        $handovers = $this->service->getList();
        return Inertia::render('shift-handovers/index', compact('handovers'));
    }

    public function create()
    {
        $unsettledTransactions = $this->service->getUnsettledTransactions();
        return Inertia::render('shift-handovers/create', compact('unsettledTransactions'));
    }

    public function store(ShiftHandoverStoreRequest $request)
    {
        $validatedData = $request->validated();
        
        $handoverData = [
            'handing_over_officer_name' => $validatedData['handing_over_officer_name'],
            'receiving_officer_name'    => $validatedData['receiving_officer_name'],
            'shift_name'                => $validatedData['shift_name'],
            'handover_date'             => now()->format('Y-m-d'),
            'created_by'                => auth()->id(),
        ];

        $handover = $this->service->createHandover($handoverData, $validatedData['transaction_ids']);

        return redirect()->route('shift-handovers.index')->with('success', 'Serah terima berhasil dibuat.');
    }

    public function show($id)
    {
        $handover = $this->service->getById(intval($id));
        if (!$handover) {
            abort(404, 'Shift handover not found');
        }

        return Inertia::render('shift-handovers/show', compact('handover'));
    }

    public function exportPdf($id)
    {
        $handover = $this->service->getById(intval($id));
        if (!$handover) {
            abort(404, 'Shift handover not found');
        }

        $pdf = Pdf::loadView('shift-handover-pdf', compact('handover'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('serah-terima-' . \Illuminate\Support\Str::slug($handover['shift_name']) . '-' . $handover['handover_date'] . '.pdf');
    }
}
