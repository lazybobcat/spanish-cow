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
 * Date: 01/06/18
 * Time: 16:52
 */

namespace App\Consumer;

use App\Importer\FileImporter;
use App\Model\Import;
use Enqueue\Client\TopicSubscriberInterface;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileImportConsumer implements PsrProcessor, TopicSubscriberInterface
{
    /**
     * @var FileImporter
     */
    private $fileImporter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(FileImporter $fileImporter, LoggerInterface $logger)
    {
        $this->fileImporter = $fileImporter;
        $this->logger = $logger;
    }

    /**
     * The method has to return either self::ACK, self::REJECT, self::REQUEUE string.
     *
     * The method also can return an object.
     * It must implement __toString method and the method must return one of the constants from above.
     *
     * @param PsrMessage $message
     * @param PsrContext $context
     *
     * @return string|object with __toString method implemented
     */
    public function process(PsrMessage $message, PsrContext $context)
    {
        $data = json_decode($message->getBody(), true);
        $import = new Import($data);
        $fs = new Filesystem();

        try {
            $this->fileImporter->import($import);
            $fs->remove($import->getFilePath());

            return self::ACK;
        } catch (\Exception $e) {
            $this->logger->error('Error during import of translation file', [
                'data' => $data,
                'exception' => $e,
            ]);

            return self::REJECT;
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedTopics()
    {
        return [Topics::TOPIC_FILE_IMPORT];
    }
}
