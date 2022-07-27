<?php

namespace App\Service;

abstract class AbstractFactory {

    public function getUser() {
        return $this->security->getUser();
    }
    
}