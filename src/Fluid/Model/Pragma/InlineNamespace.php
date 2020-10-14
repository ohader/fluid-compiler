<?php
declare(strict_types=1);

namespace FriendsOfTYPO3\FluidCompiler\Fluid\Model\Pragma;

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

use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Parsable;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Token;

class InlineNamespace implements Parsable
{
    private $prefix;
    private $reference;

    public function __construct(Token $prefix, Token $reference = null)
    {
        $this->prefix = $prefix;
        $this->reference = $reference;
    }

    /**
     * @return Token
     */
    public function getPrefix(): Token
    {
        return $this->prefix;
    }

    /**
     * @return Token|null
     */
    public function getReference(): ?Token
    {
        return $this->reference;
    }
}
