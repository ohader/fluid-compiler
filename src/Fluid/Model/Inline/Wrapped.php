<?php
declare(strict_types=1);

namespace FriendsOfTYPO3\FluidCompiler\Fluid\Model\Inline;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

class Wrapped implements Assignable
{
    /**
     * @var Assignable
     */
    private $value;

    /**
     * @var Chained[]
     */
    private $chains;

    public function __construct(Assignable $value, Chained ...$chains)
    {
        $this->value = $value;
        $this->chains = $chains;
    }

    public function dump(): string
    {
        $chains = array_map(
            function (Chained $chained) {
                return $chained->dump();
            },
            $this->chains
        );
        array_unshift($chains, '');
        return sprintf(
            '{%s%s}',
            $this->value->dump(),
            implode(' -> ', $chains)
        );
    }
}
