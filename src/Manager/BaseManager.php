<?php

namespace App\Manager;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

abstract class BaseManager implements BaseManagerInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $logger;

    /**
     * @param string                        $class
     * @param ManagerRegistry               $registry
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct($class, ManagerRegistry $registry, LoggerInterface $logger = null)
    {
        $this->class = $class;
        $this->registry = $registry;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * @return ObjectManager
     *
     * @throws \RuntimeException
     */
    public function getObjectManager(): ObjectManager
    {
        $manager = $this->registry->getManagerForClass($this->class);

        if (!$manager) {
            throw new \RuntimeException(sprintf('Unable to find the mapping information for the class %s.'
                ." Please check the 'auto_mapping' option (http://symfony.com/doc/current/reference/configuration/doctrine.html#configuration-overview)"
                ." or add the bundle to the 'mappings' section in the doctrine configuration.", $this->class));
        }

        return $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return new $this->class();
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->getRepository()->find($id);
    }

    public function findOneById($id)
    {
        return $this->getRepository()->findOneBy(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function save($entity, $andFlush = true)
    {
        $this->checkObject($entity);

        $this->getObjectManager()->persist($entity);

        if ($andFlush) {
            $this->getObjectManager()->flush();
        }
    }

    public function flush()
    {
        $this->getObjectManager()->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($entity, $andFlush = true)
    {
        $this->checkObject($entity);

        $this->getObjectManager()->remove($entity);

        if ($andFlush) {
            $this->getObjectManager()->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTableName()
    {
        return $this->getObjectManager()->getClassMetadata($this->class)->table['name'];
    }

    /**
     * Returns the related Object Repository.
     *
     * @return ObjectRepository
     */
    protected function getRepository()
    {
        return $this->getObjectManager()->getRepository($this->class);
    }

    /**
     * @param $object
     *
     * @throws \InvalidArgumentException
     */
    protected function checkObject($object)
    {
        if (!$object instanceof $this->class) {
            throw new \InvalidArgumentException(sprintf(
                'Object must be instance of %s, %s given',
                $this->class, is_object($object) ? get_class($object) : gettype($object)
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->getEntityManager()->getConnection();
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->getObjectManager();
    }

    /**
     * Make sure the code is compatible with legacy code.
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if ('em' == $name) {
            return $this->getObjectManager();
        }

        throw new \RuntimeException(sprintf('The property %s does not exists', $name));
    }

    /**
     * {@inheritdoc}
     */
    public function __call($name, $arguments)
    {
        if (!method_exists($this, $name)) {
            if (method_exists($this->getRepository(), $name)) {
                return call_user_func_array([$this->getRepository(), $name], $arguments);
            }
        }
    }
}
