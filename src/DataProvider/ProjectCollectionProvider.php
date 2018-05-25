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
 * Time: 12:05
 */

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\Project;
use App\Manager\ProjectManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProjectCollectionProvider extends BaseCollectionProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var ProjectManager
     */
    protected $projectManager;

    /**
     * @var iterable
     */
    protected $extensions;

    public function __construct(TokenStorageInterface $tokenStorage, ProjectManager $projectManager, iterable $extensions)
    {
        $this->tokenStorage = $tokenStorage;
        $this->projectManager = $projectManager;
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
        $qb = $this->projectManager->getQueryBuilder();

        // Only fetch projects associated to the current user
        $qb
            ->andWhere(':user MEMBER OF p.users')
            ->setParameter('user', $user)
        ;

        return $this->handleExtensions($this->extensions, $qb, $resourceClass, $operationName, $context);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Project::class === $resourceClass;
    }
}