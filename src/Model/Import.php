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

namespace App\Model;

use Symfony\Component\HttpFoundation\File\File;

class Import implements \JsonSerializable
{
    const FILE_TYPE_XLIFF = 'xliff';
    const FILE_TYPE_CSV = 'csv';
    const FILE_TYPE_YAML = 'yaml';

    /**
     * @var int
     */
    protected $domainId;

    /**
     * @var string
     */
    protected $domainName;

    /**
     * @var string
     */
    protected $localeCode;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var string
     */
    protected $fileType = self::FILE_TYPE_XLIFF;

    /**
     * Keep existing translations that are not in the newly imported file
     *
     * @var bool
     */
    protected $keep = true;

    public static function getFileTypes()
    {
        return [
            self::FILE_TYPE_XLIFF => self::FILE_TYPE_XLIFF,
            self::FILE_TYPE_YAML => self::FILE_TYPE_YAML,
            self::FILE_TYPE_CSV => self::FILE_TYPE_CSV,
        ];
    }

    public function jsonSerialize()
    {
        return [
            'domain_id' => $this->getDomainId(),
            'domain_name' => $this->getDomainName(),
            'locale_code' => $this->getLocaleCode(),
            'file_path' => $this->getFilePath(),
            'file_type' => $this->getFileType(),
            'keep' => $this->isKeep(),
        ];
    }

    public function __construct($from = null)
    {
        if ($from && is_array($from)) {
            $this->setDomainId($from['domain_id']);
            $this->setDomainName($from['domain_name']);
            $this->setLocaleCode($from['locale_code']);
            $this->setFilePath($from['file_path']);
            $this->setFileType($from['file_type']);
            $this->setKeep($from['keep']);
        }
    }

    /**
     * @return int
     */
    public function getDomainId(): ?int
    {
        return $this->domainId;
    }

    /**
     * @param int $domainId
     * @return Import
     */
    public function setDomainId($domainId)
    {
        $this->domainId = $domainId;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomainName(): ?string
    {
        return $this->domainName;
    }

    /**
     * @param string $domainName
     * @return Import
     */
    public function setDomainName($domainName)
    {
        $this->domainName = $domainName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }

    /**
     * @param string $localeCode
     * @return Import
     */
    public function setLocaleCode($localeCode)
    {
        $this->localeCode = $localeCode;

        return $this;
    }

    /**
     * @return File
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param File $file
     * @return Import
     */
    public function setFile($file)
    {
        $this->file = $file;
        $this->filePath = $file->getRealPath();

        return $this;
    }

    /**
     * @return string
     */
    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     * @return Import
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    /**
     * @param string $fileType
     * @return Import
     */
    public function setFileType($fileType)
    {
        $this->fileType = $fileType;

        return $this;
    }

    /**
     * @return bool
     */
    public function isKeep(): bool
    {
        return $this->keep;
    }

    /**
     * @param bool $keep
     * @return Import
     */
    public function setKeep($keep)
    {
        $this->keep = $keep;

        return $this;
    }
}