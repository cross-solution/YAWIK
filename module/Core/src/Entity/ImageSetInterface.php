<?php

declare(strict_types=1);

namespace Core\Entity;

use Doctrine\Common\Collections\Collection;

interface ImageSetInterface
{
    public const ORIGINAL  = 'original';
    public const THUMBNAIL = 'thumbnail';

    public function getId(): ?string;

    public function clear(): self;

    public function setImages(Collection $images): self;

    public function getImages(): Collection;

    public function add(ImageInterface $image): self;

    public function get(string $key, bool $fallback=true): ?ImageInterface;
}