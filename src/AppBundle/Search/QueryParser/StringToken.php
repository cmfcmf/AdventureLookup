<?php

declare(strict_types=1);

namespace AppBundle\Search\QueryParser;

class StringToken extends Token
{
    protected const TOKEN_KIND = 'string';
}