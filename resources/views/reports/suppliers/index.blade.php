@extends('layouts.app')

@section('title', 'Supplier Reports')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Supplier Reports</h1>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">By Code/Name</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">View and search suppliers by their code, name, or email address</p>
                        <a href="{{ route('reports.suppliers.by-code-name') }}" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> View Report
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Local Purchases</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">View all local purchase transactions and invoices</p>
                        <a href="{{ route('reports.suppliers.local-purchases') }}" class="btn btn-primary w-100">
                            <i class="fas fa-file-invoice"></i> View Report
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Purchase Summary</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">View summary of all purchases grouped by supplier</p>
                        <a href="{{ route('reports.suppliers.purchase-summary') }}" class="btn btn-primary w-100">
                            <i class="fas fa-chart-bar"></i> View Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection