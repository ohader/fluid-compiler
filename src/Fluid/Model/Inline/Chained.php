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
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Parsable;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\ViewHelper\Inline;

class Chained implements Parsable, Descending
{
    /**
     * @var Inline
     */
    private $viewHelper;

    public function __construct(Inline $viewHelper)
    {
        $this->viewHelper = $viewHelper;
    }

    public function getDescendants(): array
    {
        return [
            $this->viewHelper
        ];
    }

    /**
     * @return Inline
     */
    public function getViewHelper(): Inline
    {
        return $this->viewHelper;
    }
}
