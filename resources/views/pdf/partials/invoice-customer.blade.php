<table style="width: 600px">
    <tbody>
    <tr>
        <td>
            {{-- Customer--}}
            <h5 style="font-size: 14px;margin-bottom: 5px">{{env('APP_NAME')}}</h5>
            <table style="width: 300px">
                <tbody>
                <tr>
                    <td><strong>{{$user->name}} ({{$user->email}})</strong></td>
                    <td></td>
                </tr>
                <tr>
                    <td><a href="https://www.google.com/maps/place/{{$address->post_code}}" style="color:darkblue">{{$address->post_code}}, {{$address->town}}, {{$address->address_line_1}} {{$address->address_line_2}}</a></td>
                </tr>
                <tr>
                    <td>{{$user->phone}}</td>
                </tr>
                </tbody>
            </table>

        </td>
        <td>

            {{-- Address--}}
            <table style="width: 350px; margin-top: 40px">
                <tbody>
                <tr>
                    <td>Invoice ID</td>
                    <td><strong>{{$invoice->slug}}</strong></td>
                </tr>
                <tr>
                    <td>Customer reference</td>
                    <td><strong>{{$user->client_ref}}</strong></td>
                </tr>
                <tr>
                    <td>Date of purchase</td>
                    <td><strong>{{\Carbon\Carbon::parse($invoice->created_at)->format('Y-m-d H:i')}}</strong></td>
                </tr>
                <tr>
                    <td>Payment type</td>
                    <td>
                        <strong>
                            @switch($invoice->payment->source->payment_method_id)
                                @case(1)
                                    {{'Card'}}
                                @break
                                @case(2)
                                    {{'PayPal'}}
                                @break
                                @case(3)
                                    {{'Google Pay'}}
                                @break
                                @case(4)
                                    {{'Apple Pay'}}
                                @break
                                @case(5)
                                    {{'Amazon Pay'}}
                            @endswitch
                        </strong>
                    </td>
                </tr>
                </tbody>
            </table>

        </td>
    </tr>
    </tbody>
</table>
