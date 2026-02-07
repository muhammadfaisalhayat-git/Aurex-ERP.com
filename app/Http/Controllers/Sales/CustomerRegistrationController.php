<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\CustomerRegistration;
use App\Models\CustomerRegistrationDocument;
use App\Models\Customer;
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
        $registrations = CustomerRegistration::with(['creator', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('sales.customer_registrations.index', compact('registrations'));
    }

    public function create()
    {
        return view('sales.customer_registrations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customer_registrations',
            'phone' => 'required|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'business_type' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'documents' => 'nullable|array',
            'documents.*' => 'file|max:10240',
        ]);

        DB::beginTransaction();
        
        try {
            $registration = CustomerRegistration::create([
                'registration_code' => CustomerRegistration::generateRegistrationCode(),
                'company_name' => $validated['company_name'],
                'contact_person' => $validated['contact_person'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'mobile' => $validated['mobile'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'country' => $validated['country'],
                'postal_code' => $validated['postal_code'],
                'tax_number' => $validated['tax_number'],
                'registration_number' => $validated['registration_number'],
                'business_type' => $validated['business_type'],
                'website' => $validated['website'],
                'credit_limit' => $validated['credit_limit'],
                'payment_terms' => $validated['payment_terms'],
                'notes' => $validated['notes'],
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $path = $document->store('customer_documents', 'public');
                    
                    CustomerRegistrationDocument::create([
                        'customer_registration_id' => $registration->id,
                        'document_name' => $document->getClientOriginalName(),
                        'document_path' => $path,
                        'document_type' => $document->getClientMimeType(),
                        'file_size' => $document->getSize(),
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('customer-registrations.show', $registration)
                ->with('success', __('customer_registration.created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(CustomerRegistration $customerRegistration)
    {
        $customerRegistration->load(['documents', 'creator', 'approver']);
        return view('sales.customer_registrations.show', compact('customerRegistration'));
    }

    public function edit(CustomerRegistration $customerRegistration)
    {
        if ($customerRegistration->status !== 'pending') {
            return redirect()->route('customer-registrations.show', $customerRegistration)
                ->with('error', __('customer_registration.cannot_edit_processed'));
        }

        return view('sales.customer_registrations.edit', compact('customerRegistration'));
    }

    public function update(Request $request, CustomerRegistration $customerRegistration)
    {
        if ($customerRegistration->status !== 'pending') {
            return redirect()->route('customer-registrations.show', $customerRegistration)
                ->with('error', __('customer_registration.cannot_edit_processed'));
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customer_registrations,email,' . $customerRegistration->id,
            'phone' => 'required|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'business_type' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'documents' => 'nullable|array',
            'documents.*' => 'file|max:10240',
        ]);

        DB::beginTransaction();
        
        try {
            $customerRegistration->update([
                'company_name' => $validated['company_name'],
                'contact_person' => $validated['contact_person'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'mobile' => $validated['mobile'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'country' => $validated['country'],
                'postal_code' => $validated['postal_code'],
                'tax_number' => $validated['tax_number'],
                'registration_number' => $validated['registration_number'],
                'business_type' => $validated['business_type'],
                'website' => $validated['website'],
                'credit_limit' => $validated['credit_limit'],
                'payment_terms' => $validated['payment_terms'],
                'notes' => $validated['notes'],
            ]);

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $path = $document->store('customer_documents', 'public');
                    
                    CustomerRegistrationDocument::create([
                        'customer_registration_id' => $customerRegistration->id,
                        'document_name' => $document->getClientOriginalName(),
                        'document_path' => $path,
                        'document_type' => $document->getClientMimeType(),
                        'file_size' => $document->getSize(),
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('customer-registrations.show', $customerRegistration)
                ->with('success', __('customer_registration.updated_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(CustomerRegistration $customerRegistration)
    {
        if ($customerRegistration->status === 'approved') {
            return redirect()->route('customer-registrations.show', $customerRegistration)
                ->with('error', __('customer_registration.cannot_delete_approved'));
        }

        DB::beginTransaction();
        
        try {
            foreach ($customerRegistration->documents as $document) {
                Storage::disk('public')->delete($document->document_path);
                $document->delete();
            }
            
            $customerRegistration->delete();
            
            DB::commit();

            return redirect()->route('customer-registrations.index')
                ->with('success', __('customer_registration.deleted_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function approve(Request $request, CustomerRegistration $customerRegistration)
    {
        if ($customerRegistration->status !== 'pending' && $customerRegistration->status !== 'under_review') {
            return redirect()->route('customer-registrations.show', $customerRegistration)
                ->with('error', __('customer_registration.cannot_approve'));
        }

        $validated = $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            $customerRegistration->approve(Auth::id(), $validated['approval_notes'] ?? null);
            
            DB::commit();

            return redirect()->route('customer-registrations.show', $customerRegistration)
                ->with('success', __('customer_registration.approved_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, CustomerRegistration $customerRegistration)
    {
        if ($customerRegistration->status !== 'pending' && $customerRegistration->status !== 'under_review') {
            return redirect()->route('customer-registrations.show', $customerRegistration)
                ->with('error', __('customer_registration.cannot_reject'));
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        DB::beginTransaction();
        
        try {
            $customerRegistration->reject(Auth::id(), $validated['rejection_reason']);
            
            DB::commit();

            return redirect()->route('customer-registrations.show', $customerRegistration)
                ->with('success', __('customer_registration.rejected_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function convertToCustomer(CustomerRegistration $customerRegistration)
    {
        if ($customerRegistration->status !== 'approved') {
            return redirect()->route('customer-registrations.show', $customerRegistration)
                ->with('error', __('customer_registration.not_approved'));
        }

        if ($customerRegistration->converted_customer_id) {
            return redirect()->route('customer-registrations.show', $customerRegistration)
                ->with('error', __('customer_registration.already_converted'));
        }

        DB::beginTransaction();
        
        try {
            $customer = $customerRegistration->convertToCustomer();
            
            DB::commit();

            return redirect()->route('customers.show', $customer)
                ->with('success', __('customer_registration.converted_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function deleteDocument(CustomerRegistrationDocument $document)
    {
        $registration = $document->customerRegistration;
        
        if ($registration->status !== 'pending') {
            return redirect()->route('customer-registrations.show', $registration)
                ->with('error', __('customer_registration.cannot_delete_document'));
        }

        Storage::disk('public')->delete($document->document_path);
        $document->delete();

        return redirect()->route('customer-registrations.show', $registration)
            ->with('success', __('customer_registration.document_deleted'));
    }
}
