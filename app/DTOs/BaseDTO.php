<?php

namespace App\DTOs;

class BaseDTO
{
    public function toArray(){
        return (array)$this;
    }

}
