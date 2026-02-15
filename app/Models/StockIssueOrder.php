class StockIssueOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'document_number',
        'issue_date',
        'warehouse_id',
        'reference_type',
        'reference_id',
        'reference_number',
        'issue_type',
        'status',
        'notes',
        'created_by',
        'posted_by',
        'posted_at'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'posted_at' => 'datetime'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(StockIssueOrderItem::class);
    }

    public function isPosted()
    {
        return $this->status === 'posted';
    }

    public function post()
    {
        if ($this->isPosted()) {
            return false;
        }

        $this->status = 'posted';
        $this->posted_by = auth()->id();
        $this->posted_at = now();
        $this->save();

        // Update stock
        foreach ($this->items as $item) {
            $stockBalance = StockBalance::firstOrCreate(
                [
                    'product_id' => $item->product_id,
                    'warehouse_id' => $this->warehouse_id,
                ],
                [
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                    'available_quantity' => 0,
                    'average_cost' => 0,
                ]
            );

            $stockBalance->quantity -= $item->quantity;
            $stockBalance->available_quantity -= $item->quantity;
            $stockBalance->save();

            // Create stock ledger entry
            StockLedger::create([
                'product_id' => $item->product_id,
                'warehouse_id' => $this->warehouse_id,
                'transaction_date' => $this->issue_date,
                'reference_type' => $this->reference_type ?? 'stock_issue',
                'reference_id' => $this->reference_id ?? $this->id,
                'reference_number' => $this->reference_number ?? $this->document_number,
                'movement_type' => 'out',
                'quantity' => $item->quantity,
                'unit_cost' => $item->product->average_cost ?? 0,
                'total_cost' => ($item->product->average_cost ?? 0) * $item->quantity,
                'balance_quantity' => $stockBalance->quantity,
                'notes' => $this->notes ?? 'Stock Issue: ' . $this->document_number,
                'created_by' => auth()->id(),
            ]);
        }

        return true;
    }

    public function unpost()
    {
        if (!$this->isPosted()) {
            return false;
        }

        // Reverse stock entries
        foreach ($this->items as $item) {
            $stockBalance = StockBalance::where('product_id', $item->product_id)
                ->where('warehouse_id', $this->warehouse_id)
                ->first();

            if ($stockBalance) {
                $stockBalance->quantity += $item->quantity;
                $stockBalance->available_quantity += $item->quantity;
                $stockBalance->save();
            }
        }

        $this->status = 'draft';
        $this->posted_by = null;
        $this->posted_at = null;
        $this->save();

        return true;
    }
}
