<h5 style="font-size: 14px;margin-bottom: 5px">Products</h5>
<table style="width: 700px">
    <thead style="border-bottom: 1px solid black">
    <tr style="background-color: #ebebeb">
        <td></td>
        <td>Product Name</td>
        <td>SKU</td>
        <td>Quantity</td>
        <td>Unit Price</td>
        <td>Price</td>
    </tr>
    </thead>
    <tbody>
    @foreach($products as $product)
        <tr>
            <td><img src="data:image/jpg;base64,{{$product['image']}}" width="20" height="20"/></td>
            <td>{{$product['name']}}</td>
            <td>{{$product['sku']}}</td>
            <td>{{$product['amount']}}</td>
            @if(config('app.default_currency')['side'] === "left")
                <td>{{config('app.default_currency')['symbol']}}{{$product['unit_price']}}</td>
            @else
                <td>{{$product['unit_price']}}{{config('app.default_currency')['symbol']}}</td>
            @endif
            @if(config('app.default_currency')['side'] === "left")
                <td>{{config('app.default_currency')['symbol']}}{{$product['final_price']}}</td>
            @else
                <td>{{$product['final_price']}}{{config('app.default_currency')['symbol']}}</td>
            @endif
        </tr>
    @endforeach
    <tr>
        <td colspan="6" style="border-bottom: 1px dotted black"></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>Subtotal</td>
        <td>£{{$invoice->totals->subtotal}}</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>Delivery</td>
        <td>£{{$invoice->totals->delivery}}</td>
    </tr>
    @if ($invoice->totals->applied_discount > 0)
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Discount</td>
            <td>£{{$invoice->totals->applied_discount}}</td>
        </tr>
    @endif
    <tr>
        <td colspan="6" style="border-bottom: 1px dotted black"></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="font-size: 14px">Total</td>
        <td style="font-size: 14px">£{{$invoice->totals->total_payable}}</td>
    </tr>
    </tbody>
</table>
