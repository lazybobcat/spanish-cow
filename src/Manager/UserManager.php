<?php
/**
 * This file is part of the [Bionext] Gateway project.
 *
 * (c) Nvision S.A.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Created by PhpStorm.
 * User: julien
 * Date: 16/03/18
 * Time: 16:16
 */

namespace App\Manager;

use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserManager.
 *
 * @method User create()
 */
class UserManager extends BaseManager
{
    protected $encoder;

    /**
     * UserManager constructor.
     *
     * @param \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $encoder
     * @param string                                                                $class
     * @param \Doctrine\Common\Persistence\ManagerRegistry                          $registry
     * @param null|\Psr\Log\LoggerInterface                                         $logger
     */
    public function __construct(UserPasswordEncoderInterface $encoder, string $class, ManagerRegistry $registry, ?LoggerInterface $logger = null)
    {
        parent::__construct($class, $registry, $logger);

        $this->encoder = $encoder;
    }

    public function save($entity, $andFlush = true)
    {
        $this->updatePassword($entity);

        parent::save($entity, $andFlush);
    }

    public function updatePassword(User $user)
    {
        if (0 !== strlen($password = $user->getPlainPassword())) {
            $user->setPassword($this->getEncoder()->encodePassword($user, $user->getPlainPassword()));
            $user->eraseCredentials();
        }
    }

    /**
     * @return UserPasswordEncoderInterface
     */
    public function getEncoder(): UserPasswordEncoderInterface
    {
        return $this->encoder;
    }

    /**
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return UserManager
     */
    public function setEncoder(UserPasswordEncoderInterface $encoder): UserManager
    {
        $this->encoder = $encoder;

        return $this;
    }
}
