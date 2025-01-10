<?php

namespace App\Rules;

use GuzzleHttp\Client;
use Illuminate\Contracts\Validation\Rule;


class Recaptcha implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    
    protected $recaptcha_secret;

    public function __construct($recaptcha_secret)
    {
        $this->recaptcha_secret = $recaptcha_secret; 
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $client = new Client;
        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'form_params' =>
                    [
                        'secret' => $this->recaptcha_secret,
                        'response' => $value
                    ]
            ]
        );
        
        $body = json_decode((string)$response->getBody());
        
        return $body->success;
    }

    /**
     * Get the validation error message.
     *
     * @return string
    */

    public function message()
    {
        return __('common.recaptcha_failed');
    }
}
