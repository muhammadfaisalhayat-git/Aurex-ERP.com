<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\PaymentVoucher;
use App\Models\BankAccount;
use App\Models\ChartOfAccount;
use App\Models\Vendor;
use App\Services\AccountingService;
use Illuminate\Http\Request;

class PaymentVoucherController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index()
    {
        $vouchers = PaymentVoucher::with(['bankAccount', 'vendor'])->orderBy('voucher_date', 'desc')->paginate(15);
        return view('finance.vouchers.payment.index', compact('vouchers'));
    }

    public function create()
    {
        $bankAccounts = BankAccount::where('is_active', true)->get();
        $vendors = Vendor::all();
        $coaAccounts = ChartOfAccount::posting()->get();
        return view('finance.vouchers.payment.create', compact('bankAccounts', 'vendors', 'coaAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'voucher_date' => 'required|date',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'payee_name' => 'required',
            'payment_method' => 'required',
            'reference_number' => 'nullable',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable',
            'beneficiary_id' => 'nullable',
            'beneficiary_type' => 'nullable|string',
            'chart_of_account_id' => [
                'required',
                'exists:chart_of_accounts,id',
                function ($attribute, $value, $fail) use ($request) {
            $account = ChartOfAccount::find($value);
            if ($account && $account->sub_ledger_type && !$request->beneficiary_id) {
                $fail('Beneficiary is required for selected GL Account.');
            }
        }
            ],
        ]);

        $validated['company_id'] = session('active_company_id');
        $validated['created_by'] = auth()->id();
        $validated['voucher_number'] = 'PV-' . date('Ymd') . '-' . rand(1000, 9999);
        $validated['status'] = 'draft';

        $voucher = PaymentVoucher::create($validated);

        return redirect()->route('finance.vouchers.payment.show', $voucher)->with('success', 'Payment Voucher created');
    }

    public function show(PaymentVoucher $payment)
    {
        return view('finance.vouchers.payment.show', [
            'paymentVoucher' => $payment
        ]);
    }

    public function post(PaymentVoucher $payment)
    {
        if ($this->accountingService->postPaymentVoucher($payment)) {
            return back()->with('success', 'Voucher posted to ledger successfully');
        }
        return back()->with('error', 'Failed to post voucher');
    }
}
