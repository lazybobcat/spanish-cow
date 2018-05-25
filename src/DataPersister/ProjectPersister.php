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
 * Time: 14:35
 */

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Project;
use App\Entity\User;
use App\Manager\ProjectManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProjectPersister implements DataPersisterInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var ProjectManager
     */
    protected $projectManager;

    public function __construct(TokenStorageInterface $tokenStorage, ProjectManager $projectManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->projectManager = $projectManager;
    }

    /**
     * Is the data supported by the persister?
     *
     * @param Project $data
     *
     * @return bool
     */
    public function supports($data): bool
    {
        return $data instanceof Project;
    }

    /**
     * Persists the data.
     *
     * @param Project $project
     */
    public function persist($project)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof User) {
            $project->addUser($user);
            $this->projectManager->save($project);
        }
    }

    /**
     * Removes the data.
     *
     * @param Project $project
     */
    public function remove($project)
    {
        $this->projectManager->delete($project);
    }
}