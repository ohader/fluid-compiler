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

use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Inline\Ternary\Partable;

class Ternary implements Assignable
{
    /**
     * @var Partable
     */
    private $if;

    /**
     * @var Partable
     */
    private $then;

    /**
     * @var Partable
     */
    private $else;

    public function __construct(Partable ...$parts)
    {
        foreach ($parts as $part) {
            $this->assign($part);
        }
    }

    public function dump(): string
    {
        $elsePrefix = $this->then ? ' ' : '';
        return sprintf(
            '%s %s%s',
            $this->if->dump(),
            $this->then ? '? ' . $this->then->dump() : '?',
            $this->else ? $elsePrefix. ': ' . $this->else->dump() : ''
        );
    }

    private function assign(Partable $part): void
    {
        if ($part instanceof Ternary\IfPart) {
            $this->if = $part;
        } elseif ($part instanceof Ternary\ThenPart) {
            $this->then = $part;
        } elseif ($part instanceof Ternary\ElsePart) {
            $this->else = $part;
        }
    }
}