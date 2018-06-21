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

use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\Domain;
use App\Manager\DomainManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DomainCollectionProvider extends BaseCollectionProvider
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
    protected $extensions;

    public function __construct(TokenStorageInterface $tokenStorage, DomainManager $domainManager, iterable $extensions)
    {
        $this->tokenStorage = $tokenStorage;
        $this->domainManager = $domainManager;
        $this->extensions = $extensions;
    }

    /**
     * Retrieves a collection.
     *
     * @throws ResourceClassNotSupportedException
     *
     * @return array|\Traversable
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $qb = $this->domainManager->getQueryBuilder();

        // Only fetch projects associated to the current user
        $qb
            ->innerJoin('d.project', 'p')
            ->andWhere(':user MEMBER OF p.users')
            ->setParameter('user', $user)
        ;

        return $this->handleExtensions($this->extensions, $qb, $resourceClass, $operationName, $context);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Domain::class === $resourceClass;
    }
}
