<?php

namespace Core\Traits\Site;

trait MetaData
{
    /**
     * Add tag to array.
     *
     * @param string $name
     * @param array $content
     *
     * @return  $this
     */
    public function addMetaTag(string $name, array $content): self
    {
        $this->metaTags[] = ['name' => $name, 'content' => $content];

        return $this;
    }

    /**
     * Set metaTitle.
     *
     * @param string $metaTitle
     *
     * @return  $this
     */
    public function setMetaTitle(string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * Set metaDescription.
     *
     * @param string $metaDescription
     *
     * @return  $this
     */
    public function setMetaDescription(string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Set metaKeywords.
     *
     * @param string $metaKeywords
     *
     * @return  $this
     */
    public function setMetaKeywords(string $metaKeywords): self
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * Set metaIndex.
     *
     * @param boolean $metaIndex
     *
     * @return  $this
     */
    public function setMetaIndex(bool $metaIndex): self
    {
        $this->metaIndex = $metaIndex;

        return $this;
    }

    /**
     * Set metaFollow.
     *
     * @param boolean $metaFollow
     *
     * @return  $this
     */
    public function setMetaFollow(bool $metaFollow): self
    {
        $this->metaFollow = $metaFollow;

        return $this;
    }

    /**
     * Set metaCanonical.
     *
     * @param string $metaCanonical
     *
     * @return  $this
     */
    public function setMetaCanonical(string $metaCanonical): self
    {
        $this->metaCanonical = $metaCanonical;

        return $this;
    }

    /**
     * Set metaPrev.
     *
     * @param string $metaPrev
     *
     * @return  $this
     */
    public function setMetaPrev(string $metaPrev): self
    {
        $this->metaPrev = $metaPrev;

        return $this;
    }

    /**
     * Set metaNext.
     *
     * @param string $metaNext
     *
     * @return  $this
     */
    public function setMetaNext(string $metaNext): self
    {
        $this->metaNext = $metaNext;

        return $this;
    }

    /**
     * Set metaTags.
     *
     * @param string $metaTags
     *
     * @return  $this
     */
    public function setMetaTags(string $metaTags): self
    {
        $this->metaTags = $metaTags;

        return $this;
    }

    /**
     * Render header tags html.
     *
     * @return  string|null
     */
    public function metadata(): ?string
    {
        $tags = '';
        $tags .= $this->getMetaTagsHTML();
        $tags .= $this->getMetaTitleHTML();
        $tags .= $this->getMetaKeywordsHTML();
        $tags .= $this->getMetaDescriptionHTML();
        $tags .= $this->getMetaRobotsHTML();
        $tags .= $this->getMetaCanonicalHTML();
        $tags .= $this->getMetaPrevHTML();
        $tags .= $this->getMetaNextHTML();

        return $tags;
    }

    /**
     * Get metaTags html.
     *
     * @return  string|null
     */
    protected function getMetaTagsHTML(): ?string
    {
        $metaTags = array_merge($this->getDefaultMetaTags(), $this->metaTags ?? []);

        if (empty($metaTags)) {
            return null;
        }

        $tagsBuffer = '';

        foreach ($metaTags as $tag) {
            $tagName = $tag['name'];

            $currentTagBuffer = implode(' ', array_map(
                static function ($value, $key) {
                    return "{$key}=\"{$value}\"";
                },
                $tag['content'],
                array_keys($tag['content'])
            ));

            $tagsBuffer .= "<{$tagName} " . trim($currentTagBuffer) . ">\r\n";
        }

        return $tagsBuffer;
    }

    /**
     * Get default meta tags.
     *
     * @return  array
     */
    protected function getDefaultMetaTags(): array
    {
        return [
            [
                'name' => 'meta',
                'content' => ['http-equiv' => 'content-type', 'content' => 'text/html; charset=utf-8'],
            ],
            [
                'name' => 'meta',
                'content' => ['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0, user-scalable=0'],
            ],
            [
                'name' => 'meta',
                'content' => ['name' => 'csrf-token', 'content' => csrf_token()],
            ],
            [
                'name' => 'base',
                'content' => ['href' => url('/')],
            ],
        ];

    }

    /**
     * Get metaTitle html.
     *
     * @return  string|null
     */
    protected function getMetaTitleHTML(): ?string
    {
        if (!$this->metaTitle) {
            return null;
        }

        $title = e($this->metaTitle);

        return "<title>{$title}</title>\n";
    }

    /**
     * Get metaKeywords html.
     *
     * @return  string|null
     */
    protected function getMetaKeywordsHTML(): ?string
    {
        if (!$this->metaKeywords) {
            return null;
        }

        $keywords = e($this->metaKeywords);

        return "<meta name=\"keywords\" content=\"{$keywords}\">\n";
    }

    /**
     * Get metaDescription html.
     *
     * @return  string|null
     */
    protected function getMetaDescriptionHTML(): ?string
    {
        if (!$this->metaDescription) {
            return null;
        }

        $description = e($this->metaDescription);

        return "<meta name=\"description\" content=\"{$description}\">\n";
    }

    /**
     * Get robots html.
     *
     * @return  string
     */
    protected function getMetaRobotsHTML(): string
    {
        if ($this->metaIndex !== false) {
            $robots = $this->metaFollow ? 'all' : 'index, nofollow';
        } else {
            $robots = $this->metaFollow ? 'noindex, follow' : 'none';
        }

        return "<meta name=\"robots\" content=\"{$robots}\">\n";
    }

    /**
     * Get metaCanonical html.
     *
     * @return  string|null
     */
    protected function getMetaCanonicalHTML(): ?string
    {
        if (!$this->metaCanonical) {
            return null;
        }

        $host = request()->getSchemeAndHttpHost();
        $canonical = trim($this->metaCanonical, '/');
        return "<link rel=\"canonical\" href=\"{$host}/{$canonical}\">";
    }

    /**
     * Get metaPrev html.
     *
     * @return  string|null
     */
    protected function getMetaPrevHTML(): ?string
    {
        if (!$this->metaPrev) {
            return null;
        }

        return "<link rel=\"prev\" href=\"{$this->metaPrev}\">";
    }

    /**
     * Get metaNext html.
     *
     * @return  string|null
     */
    protected function getMetaNextHTML(): ?string
    {
        if (!$this->metaNext) {
            return null;
        }

        return "<link rel=\"next\" href=\"{$this->metaNext}\">";
    }

    /**
     * Get site title.
     *
     * @return  string|null
     */
    public function getTitle(): ?string
    {
        return $this->metaTitle;
    }
}