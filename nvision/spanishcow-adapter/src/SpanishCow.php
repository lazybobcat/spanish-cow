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
 * Date: 08/06/18
 * Time: 12:12
 */

namespace Nvision\SpanishCowAdapter;

use Nvision\SpanishCowAdapter\Model\Asset;
use Nvision\SpanishCowAdapter\Model\Translation;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Translation\Common\Model\Message;
use Translation\Common\Model\MessageInterface;
use Translation\Common\Storage;
use Translation\Common\TransferableStorage;

class SpanishCow implements Storage, TransferableStorage
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get a translation. If no translation is found, null MUST be returned.
     *
     * @param string $locale
     * @param string $domain
     * @param string $key
     *
     * @return MessageInterface
     */
    public function get($locale, $domain, $key)
    {
        try {
            $translation = $this->client->getTranslation($locale, $domain, $key);
        } catch (\Exception $e) {
            return null;
        }

        if (empty($translation)) {
            return null;
        }

        return new Message($key, $domain, $locale, $translation['target']);
    }

    /**
     * Create a new translation or asset. If a translation already exist this function
     * will do nothing.
     *
     * @param MessageInterface $message
     */
    public function create(MessageInterface $message)
    {
        $asset = new Asset();
        $asset
            ->setResname($message->getKey())
            ->setSource($message->getKey())
            ->setDomain($message->getDomain())
        ;

        try {
            $this->client->postAsset($asset);
        } catch (\Exception $e) {
        }

        $target = $message->getTranslation();

        // translation is the same as the key, so we will set it to empty string
        // as it was not translated and stats on Spanish-Cow will be unaffected
        if ($message->getKey() === $message->getTranslation()) {
            $target = '';
        }

        $translation = new Translation();
        $translation
            ->setTarget($target)
            ->setLocale($message->getLocale())
            ->setDomain($message->getDomain())
            ->setResname($message->getKey())
        ;

        try {
            $this->client->postTranslation($translation);
        } catch (\Exception $e) {
        }
    }

    /**
     * Update a translation. Creates a translation if there is none to update.
     *
     * @param MessageInterface $message
     */
    public function update(MessageInterface $message)
    {
        // TODO: Implement update() method.
    }

    /**
     * Remove a translation from the storage. If the storage implementation makes
     * a difference between translations and assets then this function MUST only
     * remove the translation.
     *
     * @param string $locale
     * @param string $domain
     * @param string $key
     */
    public function delete($locale, $domain, $key)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Get messages from the storage into the $catalogue.
     *
     * @param MessageCatalogueInterface $catalogue
     */
    public function export(MessageCatalogueInterface $catalogue)
    {
        // TODO: Implement export() method.
    }

    /**
     * Populate the storage with all the messages in $catalogue. This action
     * should be considered as a "force merge". Existing messages in the storage
     * will be overwritten but no message will be removed.
     *
     * @param MessageCatalogueInterface $catalogue
     */
    public function import(MessageCatalogueInterface $catalogue)
    {
        // TODO: Implement import() method.
    }
}
