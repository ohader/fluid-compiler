<?php
declare(strict_types=1);

namespace FriendsOfTYPO3\FluidCompiler\Fluid\Model;

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

trait ParsableTrait
{
    private function retrieveDescendants(Parsable $parsable): array
    {
        return $parsable instanceof Descending ? $parsable->getDescendants() : [];
    }

    private function hasSomeChild($criteria, $scope, Parsable ...$items): bool
    {
        foreach ($items as $item) {
            if ($scope === '__CLASS__') {
                if ($criteria === get_class($item)) {
                    return true;
                }
                continue;
            }
            if (!$item instanceof Matchable) {
                continue;
            }
            if ($item->matches($criteria, $scope)) {
                return true;
            }
        }
        return false;
    }

    private function findSomeChild($criteria, $scope, Parsable ...$items): ?Parsable
    {
        foreach ($items as $item) {
            if ($scope === '__CLASS__') {
                if ($criteria === get_class($item)) {
                    return $item;
                }
                continue;
            }
            if (!$item instanceof Matchable) {
                continue;
            }
            if ($item->matches($criteria)) {
                return $item;
            }
        }
        return null;
    }
}
