<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendEmailRequest;
use App\Mail\Admin\GenericAdminEmail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    /**
     * @param  SendEmailRequest  $request
     * @return string[]
     */
    public function sendEmail(SendEmailRequest $request): array
    {
        $addresses = $request->addresses;
        foreach ($addresses as $address) {
            $emailAddress = [];
            preg_match('/<([^>]+)>/', $address, $emailAddress);
            if (count($emailAddress) > 0) {
                $emailAddress = $emailAddress[1];
            } else {
                $emailAddress = $address;
            }
            Mail::to($emailAddress)->send(new GenericAdminEmail($request, $emailAddress));
        }

        return ['message' => 'success'];
    }

    /**
     * @return Collection
     */
    public function getUserOptions(): Collection
    {
        return DB::table('users')->select([DB::raw('CONCAT (name, " <", email, ">") as "option"')])->pluck('option');
    }
}
