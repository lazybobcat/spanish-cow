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
 * Time: 16:36
 */

namespace App\DataProvider;

use App\Entity\Asset;
use App\Manager\AssetManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AssetCollectionProvider extends BaseCollectionProvider
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var AssetManager
     */
    protected $assetManager;

    /**
     * @var iterable
     */
    protected $extensions;

    public function __construct(TokenStorageInterface $tokenStorage, AssetManager $assetManager, iterable $extensions)
    {
        $this->tokenStorage = $tokenStorage;
        $this->assetManager = $assetManager;
        $this->extensions = $extensions;
    }

    /**
     * Retrieves a collection.
     *
     * @return array|\Traversable
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $qb = $this->assetManager->getQueryBuilder();

        // Only fetch projects associated to the current user
        $qb
            ->innerJoin('a.domain', 'd')
            ->innerJoin('d.project', 'p')
            ->andWhere(':user MEMBER OF p.users')
            ->setParameter('user', $user)
        ;

        return $this->handleExtensions($this->extensions, $qb, $resourceClass, $operationName, $context);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Asset::class === $resourceClass;
    }
}
