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
 * Time: 18:34
 */

namespace Nvision\SpanishCowAdapter\Model;


class Translation implements \JsonSerializable
{
    /**
     * @var string
     */
    protected $target;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $resname;

    /**
     * @var string
     */
    protected $locale;

    public function __construct($source = null)
    {
        if ($source && is_array($source)) {
            $this->target = $source['target'];
        }
    }

    /**
     * Specify data which should be serialized to JSON
     */
    public function jsonSerialize()
    {
        return ['target' => $this->target];
    }

    /**
     * @return string
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * @param string $target
     * @return Translation
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     * @return Translation
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return string
     */
    public function getResname(): string
    {
        return $this->resname;
    }

    /**
     * @param string $resname
     * @return Translation
     */
    public function setResname($resname)
    {
        $this->resname = $resname;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return Translation
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }
}