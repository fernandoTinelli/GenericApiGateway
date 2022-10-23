<?php

namespace App\Validator;

use App\Gateway\Request\JsonServiceRequest;

abstract class AbstractRequestValidator
{
    abstract public function validate(JsonServiceRequest $request): Violations;
}