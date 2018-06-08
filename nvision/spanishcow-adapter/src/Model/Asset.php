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
 * Time: 17:51
 */

namespace Nvision\SpanishCowAdapter\Model;


class Asset implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $resname;

    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $notes;

    /**
     * @var string
     */
    protected $domain;

    public function __construct($source = null)
    {
        if ($source && is_array($source)) {
            $this->resname = $source['resname'];
            $this->source = $source['source'];
            $this->notes = $source['notes'];
        }
    }

    /**
     * Specify data which should be serialized to JSON
     */
    public function jsonSerialize()
    {
        return [
            'resname' => $this->resname,
            'source' => $this->source,
            'notes' => $this->notes,
        ];
    }

    /**
     * @return string
     */
    public function getResname(): ?string
    {
        return $this->resname;
    }

    /**
     * @param string $resname
     * @return Asset
     */
    public function setResname($resname)
    {
        $this->resname = $resname;

        return $this;
    }

    /**
     * @return string
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @return Asset
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return string
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     * @return Asset
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     * @return Asset
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }
}