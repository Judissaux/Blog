<?php

namespace App\Model;

use DateTimeInterface;
//création de l'interface permettant de rendant oblgatoire l'implémentation des propriété createdAt et UpdatedAt
interface TimestampedInterface
{
    public function getCreatedAt(): ?\DateTimeInterface;

    public function setCreatedAt(\DateTimeInterface $createdAt);

    public function getUpdatedAt(): ?\DateTimeInterface;

    public function setUpdatedAt(?\DateTimeInterface $updatedAt);

}