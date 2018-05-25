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
 * Time: 15:27
 */

namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\Domain;
use App\Manager\DomainManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DomainCollectionProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var DomainManager
     */
    protected $domainManager;

    /**
     * @var iterable
     */
    protected $itemExtensions;

    public function __construct(TokenStorageInterface $tokenStorage, DomainManager $domainManager, iterable $itemExtensions)
    {
        $this->tokenStorage = $tokenStorage;
        $this->domainManager = $domainManager;
        $this->itemExtensions = $itemExtensions;
    }

    /**
     * Retrieves a collection.
     *
     * @throws ResourceClassNotSupportedException
     *
     * @return array|\Traversable
     */
    public function getCollection(string $resourceClass, string $operationName = null)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $qb = $this->domainManager->getQueryBuilder();
        $queryNameGenerator = new QueryNameGenerator();

        // Only fetch projects associated to the current user
        $qb
            ->innerJoin('d.project', 'p')
            ->andWhere(':user MEMBER OF p.users')
            ->setParameter('user', $user)
        ;

        /** @var QueryCollectionExtensionInterface $extension */
        foreach ($this->itemExtensions as $extension) {
            $extension->applyToCollection($qb, $queryNameGenerator, $resourceClass, $operationName);
        }

        return $qb->getQuery()->getResult();
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Domain::class === $resourceClass;
    }
}