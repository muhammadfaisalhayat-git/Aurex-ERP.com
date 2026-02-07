<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Models\SupplierRegistration;
use App\Models\SupplierRegistrationDocument;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SupplierRegistrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:supplier_registration.view')->only(['index', 'show']);
        $this->middleware('permission:supplier_registration.create')->only(['create', 'store']);
        $this->middleware('permission:supplier_registration.approve')->only(['approve', 'reject']);
        $this->middleware('permission:supplier_registration.delete')->only(['destroy']);
    }

    public function index()
    {
        $registrations = SupplierRegistration::with(['creator', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('purchases.supplier_registrations.index', compact('registrations'));
    }

    public function create()
    {
        return view('purchases.supplier_registrations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:supplier_registrations',
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
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:100',
            'iban' => 'nullable|string|max:50',
            'products_services' => 'nullable|string',
            'notes' => 'nullable|string',
            'documents' => 'nullable|array',
            'documents.*' => 'file|max:10240',
        ]);

        DB::beginTransaction();
        
        try {
            $registration = SupplierRegistration::create([
                'registration_code' => SupplierRegistration::generateRegistrationCode(),
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
                'bank_name' => $validated['bank_name'],
                'bank_account' => $validated['bank_account'],
                'iban' => $validated['iban'],
                'products_services' => $validated['products_services'],
                'notes' => $validated['notes'],
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $path = $document->store('supplier_documents', 'public');
                    
                    SupplierRegistrationDocument::create([
                        'supplier_registration_id' => $registration->id,
                        'document_name' => $document->getClientOriginalName(),
                        'document_path' => $path,
                        'document_type' => $document->getClientMimeType(),
                        'file_size' => $document->getSize(),
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('supplier-registrations.show', $registration)
                ->with('success', __('supplier_registration.created_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(SupplierRegistration $supplierRegistration)
    {
        $supplierRegistration->load(['documents', 'creator', 'approver']);
        return view('purchases.supplier_registrations.show', compact('supplierRegistration'));
    }

    public function edit(SupplierRegistration $supplierRegistration)
    {
        if ($supplierRegistration->status !== 'pending') {
            return redirect()->route('supplier-registrations.show', $supplierRegistration)
                ->with('error', __('supplier_registration.cannot_edit_processed'));
        }

        return view('purchases.supplier_registrations.edit', compact('supplierRegistration'));
    }

    public function update(Request $request, SupplierRegistration $supplierRegistration)
    {
        if ($supplierRegistration->status !== 'pending') {
            return redirect()->route('supplier-registrations.show', $supplierRegistration)
                ->with('error', __('supplier_registration.cannot_edit_processed'));
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:supplier_registrations,email,' . $supplierRegistration->id,
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
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:100',
            'iban' => 'nullable|string|max:50',
            'products_services' => 'nullable|string',
            'notes' => 'nullable|string',
            'documents' => 'nullable|array',
            'documents.*' => 'file|max:10240',
        ]);

        DB::beginTransaction();
        
        try {
            $supplierRegistration->update([
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
                'bank_name' => $validated['bank_name'],
                'bank_account' => $validated['bank_account'],
                'iban' => $validated['iban'],
                'products_services' => $validated['products_services'],
                'notes' => $validated['notes'],
            ]);

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $path = $document->store('supplier_documents', 'public');
                    
                    SupplierRegistrationDocument::create([
                        'supplier_registration_id' => $supplierRegistration->id,
                        'document_name' => $document->getClientOriginalName(),
                        'document_path' => $path,
                        'document_type' => $document->getClientMimeType(),
                        'file_size' => $document->getSize(),
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('supplier-registrations.show', $supplierRegistration)
                ->with('success', __('supplier_registration.updated_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(SupplierRegistration $supplierRegistration)
    {
        if ($supplierRegistration->status === 'approved') {
            return redirect()->route('supplier-registrations.show', $supplierRegistration)
                ->with('error', __('supplier_registration.cannot_delete_approved'));
        }

        DB::beginTransaction();
        
        try {
            foreach ($supplierRegistration->documents as $document) {
                Storage::disk('public')->delete($document->document_path);
                $document->delete();
            }
            
            $supplierRegistration->delete();
            
            DB::commit();

            return redirect()->route('supplier-registrations.index')
                ->with('success', __('supplier_registration.deleted_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function approve(Request $request, SupplierRegistration $supplierRegistration)
    {
        if ($supplierRegistration->status !== 'pending' && $supplierRegistration->status !== 'under_review') {
            return redirect()->route('supplier-registrations.show', $supplierRegistration)
                ->with('error', __('supplier_registration.cannot_approve'));
        }

        $validated = $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            $supplierRegistration->approve(Auth::id(), $validated['approval_notes'] ?? null);
            
            DB::commit();

            return redirect()->route('supplier-registrations.show', $supplierRegistration)
                ->with('success', __('supplier_registration.approved_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, SupplierRegistration $supplierRegistration)
    {
        if ($supplierRegistration->status !== 'pending' && $supplierRegistration->status !== 'under_review') {
            return redirect()->route('supplier-registrations.show', $supplierRegistration)
                ->with('error', __('supplier_registration.cannot_reject'));
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        DB::beginTransaction();
        
        try {
            $supplierRegistration->reject(Auth::id(), $validated['rejection_reason']);
            
            DB::commit();

            return redirect()->route('supplier-registrations.show', $supplierRegistration)
                ->with('success', __('supplier_registration.rejected_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function convertToVendor(SupplierRegistration $supplierRegistration)
    {
        if ($supplierRegistration->status !== 'approved') {
            return redirect()->route('supplier-registrations.show', $supplierRegistration)
                ->with('error', __('supplier_registration.not_approved'));
        }

        if ($supplierRegistration->converted_vendor_id) {
            return redirect()->route('supplier-registrations.show', $supplierRegistration)
                ->with('error', __('supplier_registration.already_converted'));
        }

        DB::beginTransaction();
        
        try {
            $vendor = $supplierRegistration->convertToVendor();
            
            DB::commit();

            return redirect()->route('vendors.show', $vendor)
                ->with('success', __('supplier_registration.converted_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function deleteDocument(SupplierRegistrationDocument $document)
    {
        $registration = $document->supplierRegistration;
        
        if ($registration->status !== 'pending') {
            return redirect()->route('supplier-registrations.show', $registration)
                ->with('error', __('supplier_registration.cannot_delete_document'));
        }

        Storage::disk('public')->delete($document->document_path);
        $document->delete();

        return redirect()->route('supplier-registrations.show', $registration)
            ->with('success', __('supplier_registration.document_deleted'));
    }
}
