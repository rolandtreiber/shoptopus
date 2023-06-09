<?php

namespace App\Rules;

use Closure;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\Extra\SpoofCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\NoRFCWarningsValidation;
use Egulias\EmailValidator\Validation\RFCValidation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Illuminate\Validation\Concerns\FilterEmailValidation;

class ContainsEmail implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param \Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $emailAddress = [];
        preg_match('/<([^>]+)>/', $value, $emailAddress);
        if (count($emailAddress) > 0) {
            $emailAddress = $emailAddress[1];
        } else {
            $emailAddress = $value;
        }

        if (! is_string($value) && ! (is_object($value) && method_exists($value, '__toString'))) {
            $fail('Invalid email');
        }

        $result = (new EmailValidator)->isValid($value, new MultipleValidationWithAnd([new RFCValidation]));

        if ($result) {
            $fail('Invalid email');
        }
    }
}
