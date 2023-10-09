<?php

namespace MityDigital\StatamicLogger\Exceptions;

use Exception;

class SupplementaryDataNotArray extends Exception
{
    protected $message = 'The supplementary data returned is not an array.';
}
