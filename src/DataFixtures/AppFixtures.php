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
 * Date: 29/05/18
 * Time: 10:47
 */

namespace App\DataFixtures;

use App\Entity\Asset;
use App\Entity\Domain;
use App\Entity\Locale;
use App\Entity\Project;
use App\Entity\Translation;
use App\Entity\User;
use App\Manager\UserManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var Faker\Generator
     */
    protected $faker;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
        $this->faker = Faker\Factory::create();
    }

    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = $this->createUser();
        $projects = $this->loadProjects($manager, $user);
        $domains = $this->loadDomains($manager, $projects);
        $this->loadAssets($manager, $domains);

        $manager->flush();
    }

    private function loadAssets(ObjectManager $manager, array $domains)
    {
        /** @var Domain $domain */
        foreach ($domains as $domain) {
            for ($i = 0; $i < 5; ++$i) {
                $asset = new Asset();
                $asset
                    ->setDomain($domain)
                    ->setResname($this->faker->slug)
                    ->setSource($this->faker->sentence)
                    ->setNotes($this->faker->realText(100))
                ;
                $translation = new Translation();
                $translation
                    ->setLocale($domain->getDefaultLocale())
                    ->setAsset($asset)
                    ->setTarget($this->faker->sentence)
                ;
                $asset->addTranslation($translation);
                $manager->persist($asset);
            }
        }

        $manager->flush();
    }

    private function loadDomains(ObjectManager $manager, array $projects)
    {
        $domains = [];

        /** @var Project $project */
        foreach ($projects as $project) {
            $locale = new Locale();
            $locale
                ->setName($this->faker->word)
                ->setCode($this->faker->randomElement(['fr', 'en', 'de', 'pt', 'lb']))
            ;
            $manager->persist($locale);

            $domain = new Domain();
            $domain
                ->addLocale($locale)
                ->setName($this->faker->word)
                ->setProject($project)
                ->setDefaultLocale($locale)
            ;
            $manager->persist($domain);
            $domains[] = $domain;
        }

        $manager->flush();

        return $domains;
    }

    private function loadProjects(ObjectManager $manager, User $user)
    {
        $projects = [];

        for ($i = 0; $i < 5; ++$i) {
            $project = new Project();
            $project
                ->setName($this->faker->company)
                ->addUser($user)
            ;
            $manager->persist($project);
            $projects[] = $project;
        }

        $manager->flush();

        return $projects;
    }

    private function createUser()
    {
        $user = new User();
        $user
            ->setUsername($this->faker->email)
            ->setPlainPassword('123456') // this is top notch security here
        ;
        $this->userManager->save($user);

        return $user;
    }
}
