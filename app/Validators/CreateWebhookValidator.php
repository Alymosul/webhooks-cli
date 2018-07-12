<?php

namespace App\Validators;

use App\Exceptions\CouldNotCreateWebhook;

class CreateWebhookValidator
{
    /**
     * Validates if a callback url is valid.
     *
     * @param $value
     *
     * @return void
     *
     * @throws CouldNotCreateWebhook
     */
    public function validateCallbackUrl($value)
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            throw new CouldNotCreateWebhook;
        }
    }
}
