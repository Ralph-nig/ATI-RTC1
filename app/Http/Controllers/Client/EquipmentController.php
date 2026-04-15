<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EquipmentController extends Controller
{
    /**
     * Return the next auto-generated document number for a given type (AJAX).
     * GET /client/equipment/api/next-document-number?type=ICS
     */
    public function nextDocumentNumber(Request $request)
    {
        $type = strtoupper($request->get('type', 'ICS'));
        if (!in_array($type, ['ICS', 'PAR'])) {
            return response()->json(['error' => 'Invalid type'], 422);
        }
        return response()->json(['number' => Equipment::generateDocumentNumber($type)]);
    }

    /**
     * Validate that the document type matches the unit value rule:
     *   - ICS  → unit value must be BELOW ₱50,000
     *   - PAR  → unit value must be ₱50,000 OR ABOVE
     *
     * Returns an error string, or null when valid.
     */
    private function checkDocumentTypeRule(string $docType, float $unitValue): ?string
    {
        if ($docType === 'ICS' && $unitValue >= 50000) {
            return 'ICS cannot be used for items worth ₱50,000 or above. Please use PAR instead.';
        }

        if ($docType === 'PAR' && $unitValue < 50000) {
            return 'PAR cannot be used for items below ₱50,000. Please use ICS instead.';
        }

        return null;
    }

    /**
     * Display a listing of equipment
     */
    public function index(Request $request)
    {
        $query = Equipment::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter by condition
        if ($request->has('condition') && $request->condition) {
            $query->byCondition($request->condition);
        }

        // Sorting
        $sortBy        = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        // Paginate results
        $equipment = $query->paginate(10);

        // Get unique values for filters
        $conditions = ['Serviceable', 'Unserviceable'];

        return view('client.equipment.index', compact('equipment', 'conditions'));
    }

    /**
     * Show equipment assigned to the currently logged-in user.
     */
    public function myEquipment()
    {
        $myEquipment = Equipment::where('responsible_person', auth()->user()->name)
            ->orderBy('article')
            ->get();

        return view('client.equipment.my-equipment', compact('myEquipment'));
    }

    /**
     * Show the form for creating new equipment
     */
    public function create()
    {
        if (!auth()->user()->hasPermission('create')) {
            return redirect()->route('client.equipment.index')
                ->with('error', 'You do not have permission to create equipment.');
        }

        $users = \App\Models\User::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        return view('client.equipment.create', compact('users'));
    }

    /**
     * Store newly created equipment
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('create')) {
            return redirect()->route('client.equipment.index')
                ->with('error', 'You do not have permission to create equipment.');
        }

        // Strict ICS / PAR rule — block invalid combinations before field validation
        $docType   = (string) $request->input('document_type', '');
        $unitValue = (float)  $request->input('unit_value', 0);

        $docTypeError = $this->checkDocumentTypeRule($docType, $unitValue);
        if ($docTypeError) {
            return back()->withInput()->withErrors([
                'document_type' => $docTypeError,
            ]);
        }

        $validated = $request->validate([
            'document_type'         => 'required|in:ICS,PAR',
            'document_number'       => 'required|string|max:50|unique:equipment,document_number',
            'property_number'       => 'required|string|max:255|unique:equipment,property_number',
            'article'               => 'required|string|max:255',
            'classification'        => 'nullable|string|max:255',
            'description'           => 'nullable|string',
            'unit_of_measurement'   => 'required|string|max:50',
            'unit_value'            => 'required|numeric|min:0',
            'quantity'              => 'required|integer|min:1|max:9999',
            'condition'             => 'required|in:Serviceable,Unserviceable',
            'disposal_method'       => 'required_if:condition,Unserviceable|nullable|in:sale,transfer,destruction,others',
            'disposal_details'      => 'required_if:disposal_method,others|nullable|string|max:255|exclude_unless:disposal_method,others',
            'acquisition_date'      => 'nullable|date',
            'location'              => 'nullable|string|max:255',
            'responsibility_center' => 'nullable|in:ISS,AFU,CDMS,PAS,PMEU,OCD,DORM',
            'responsible_person'    => 'nullable|string|max:255',
            'remarks'               => 'nullable|string',
        ], [
            'document_type.required'       => 'Please select a document type (ICS or PAR).',
            'document_number.required'     => 'Document number is required.',
            'document_number.unique'       => 'This document number is already in use.',
            'disposal_method.required_if'  => 'The disposal method field is required when condition is Unserviceable.',
            'disposal_details.required_if' => 'Please specify the disposal details when selecting "Others".',
        ]);

        Equipment::create($validated);

        return redirect()->route('client.equipment.index')
            ->with('success', 'Equipment added successfully!');
    }

    /**
     * Display the specified equipment
     */
    public function show($id)
    {
        if (!auth()->user()->hasPermission('read')) {
            return redirect()->route('client.equipment.index')
                ->with('error', 'You do not have permission to view equipment.');
        }

        $equipment = Equipment::findOrFail($id);
        return view('client.equipment.view', compact('equipment'));
    }

    /**
     * Show the form for editing equipment
     */
    public function edit($id)
    {
        if (!auth()->user()->hasPermission('update')) {
            return redirect()->route('client.equipment.index')
                ->with('error', 'You do not have permission to edit equipment.');
        }

        $equipment = Equipment::findOrFail($id);
        $users     = \App\Models\User::where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return view('client.equipment.edit', compact('equipment', 'users'));
    }

    /**
     * Update the specified equipment
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasPermission('update')) {
            return redirect()->route('client.equipment.index')
                ->with('error', 'You do not have permission to update equipment.');
        }

        $equipment = Equipment::findOrFail($id);

        // Strict ICS / PAR rule — block invalid combinations before field validation
        $docType   = (string) $request->input('document_type', '');
        $unitValue = (float)  $request->input('unit_value', 0);

        $docTypeError = $this->checkDocumentTypeRule($docType, $unitValue);
        if ($docTypeError) {
            return back()->withInput()->withErrors([
                'document_type' => $docTypeError,
            ]);
        }

        $validated = $request->validate([
            'document_type'         => 'required|in:ICS,PAR',
            'document_number'       => 'required|string|max:50|unique:equipment,document_number,' . $id,
            'property_number'       => 'required|string|max:255|unique:equipment,property_number,' . $id,
            'article'               => 'required|string|max:255',
            'classification'        => 'nullable|string|max:255',
            'description'           => 'nullable|string',
            'unit_of_measurement'   => 'required|string|max:50',
            'unit_value'            => 'required|numeric|min:0',
            'quantity'              => 'required|integer|min:1|max:9999',
            'condition'             => 'required|in:Serviceable,Unserviceable',
            'disposal_method'       => 'required_if:condition,Unserviceable|nullable|in:sale,transfer,destruction,others',
            'disposal_details'      => 'required_if:disposal_method,others|nullable|string|max:255|exclude_unless:disposal_method,others',
            'acquisition_date'      => 'nullable|date',
            'location'              => 'nullable|string|max:255',
            'responsibility_center' => 'nullable|in:ISS,AFU,CDMS,PAS,PMEU,OCD,DORM',
            'responsible_person'    => 'nullable|string|max:255',
            'remarks'               => 'nullable|string',
        ], [
            'document_type.required'       => 'Please select a document type (ICS or PAR).',
            'document_number.required'     => 'Document number is required.',
            'document_number.unique'       => 'This document number is already in use.',
            'disposal_method.required_if'  => 'The disposal method field is required when condition is Unserviceable.',
            'disposal_details.required_if' => 'Please specify the disposal details when selecting "Others".',
        ]);

        $equipment->update($validated);

        return redirect()->route('client.equipment.index')
            ->with('success', 'Equipment updated successfully!');
    }

    /**
     * Remove the specified equipment
     */
    public function destroy($id)
    {
        if (!auth()->user()->hasPermission('delete')) {
            return redirect()->route('client.equipment.index')
                ->with('error', 'You do not have permission to delete equipment.');
        }

        $equipment = Equipment::findOrFail($id);
        $equipment->forceDelete();

        return redirect()->route('client.equipment.index')
            ->with('success', 'Equipment deleted successfully!');
    }

    /**
     * Export equipment to Excel
     */
    public function export(Request $request)
    {
        if (!auth()->user()->hasPermission('read')) {
            return redirect()->route('client.equipment.index')
                ->with('error', 'You do not have permission to export equipment.');
        }

        $query = Equipment::query();

        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        if ($request->has('condition') && !empty($request->condition)) {
            $query->byCondition($request->condition);
        }

        $sortBy        = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $perPage     = 10;
        $currentPage = $request->get('page', 1);
        $offset      = ($currentPage - 1) * $perPage;
        $equipment   = $query->skip($offset)->take($perPage)->get();

        $data   = [];
        $data[] = [
            'Property Number',
            'Article',
            'Qty',
            'Classification',
            'Description',
            'Unit of Measurement',
            'Unit Value',
            'Condition',
            'Disposal Method',
            'Disposal Details',
            'Acquisition Date',
            'Responsibility Center',
            'Responsible Person',
            'Remarks',
        ];

        foreach ($equipment as $item) {
            $data[] = [
                $item->property_number,
                $item->article,
                $item->quantity ?? 1,
                $item->classification ?: 'N/A',
                $item->description ?: 'N/A',
                $item->unit_of_measurement,
                $item->unit_value,
                $item->condition,
                $item->disposal_method ?: 'N/A',
                $item->disposal_details ?: 'N/A',
                $item->acquisition_date ? $item->acquisition_date->format('F d, Y') : 'N/A',
                $item->responsibility_center ?: 'N/A',
                $item->responsible_person ?: 'N/A',
                $item->remarks ?: 'N/A',
            ];
        }

        return Excel::download(
            new class($data) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithEvents {
                protected $data;

                public function __construct($data)
                {
                    $this->data = $data;
                }

                public function array(): array
                {
                    return $this->data;
                }

                public function registerEvents(): array
                {
                    return [
                        \Maatwebsite\Excel\Events\AfterSheet::class => function (\Maatwebsite\Excel\Events\AfterSheet $event) {
                            $sheet = $event->sheet->getDelegate();
                            $sheet->getColumnDimension('A')->setWidth(15);
                            $sheet->getColumnDimension('B')->setWidth(20);
                            $sheet->getColumnDimension('C')->setWidth(15);
                            $sheet->getColumnDimension('D')->setWidth(30);
                            $sheet->getColumnDimension('E')->setWidth(15);
                            $sheet->getColumnDimension('F')->setWidth(12);
                            $sheet->getColumnDimension('G')->setWidth(12);
                            $sheet->getColumnDimension('H')->setWidth(15);
                            $sheet->getColumnDimension('I')->setWidth(20);
                            $sheet->getColumnDimension('J')->setWidth(15);
                            $sheet->getColumnDimension('K')->setWidth(20);
                            $sheet->getColumnDimension('L')->setWidth(20);
                            $sheet->getColumnDimension('M')->setWidth(30);
                        },
                    ];
                }
            },
            'equipment_list.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    /**
     * Get unique classifications for autocomplete
     */
    public function getClassifications()
    {
        $classifications = Equipment::whereNotNull('classification')
            ->distinct()
            ->pluck('classification')
            ->filter()
            ->values();

        return response()->json($classifications);
    }
}