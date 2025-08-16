<?php
namespace App\Services\Assistant;

class ContactInfo
{
    public function get(): array
    {
        $phone = config('contact.phone', env('CONTACT_PHONE'));
        $email = config('contact.email', env('CONTACT_EMAIL'));
        $hours = config('contact.hours', env('CONTACT_HOURS'));
        $url   = config('contact.url',   env('CONTACT_URL', route('home')));
        return [
            'phone' => $phone ?: null,
            'email' => $email ?: null,
            'hours' => $hours ?: null,
            'url'   => $url   ?: null,
        ];
    }
}
