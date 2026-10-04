<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewsletterRequest extends FormRequest
{
    protected $errorBag = 'newsletter';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['email_newsletter' => ['required', 'email:rfc', 'max:150']];
    }

    public function attributes(): array
    {
        return ['email_newsletter' => 'adresse e-mail'];
    }
}
