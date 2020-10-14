<?php
declare(strict_types=1);

namespace FriendsOfTYPO3\FluidCompiler\Fluid\Visitor;

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

use FriendsOfTYPO3\FluidCompiler\Fluid\Exporter\StackedDumping;
use FriendsOfTYPO3\FluidCompiler\Fluid\Exporter\DumpingExporter;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Parsable;

class DumpingVisitor extends SilentVisitor
{
    /**
     * @var DumpingExporter
     */
    private $exporter;

    /**
     * @var string[]
     */
    private $stack = [[]];
    private $stackPointer = 0;

    public function __construct(StackedDumping $exporter)
    {
        $this->exporter = $exporter;
    }

    public function get(): string
    {
        return implode('', $this->currentStack());
    }

    public function enter(Parsable $parsable, ?string $index): ?Parsable
    {
        $this->pushStack();
        return null;
    }

    public function leave(Parsable $parsable, ?string $index): ?Parsable
    {
        $stack = $this->popStack();
        $this->appendStack(
            $this->exporter->dumpStacked($parsable, $stack),
            $index
        );
        return null;
    }

    private function pushStack(): void
    {
        $this->stack[] = [];
        $this->stackPointer++;
    }

    private function popStack(): array
    {
        $stack = array_pop($this->stack);
        $this->stackPointer--;
        return $stack;
    }

    /**
     * @return string
     */
    private function currentStack(): array
    {
        return $this->stack[$this->stackPointer];
    }

    private function appendStack(string $value, ?string $index): void
    {
        if ($index === null) {
            $this->stack[$this->stackPointer][] = $value;
        } else {
            $this->stack[$this->stackPointer][$index] = $value;
        }
    }
}
