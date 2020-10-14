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

use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Descending;

class Wrapped implements Assignable, Descending
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

    public function getDescendants(): array
    {
        return array_merge(
            [$this->value],
            $this->chains
        );
    }
}
