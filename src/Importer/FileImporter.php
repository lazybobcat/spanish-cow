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
use App\Model\Import;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Translation\Dumper\DumperInterface;
use Symfony\Component\Translation\Loader\CsvFileLoader;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Writer\TranslationWriter;

class FileImporter
{
    const INPUT_TYPE_XLIFF = Import::FILE_TYPE_XLIFF;
    const INPUT_TYPE_CSV = Import::FILE_TYPE_CSV;
    const INPUT_TYPE_YAML = Import::FILE_TYPE_YAML;

    const OUTPUT_TYPE_DATABASE = 'database';

    /**
     * @var TranslationWriter
     */
    private $translationWriter;
    /**
     * @var DatabaseDumper
     */
    private $databaseDumper;

    public function __construct(TranslationWriter $translationWriter, DatabaseDumper $databaseDumper)
    {
        $this->translationWriter = $translationWriter;
        $this->databaseDumper = $databaseDumper;
    }

    public function import(Import $importData)
    {
        $file = new File($importData->getFilePath());
        $this->translationWriter->addDumper(self::OUTPUT_TYPE_DATABASE, $this->getDumper(self::OUTPUT_TYPE_DATABASE));
        $loader = $this->getLoader($importData->getFileType());

        $catalogue = $loader->load($file->getRealPath(), $importData->getLocaleCode(), $importData->getDomainName());
        $this->translationWriter->write($catalogue, self::OUTPUT_TYPE_DATABASE, [
            'import_data' => $importData,
        ]);
    }

    /**
     * @param string $type
     *
     * @return DumperInterface
     */
    private function getDumper($type)
    {
        switch ($type) {
            case self::OUTPUT_TYPE_DATABASE:
                return $this->databaseDumper;

            default:
                throw new \LogicException("No dumper configured for type '{$type}'");
        }
    }

    private function getLoader($type)
    {
        switch ($type) {
            case self::INPUT_TYPE_XLIFF:
                return new XliffFileLoader();

            case self::INPUT_TYPE_CSV:
                return new CsvFileLoader();

            case self::INPUT_TYPE_YAML:
                return new YamlFileLoader();

            default:
                throw new \LogicException("No loader configured for type '{$type}'");
        }
    }
}
