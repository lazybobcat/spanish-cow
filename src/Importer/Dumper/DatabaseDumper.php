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
 * Date: 05/06/18
 * Time: 16:24
 */

namespace App\Importer\Dumper;

use App\Entity\Asset;
use App\Entity\Domain;
use App\Entity\Locale;
use App\Entity\Translation;
use App\Manager\AssetManager;
use App\Manager\DomainManager;
use App\Manager\LocaleManager;
use App\Manager\TranslationManager;
use App\Model\Import;
use Symfony\Component\Translation\Dumper\DumperInterface;
use Symfony\Component\Translation\MessageCatalogue;

class DatabaseDumper implements DumperInterface
{
    /**
     * @var DomainManager
     */
    private $domainManager;

    /**
     * @var AssetManager
     */
    private $assetManager;

    /**
     * @var TranslationManager
     */
    private $translationManager;

    /**
     * @var LocaleManager
     */
    private $localeManager;

    public function __construct(DomainManager $domainManager, AssetManager $assetManager, TranslationManager $translationManager, LocaleManager $localeManager)
    {
        $this->domainManager = $domainManager;
        $this->assetManager = $assetManager;
        $this->translationManager = $translationManager;
        $this->localeManager = $localeManager;
    }

    /**
     * Dumps the message catalogue.
     *
     * @param MessageCatalogue $messages The message catalogue
     * @param array            $options  Options that are used by the dumper
     */
    public function dump(MessageCatalogue $messages, $options = [])
    {
        if (!isset($options['import_data']) || !$options['import_data'] instanceof Import) {
            throw new \LogicException("DatabaseDumper requires the option 'import_data' to be an instance of '".Import::class."'");
        }

        /** @var Import $importData */
        $importData = $options['import_data'];
        /** @var Locale $locale */
        $locale = $this->localeManager->findOneBy(['code' => $importData->getLocaleCode()]);
        /** @var Domain $domain */
        $domain = $this->domainManager->find($importData->getDomainId());

        if (!$domain) {
            throw new \LogicException("The domain with id '{$importData->getDomainId()}' has not been found.");
        }

        // If keep is false, we have to purge the database for the given domain first
        if (!$importData->isKeep()) {
            $this->assetManager->deleteForDomain($domain);
        }

        // Insert each Asset and its translation in the given locale one by one
        foreach ($messages->all($importData->getDomainName()) as $resname => $target) {
            /** @var Asset $asset */
            $asset = $this->assetManager->findOneBy(['resname' => $resname, 'domain' => $domain]);
            /** @var Translation $translation */
            $translation = null;

            // Build / complete Asset
            if (!$asset) {
                $asset = $this->assetManager->create();
                $asset
                    ->setDomain($domain)
                    ->setResname($resname)
                    ->setSource($target)
                ;
            } else {
                $translation = $asset->getTranslationForCode($locale->getCode());
            }

            // Build / complete Translation
            if (!$translation) {
                $translation = $this->translationManager->create();
                $translation
                    ->setLocale($locale)
                    ->setTarget($target)
                ;
                $asset->addTranslation($translation);
            } else {
                $translation->setTarget($target);
            }

            // Save Asset and Translation to database
            $this->assetManager->save($asset, false);
        }

        $this->assetManager->flush();
    }
}
