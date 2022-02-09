<?php

declare(strict_types=1);

namespace Mautic\LeadBundle\Provider;

interface FilterOperatorProviderInterface
{
    /**
     * Finds all operators and reutrn them in an array.
     */
    public function getAllOperators(): array;
}
