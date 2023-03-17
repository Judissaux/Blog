<?php

namespace App\Service;

use App\Repository\OptionRepository;

class OptionService
{
    public function __construct(private OptionRepository $optionRepo){}

    public function findAll()
    {
        return $this->optionRepo->findAllForTwig();
    }

}