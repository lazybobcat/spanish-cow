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
    protected $sourceFilePath;

    /**
     * @var string
     */
    protected $targetFilePath;

    /**
     * @var string
     */
    protected $sourceType = FileType::FILE_TYPE_XLIFF;

    /**
     * @var string
     */
    protected $targetType = FileType::FILE_TYPE_DATABASE;

    /**
     * Keep existing translations that are not in the newly imported file
     *
     * @var bool
     */
    protected $keep = true;

    public static function getFileTypes()
    {
        return [
            FileType::FILE_TYPE_XLIFF => FileType::FILE_TYPE_XLIFF,
            FileType::FILE_TYPE_YAML => FileType::FILE_TYPE_YAML,
            FileType::FILE_TYPE_CSV => FileType::FILE_TYPE_CSV,
        ];
    }

    public function jsonSerialize()
    {
        return [
            'domain_id' => $this->getDomainId(),
            'domain_name' => $this->getDomainName(),
            'locale_code' => $this->getLocaleCode(),
            'source_path' => $this->getSourceFilePath(),
            'source_type' => $this->getSourceType(),
            'target_path' => $this->getTargetFilePath(),
            'target_type' => $this->getTargetType(),
            'keep' => $this->isKeep(),
        ];
    }

    public function __construct($from = null)
    {
        if ($from && is_array($from)) {
            $this->setDomainId($from['domain_id']);
            $this->setDomainName($from['domain_name']);
            $this->setLocaleCode($from['locale_code']);
            $this->setSourceFilePath($from['source_path']);
            $this->setSourceType($from['source_type']);
            $this->setTargetFilePath($from['target_path']);
            $this->setTargetType($from['target_type']);
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
        $this->sourceFilePath = $file->getRealPath();

        return $this;
    }

    /**
     * @return string
     */
    public function getSourceFilePath(): ?string
    {
        return $this->sourceFilePath;
    }

    /**
     * @param string $sourceFilePath
     * @return Import
     */
    public function setSourceFilePath($sourceFilePath)
    {
        $this->sourceFilePath = $sourceFilePath;

        return $this;
    }

    /**
     * @return string
     */
    public function getTargetFilePath(): ?string
    {
        return $this->targetFilePath;
    }

    /**
     * @param string $targetFilePath
     * @return Import
     */
    public function setTargetFilePath($targetFilePath)
    {
        $this->targetFilePath = $targetFilePath;

        return $this;
    }

    /**
     * @return string
     */
    public function getSourceType(): ?string
    {
        return $this->sourceType;
    }

    /**
     * @param string $sourceType
     * @return Import
     */
    public function setSourceType($sourceType)
    {
        $this->sourceType = $sourceType;

        return $this;
    }

    /**
     * @return string
     */
    public function getTargetType(): string
    {
        return $this->targetType;
    }

    /**
     * @param string $targetType
     * @return Import
     */
    public function setTargetType($targetType)
    {
        $this->targetType = $targetType;

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