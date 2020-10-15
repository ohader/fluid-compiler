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

use FriendsOfTYPO3\FluidCompiler\Fluid\Model;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Parsable;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\ParsableTrait;

class RayVisitor implements Visitable
{
    use ParsableTrait;

    private const IGNORE = [
        Model\Token::class,
        Model\Tree::class,
    ];
    private const SELF_TERMINATING = [
        Model\Node\SelfClosing::class,
        Model\ViewHelper\SelfClosing::class,
        Model\Attribute::class,
        Model\Inline\Wrapped::class,
    ];

    private $ray = [];

    /**
     * @return Parsable[]
     */
    public function get(): array
    {
        return $this->ray;
    }

    public function enter(Parsable $parsable, ?string $index): ?Parsable
    {
        if (!$this->isA($parsable, self::IGNORE)) {
            $this->ray[] = $parsable;
        }
        return null;
    }

    public function leave(Parsable $parsable, ?string $index): ?Parsable
    {
        if ($this->isA($parsable, self::IGNORE)) {
            return null;
        }
        if ($parsable instanceof Model\Closable) {
            $this->close($parsable);
        } elseif ($this->isA($parsable, self::SELF_TERMINATING)){
            $this->prune($parsable);
        }
        return null;
    }

    private function prune(Parsable $parsable): void
    {
        $index = array_search($parsable, $this->ray, true);
        if ($index === false) {
            throw new \LogicException('Cannot prune missing parsable', 1602755622);
        }
        // remove all items after (including) index
        array_splice($this->ray, $index);
    }

    private function close(Model\Closable $parsable): void
    {
        foreach (array_reverse($this->ray, true) as $index => $item) {
            if ($parsable->closes($item)) {
                // remove all items after (including) index
                array_splice($this->ray, $index);
                return;
            }
        }
        throw new \LogicException(
            sprintf('Cannot resolve counterpart for %s', get_class($parsable)),
            1602755623
        );
    }

    private function isA(Parsable $parsable, array $classNames): bool
    {
        foreach ($classNames as $className) {
            if (is_a($parsable, $className)) {
                return true;
            }
        }
        return false;
    }
}
