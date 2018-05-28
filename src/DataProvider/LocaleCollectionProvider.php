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
 * Time: 16:07
 */

namespace App\DataProvider;

use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\Locale;
use App\Manager\LocaleManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LocaleCollectionProvider extends BaseCollectionProvider
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var LocaleManager
     */
    protected $localeManager;

    /**
     * @var iterable
     */
    protected $extensions;

    public function __construct(TokenStorageInterface $tokenStorage, LocaleManager $localeManager, iterable $extensions)
    {
        $this->tokenStorage = $tokenStorage;
        $this->localeManager = $localeManager;
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
        $qb = $this->localeManager->getQueryBuilder();

        // Only fetch projects associated to the current user
        $qb
            ->innerJoin('l.domain', 'd')
            ->innerJoin('d.project', 'p')
            ->andWhere(':user MEMBER OF p.users')
            ->setParameter('user', $user)
        ;

        return $this->handleExtensions($this->extensions, $qb, $resourceClass, $operationName, $context);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Locale::class === $resourceClass;
    }
}