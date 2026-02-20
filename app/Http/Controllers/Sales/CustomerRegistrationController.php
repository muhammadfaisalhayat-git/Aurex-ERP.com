<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\CustomerRegistrationDocument;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CustomerRegistrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:customer_registration.view')->only(['index', 'show']);
        $this->middleware('permission:customer_registration.create')->only(['create', 'store']);
        $this->middleware('permission:customer_registration.approve')->only(['approve', 'reject']);
        $this->middleware('permission:customer_registration.delete')->only(['destroy']);
    }

    public function index()
    {
        $registrations = Customer::whereIn('status', ['pending', 'under_review', 'approved', 'rejected'])
            ->with(['creator', 'reviewer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('sales.customer_registrations.index', compact('registrations'));
    }

    public function create()
    {
        $customerGroups = CustomerGroup::where('is_active', true)->get();
        $branches = Branch::active()->get();
        $salesmen = User::where('is_active', true)->get();

        return view('sales.customer_registrations.create', compact('customerGroups', 'branches', 'salesmen'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_type' => 'required|in:individual,company',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'group_id' => 'nullable|exists:customer_groups,id',
            'salesman_id' => 'nullable|exists:users,id',
            'contact_person' => 'required_if:customer_type,company|nullable|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email',
            'phone' => 'required|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'billing_address' => 'required_if:customer_type,company|nullable|string',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'region' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'tax_number' => 'required_if:customer_type,company|nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'business_type' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string|max:255',
            'opening_balance' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'documents' => 'nullable|array',
            'documents.*' => 'file|max:10240',
        ]);

        DB::beginTransaction();

        try {
            $registration = Customer::create([
                'registration_number' => Customer::generateRegistrationCode(),
                'registration_date' => now(),
                'name_en' => $validated['name_en'],
                'name_ar' => $validated['name_ar'] ?? null,
                'customer_type' => $validated['customer_type'],
                'group_id' => $validated['group_id'] ?? null,
                'salesman_id' => $validated['salesman_id'] ?? null,
                'commercial_registration' => $validated['registration_number'] ?? null,
                'contact_person' => $validated['customer_type'] === 'individual' ? $validated['name_en'] : $validated['contact_person'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'mobile' => $validated['mobile'],
                'address' => $validated['billing_address'] ?? null,
                'city' => $validated['city'],
                'country' => $validated['country'],
                'region' => $validated['region'] ?? null,
                'postal_code' => $validated['postal_code'],
                'tax_number' => $validated['tax_number'] ?? null,
                'business_type' => $validated['business_type'] ?? null,
                'website' => $validated['website'] ?? null,
                'credit_limit' => $validated['credit_limit'] ?? 0,
                'payment_terms' => $validated['payment_terms'] ? (in_array($validated['payment_terms'], ['cash', 'credit_15', 'credit_30', 'credit_45', 'credit_60', 'credit_90']) ? $validated['payment_terms'] : 'credit_30') : 'credit_30',
                'opening_balance' => $validated['opening_balance'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
                'submitted_by' => Auth::id(),
                'company_id' => session('company_id'),
                'branch_id' => session('branch_id'),
                'code' => Customer::generateNextCode(),
            ]);

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $path = $document->store('customer_documents', 'public');

                    CustomerRegistrationDocument::create([
                        'customer_id' => $registration->id,
                        'document_name' => $document->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $document->getClientMimeType(),
                        'document_type' => 'other',
                        'file_size' => $document->getSize(),
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('sales.customer-registrations.show', $registration)
                ->with('success', __('customer_registration.created_successfully'));
        }
        catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Customer $customerRegistration)
    {
        $customerRegistration->load(['documents', 'creator', 'reviewer']);
        return view('sales.customer_registrations.show', compact('customerRegistration'));
    }

    public function edit(Customer $customerRegistration)
    {
        if ($customerRegistration->status !== 'pending') {
            return redirect()->route('sales.customer-registrations.show', $customerRegistration)
                ->with('error', __('customer_registration.cannot_edit_processed'));
        }

        $customerGroups = CustomerGroup::where('is_active', true)->get();
        $branches = Branch::active()->get();
        $salesmen = User::where('is_active', true)->get();

        return view('sales.customer_registrations.edit', compact('customerRegistration', 'customerGroups', 'branches', 'salesmen'));
    }

    public function update(Request $request, Customer $customerRegistration)
    {
        if ($customerRegistration->status !== 'pending') {
            return redirect()->route('sales.customer-registrations.show', $customerRegistration)
                ->with('error', __('customer_registration.cannot_edit_processed'));
        }

        $validated = $request->validate([
            'customer_type' => 'required|in:individual,company',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'group_id' => 'nullable|exists:customer_groups,id',
            'salesman_id' => 'nullable|exists:users,id',
            'contact_person' => 'required_if:customer_type,company|nullable|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,' . $customerRegistration->id,
            'phone' => 'required|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'billing_address' => 'required_if:customer_type,company|nullable|string',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'region' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'tax_number' => 'required_if:customer_type,company|nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'business_type' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string|max:255',
            'opening_balance' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'documents' => 'nullable|array',
            'documents.*' => 'file|max:10240',
        ]);

        DB::beginTransaction();

        try {
            $customerRegistration->update([
                'customer_type' => $validated['customer_type'],
                'name_en' => $validated['name_en'],
                'name_ar' => $validated['name_ar'] ?? null,
                'group_id' => $validated['group_id'] ?? null,
                'salesman_id' => $validated['salesman_id'] ?? null,
                'contact_person' => $validated['customer_type'] === 'individual' ? $validated['name_en'] : $validated['contact_person'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'mobile' => $validated['mobile'],
                'address' => $validated['billing_address'] ?? null,
                'city' => $validated['city'],
                'country' => $validated['country'],
                'region' => $validated['region'] ?? null,
                'postal_code' => $validated['postal_code'],
                'tax_number' => $validated['tax_number'] ?? null,
                'commercial_registration' => $validated['registration_number'] ?? null,
                'business_type' => $validated['business_type'] ?? null,
                'website' => $validated['website'] ?? null,
                'credit_limit' => $validated['credit_limit'] ?? 0,
                'payment_terms' => $validated['payment_terms'] ? (in_array($validated['payment_terms'], ['cash', 'credit_15', 'credit_30', 'credit_45', 'credit_60', 'credit_90']) ? $validated['payment_terms'] : 'credit_30') : 'credit_30',
                'opening_balance' => $validated['opening_balance'] ?? 0,
                'notes' => $validated['notes'] ?? null,
            ]);

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $path = $document->store('customer_documents', 'public');

                    CustomerRegistrationDocument::create([
                        'customer_id' => $customerRegistration->id,
                        'document_name' => $document->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $document->getClientMimeType(),
                        'document_type' => 'other',
                        'file_size' => $document->getSize(),
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('sales.customer-registrations.show', $customerRegistration)
                ->with('success', __('customer_registration.updated_successfully'));
        }
        catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(Customer $customerRegistration)
    {
        if ($customerRegistration->status === 'approved' || $customerRegistration->status === 'active') {
            return redirect()->route('sales.customer-registrations.show', $customerRegistration)
                ->with('error', __('customer_registration.cannot_delete_approved'));
        }

        DB::beginTransaction();

        try {
            foreach ($customerRegistration->documents as $document) {
                Storage::disk('public')->delete($document->file_path);
                $document->delete();
            }

            $customerRegistration->delete();

            DB::commit();

            return redirect()->route('sales.customer-registrations.index')
                ->with('success', __('customer_registration.deleted_successfully'));
        }
        catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function approve(Request $request, Customer $customerRegistration)
    {
        if ($customerRegistration->status !== 'pending' && $customerRegistration->status !== 'under_review') {
            return redirect()->route('sales.customer-registrations.show', $customerRegistration)
                ->with('error', __('customer_registration.cannot_approve'));
        }

        $validated = $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $customerRegistration->update([
                'status' => 'active',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'notes' => $validated['approval_notes'] ?? $customerRegistration->notes,
            ]);

            DB::commit();

            return redirect()->route('sales.customer-registrations.show', $customerRegistration)
                ->with('success', __('customer_registration.approved_successfully'));
        }
        catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, Customer $customerRegistration)
    {
        if ($customerRegistration->status !== 'pending' && $customerRegistration->status !== 'under_review') {
            return redirect()->route('sales.customer-registrations.show', $customerRegistration)
                ->with('error', __('customer_registration.cannot_reject'));
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $customerRegistration->update([
                'status' => 'rejected',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            DB::commit();

            return redirect()->route('sales.customer-registrations.show', $customerRegistration)
                ->with('success', __('customer_registration.rejected_successfully'));
        }
        catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function convertToCustomer(Customer $customerRegistration)
    {
        return redirect()->route('sales.customers.show', $customerRegistration)
            ->with('info', __('customer_registration.already_converted'));
    }

    public function deleteDocument(CustomerRegistrationDocument $document)
    {
        $registration = $document->customer;

        if ($registration->status !== 'pending') {
            return redirect()->route('sales.customer-registrations.show', $registration)
                ->with('error', __('customer_registration.cannot_delete_document'));
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->route('sales.customer-registrations.show', $registration)
            ->with('success', __('customer_registration.document_deleted'));
    }
}
