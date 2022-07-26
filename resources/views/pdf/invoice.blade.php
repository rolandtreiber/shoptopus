@extends('pdf.layout.pdf')

@section('content')
    <div class="content">
        <h2 class="main-title">Receipt</h2>
        <span style="display: inline-block; font-weight: 200;font-size: 14px; font-family: Verdana, Arial, sans-serif; color:#bbb">Date: {{\Carbon\Carbon::parse($invoice->created_at)->format('Y-m-d')}}</span>
        @include('pdf.partials.invoice-customer', ['user' => $invoice->user, 'address' => $invoice->address, 'invoice' => $invoice])
        @include('pdf.partials.invoice-products', ['products' => $invoice->products, 'invoice' => $invoice])
    </div>
@endsection
