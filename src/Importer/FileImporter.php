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
 * Time: 17:20
 */

namespace App\Importer;

use App\Importer\Dumper\DatabaseDumper;
use App\Importer\Loader\DatabaseLoader;
use App\Model\FileType;
use App\Model\Import;
use Symfony\Component\Translation\Dumper\CsvFileDumper;
use Symfony\Component\Translation\Dumper\DumperInterface;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\Dumper\YamlFileDumper;
use Symfony\Component\Translation\Loader\CsvFileLoader;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Writer\TranslationWriter;

class FileImporter
{
    /**
     * @var TranslationWriter
     */
    private $translationWriter;

    /**
     * @var DatabaseDumper
     */
    private $databaseDumper;

    /**
     * @var DatabaseLoader
     */
    private $databaseLoader;

    public function __construct(TranslationWriter $translationWriter, DatabaseDumper $databaseDumper, DatabaseLoader $databaseLoader)
    {
        $this->translationWriter = $translationWriter;
        $this->databaseDumper = $databaseDumper;
        $this->databaseLoader = $databaseLoader;
    }

    public function import(Import $importData)
    {
        $this->translationWriter->addDumper($importData->getTargetType(), $this->getDumper($importData->getTargetType()));
        $loader = $this->getLoader($importData->getSourceType());

        $catalogue = $loader->load($this->getResource($importData, $importData->getSourceType()), $importData->getLocaleCode(), $importData->getDomainName());
        $this->translationWriter->write($catalogue, $importData->getTargetType(), $this->getOptions($importData, $importData->getTargetType()));
    }

    private function getOptions(Import $data, $targetType)
    {
        $options = [
            'import_data' => $data,
        ];

        switch ($targetType) {
            case FileType::FILE_TYPE_DATABASE:
                break;

            case FileType::FILE_TYPE_XLIFF:
            case FileType::FILE_TYPE_CSV:
            case FileType::FILE_TYPE_YAML:
            default:
                $options['path'] = $data->getTargetFilePath();
                break;
        }

        return $options;
    }

    private function getResource(Import $data, $type)
    {
        switch ($type) {
            case FileType::FILE_TYPE_DATABASE:
                return $data->getDomainId();

            case FileType::FILE_TYPE_XLIFF:
            case FileType::FILE_TYPE_CSV:
            case FileType::FILE_TYPE_YAML:
            default:
                return $data->getSourceFilePath();
        }
    }

    /**
     * @param string $type
     *
     * @return DumperInterface
     */
    private function getDumper($type)
    {
        switch ($type) {
            case FileType::FILE_TYPE_DATABASE:
                return $this->databaseDumper;

            case FileType::FILE_TYPE_XLIFF:
                return new XliffFileDumper();

            case FileType::FILE_TYPE_CSV:
                return new CsvFileDumper();

            case FileType::FILE_TYPE_YAML:
                return new YamlFileDumper();

            default:
                throw new \LogicException("No dumper configured for type '{$type}'");
        }
    }

    private function getLoader($type)
    {
        switch ($type) {
            case FileType::FILE_TYPE_DATABASE:
                return $this->databaseLoader;

            case FileType::FILE_TYPE_XLIFF:
                return new XliffFileLoader();

            case FileType::FILE_TYPE_CSV:
                return new CsvFileLoader();

            case FileType::FILE_TYPE_YAML:
                return new YamlFileLoader();

            default:
                throw new \LogicException("No loader configured for type '{$type}'");
        }
    }
}
