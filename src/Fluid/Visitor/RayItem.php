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

use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Closable;
use FriendsOfTYPO3\FluidCompiler\Fluid\Model\Parsable;

class RayItem implements \Countable
{
    /**
     * @var Parsable[]
     */
    private $ray = [];

    /**
     * @return Parsable[]
     */
    public function get(): array
    {
        return $this->ray;
    }

    public function count(): int
    {
        return count($this->ray);
    }

    public function append(Parsable $parsable): self
    {
        $target = clone $this;
        $target->ray[] = $parsable;
        return $target;
    }

    public function reverse(): self
    {
        $target = clone $this;
        $target->ray = array_reverse($target->ray);
        return $target;
    }

    public function without(Parsable $without): self
    {
        $target = clone $this;
        $target->ray = array_filter(
            $target->ray,
            function (Parsable $item) use ($without) {
                return $item !== $without;
            }
        );
        return $target;
    }

    public function pruneBefore(Parsable $parsable): self
    {
        $index = array_search($parsable, $this->ray, true);
        if ($index === false) {
            throw new \LogicException('Cannot prune missing parsable', 1602755622);
        }
        $target = clone $this;
        // keep ray items before $before (remove everything after index of $before)
        array_splice($target->ray, $index);
        return $target;
    }

    public function pruneAfter(Parsable $parsable): self
    {
        $index = array_search($parsable, $this->ray, true);
        if ($index === false) {
            throw new \LogicException('Cannot prune missing parsable', 1602755622);
        }
        $target = clone $this;
        // keep ray items after $after (remove everything before index of $after)
        array_splice($target->ray, 0, $index);
        return $target;
    }

    public function close(Closable $parsable): self
    {
        foreach (array_reverse($this->ray, true) as $index => $item) {
            if ($parsable->closes($item)) {
                $target = clone $this;
                // remove all items after (including) index
                array_splice($target->ray, $index);
                return $target;
            }
        }
        throw new \LogicException(
            sprintf('Cannot resolve counterpart for %s', get_class($parsable)),
            1602755623
        );
    }
}
