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

class Chained implements \FriendsOfTYPO3\FluidCompiler\Fluid\Model\Parsable, \FriendsOfTYPO3\FluidCompiler\Fluid\Model\Dumping
{
    /**
     * @var \FriendsOfTYPO3\FluidCompiler\Fluid\Model\ViewHelper\Inline
     */
    private $viewHelper;

    public function __construct(\FriendsOfTYPO3\FluidCompiler\Fluid\Model\ViewHelper\Inline $viewHelper)
    {
        $this->viewHelper = $viewHelper;
    }

    public function dump(): string
    {
        return $this->viewHelper->dump();
    }
}
