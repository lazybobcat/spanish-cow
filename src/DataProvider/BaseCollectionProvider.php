<?php
/**
 * This file is part of the spanish-cow project.
 *
 * (c) Nvision S.A.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * Created by PhpStorm.
 * User: loicb
 * Date: 25/05/18
 * Time: 18:15
 */

namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Doctrine\ORM\QueryBuilder;

abstract class BaseCollectionProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    protected function handleExtensions(\Traversable $extensions, QueryBuilder $qb, $resourceClass, $operationName, array $context = [])
    {
        $queryNameGenerator = new QueryNameGenerator();

        /** @var QueryCollectionExtensionInterface $extension */
        foreach ($extensions as $extension) {
            if ($extension instanceof ContextAwareQueryCollectionExtensionInterface) {
                $extension->applyToCollection($qb, $queryNameGenerator, $resourceClass, $operationName, $context);
            } else {
                $extension->applyToCollection($qb, $queryNameGenerator, $resourceClass, $operationName);
                if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass, $operationName)) {
                    return $extension->getResult($qb);
                }
            }
        }

        return $qb->getQuery()->getResult();
    }
}
