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
 * Date: 28/05/18
 * Time: 15:31
 */

namespace App\DataProvider;

use App\Entity\Translation;
use App\Manager\TranslationManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TranslationCollectionProvider extends BaseCollectionProvider
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var TranslationManager
     */
    protected $translationManager;

    /**
     * @var iterable
     */
    protected $extensions;

    public function __construct(TokenStorageInterface $tokenStorage, TranslationManager $translationManager, iterable $extensions)
    {
        $this->tokenStorage = $tokenStorage;
        $this->translationManager = $translationManager;
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
        $qb = $this->translationManager->getQueryBuilder();

        // Only fetch projects associated to the current user
        $qb
            ->innerJoin('t.asset', 'a')
            ->innerJoin('a.domain', 'd')
            ->innerJoin('d.project', 'p')
            ->andWhere(':user MEMBER OF p.users')
            ->setParameter('user', $user)
        ;

        return $this->handleExtensions($this->extensions, $qb, $resourceClass, $operationName, $context);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Translation::class === $resourceClass;
    }
}
