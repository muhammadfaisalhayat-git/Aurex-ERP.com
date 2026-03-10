<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\CustomerRequest;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Product;
use App\Services\WhatsAppService;
use App\Services\ArabicShaper;
use Illuminate\Http\Request;

class CustomerRequestController extends Controller
{
    protected $whatsappService;
    protected $arabicShaper;

    public function __construct(WhatsAppService $whatsappService, ArabicShaper $arabicShaper)
    {
        $this->whatsappService = $whatsappService;
        $this->arabicShaper = $arabicShaper;
    }

    public function index(Request $request)
    {
        $query = CustomerRequest::with(['customer', 'branch', 'creator']);

        // Apply filters
        if ($request->filled('document_number')) {
            $query->where('document_number', 'like', '%' . $request->document_number . '%');
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        } elseif ($request->filled('customer_name')) {
            $customerName = $request->customer_name;
            $query->whereHas('customer', function ($q) use ($customerName) {
                $q->where('name_en', 'like', "%{$customerName}%")
                    ->orWhere('name_ar', 'like', "%{$customerName}%")
                    ->orWhere('code', 'like', "%{$customerName}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('request_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('request_date', '<=', $request->date_to);
        }

        $customerRequests = $query->latest()->paginate(10);
        $customers = Customer::active()->orderBy('name_en')->get();
        $branches = Branch::all();

        return view('sales.customer-requests.index', compact('customerRequests', 'customers', 'branches'));
    }

    public function create()
    {
        $customers = Customer::active()->orderBy('name_en')->get();
        $branches = Branch::all();
        $products = Product::active()->orderBy('name_en')->get();
        $document_number = \App\Models\DocumentNumber::generate('customer_request', 'CR');
        $taxSetting = \App\Models\TaxSetting::getCurrent();

        return view('sales.customer-requests.create', compact('customers', 'branches', 'products', 'document_number', 'taxSetting'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_number' => 'required|string|max:50|unique:customer_requests,document_number',
            'request_date' => 'required|date',
            'needed_date' => 'nullable|date|after_or_equal:request_date',
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'status' => 'required|in:pending,converted,cancelled,draft',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.measurement_unit_id' => 'required|exists:measurement_units,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        $taxSetting = \App\Models\TaxSetting::getCurrent();
        $subtotal = 0;
        $totalTax = 0;

        foreach ($validated['items'] as &$item) {
            $item['tax_rate'] = $taxSetting->tax_enabled ? $taxSetting->default_tax_rate : 0;
            $lineSubtotal = $item['quantity'] * $item['unit_price'];
            $lineTax = $lineSubtotal * ($item['tax_rate'] / 100);
            $item['tax_amount'] = $lineTax;
            $item['total_amount'] = $lineSubtotal + $lineTax;

            $subtotal += $lineSubtotal;
            $totalTax += $lineTax;
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $customerRequest = CustomerRequest::create([
                'document_number' => $validated['document_number'],
                'request_date' => $validated['request_date'],
                'needed_date' => $validated['needed_date'],
                'customer_id' => $validated['customer_id'],
                'branch_id' => $validated['branch_id'],
                'subtotal' => $subtotal,
                'tax_amount' => $totalTax,
                'total_amount' => $subtotal + $totalTax,
                'status' => $validated['status'],
                'notes' => $validated['notes'],
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                \App\Models\CustomerRequestItem::create([
                    'customer_request_id' => $customerRequest->id,
                    'product_id' => $item['product_id'],
                    'measurement_unit_id' => $item['measurement_unit_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'],
                    'tax_amount' => $item['tax_amount'],
                    'total_amount' => $item['total_amount'],
                    'notes' => $item['notes'],
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('sales.customer-requests.index')
                ->with('success', __('messages.record_created'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Error creating request: ' . $e->getMessage())->withInput();
        }
    }

    public function show(CustomerRequest $customerRequest)
    {
        $customerRequest->load(['customer', 'branch', 'creator', 'items.product', 'items.measurementUnit']);
        return view('sales.customer-requests.show', compact('customerRequest'));
    }

    public function edit(CustomerRequest $customerRequest)
    {
        $customerRequest->load('items');
        $customers = Customer::active()->orderBy('name_en')->get();
        $branches = Branch::all();
        $products = Product::active()->orderBy('name_en')->get();
        $taxSetting = \App\Models\TaxSetting::getCurrent();

        return view('sales.customer-requests.edit', compact('customerRequest', 'customers', 'branches', 'products', 'taxSetting'));
    }

    public function update(Request $request, CustomerRequest $customerRequest)
    {
        $validated = $request->validate([
            'document_number' => 'required|string|max:50|unique:customer_requests,document_number,' . $customerRequest->id,
            'request_date' => 'required|date',
            'needed_date' => 'nullable|date|after_or_equal:request_date',
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'status' => 'required|in:pending,converted,cancelled,draft',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.measurement_unit_id' => 'required|exists:measurement_units,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        $taxSetting = \App\Models\TaxSetting::getCurrent();
        $subtotal = 0;
        $totalTax = 0;

        foreach ($validated['items'] as &$item) {
            $item['tax_rate'] = $taxSetting->tax_enabled ? $taxSetting->default_tax_rate : 0;
            $lineSubtotal = $item['quantity'] * $item['unit_price'];
            $lineTax = $lineSubtotal * ($item['tax_rate'] / 100);
            $item['tax_amount'] = $lineTax;
            $item['total_amount'] = $lineSubtotal + $lineTax;

            $subtotal += $lineSubtotal;
            $totalTax += $lineTax;
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $customerRequest->update([
                'document_number' => $validated['document_number'],
                'request_date' => $validated['request_date'],
                'needed_date' => $validated['needed_date'],
                'customer_id' => $validated['customer_id'],
                'branch_id' => $validated['branch_id'],
                'subtotal' => $subtotal,
                'tax_amount' => $totalTax,
                'total_amount' => $subtotal + $totalTax,
                'status' => $validated['status'],
                'notes' => $validated['notes'],
            ]);

            $customerRequest->items()->delete();
            foreach ($validated['items'] as $item) {
                \App\Models\CustomerRequestItem::create([
                    'customer_request_id' => $customerRequest->id,
                    'product_id' => $item['product_id'],
                    'measurement_unit_id' => $item['measurement_unit_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'],
                    'tax_amount' => $item['tax_amount'],
                    'total_amount' => $item['total_amount'],
                    'notes' => $item['notes'],
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('sales.customer-requests.index')
                ->with('success', __('messages.record_updated'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Error updating request: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(CustomerRequest $customerRequest)
    {
        if ($customerRequest->status === 'converted') {
            return back()->with('error', 'Cannot delete a converted request.');
        }

        $customerRequest->items()->delete();
        $customerRequest->delete();

        return redirect()->route('sales.customer-requests.index')
            ->with('success', __('messages.record_deleted'));
    }

    public function convertToQuotation(CustomerRequest $customerRequest)
    {
        // Implementation for conversion logic can be added here
        return redirect()->back()->with('error', 'Conversion logic not implemented yet.');
    }

    public function exportPdf(CustomerRequest $customerRequest)
    {
        $customerRequest->load(['customer', 'branch', 'creator', 'items.product', 'items.measurementUnit', 'company']);

        // Reshape Arabic text for PDF
        if ($customerRequest->company) {
            $customerRequest->company_name_ar = $this->arabicShaper->shape($customerRequest->company->name_ar ?? $customerRequest->company->name_en);
        }
        $customerRequest->customer_name_ar = $this->arabicShaper->shape($customerRequest->customer?->name_ar ?? '');
        $customerRequest->notes_ar = $this->arabicShaper->shape($customerRequest->notes ?? '');

        foreach ($customerRequest->items as $item) {
            $item->product_name_ar = $this->arabicShaper->shape($item->product->name_ar ?? $item->product->name_en);
        }

        // Base64 logo for PDF
        $logoBase64 = null;
        if ($customerRequest->company?->logo) {
            $path = public_path('storage/' . $customerRequest->company->logo);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('sales.customer-requests.pdf', compact('customerRequest', 'logoBase64'));

        return $pdf->download('Customer_Request_' . $customerRequest->document_number . '.pdf');
    }

    public function sendWhatsApp(CustomerRequest $customerRequest)
    {
        $customer = $customerRequest->customer;

        if (!$customer) {
            return back()->with('error', 'Customer request does not have a linked customer.');
        }

        $phone = $customer->mobile ?? $customer->phone;

        if (!$phone) {
            return back()->with('error', 'Customer does not have a mobile or phone number.');
        }

        $message = $this->whatsappService->formatDocumentMessage($customerRequest, 'request');
        $link = $this->whatsappService->generateLink($phone, $message);

        return redirect()->away($link);
    }
}
