<?php

namespace mmFaker\Generators;

use mmFaker\Faker;

trait PersonTrait
{

    public function addPersonName($fieldName, $generationMode = Faker::RANDOM_VALUE, $fixContent = null)
    {
        if ($this->totPersonNames == 0) {
            $this->personNames = explode("\n", file_get_contents($this->lists[Faker::LISTTYPE_PERSONNAMES]));
            $this->totPersonNames = count($this->personNames) - 1;
        }
        $this->columns[$fieldName] = array(
            "type"      => Faker::TYPE_PERSONNAME,
            "random"    => $generationMode == Faker::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == Faker::RANDOM_VALUE ? null : $fixContent,
            "minLength" => $generationMode == null,
            "maxLength" => $generationMode == null,
        );
        return $this;
    }

    public function addPersonSurname($fieldName, $generationMode = Faker::RANDOM_VALUE, $fixContent = null)
    {
        if ($this->totPersonSurnames == 0) {
            $this->personSurnames = explode("\n", file_get_contents($this->lists[Faker::LISTTYPE_PERSONSURNAMES]));
            $this->totPersonSurnames = count($this->personSurnames) - 1;
        }
        $this->columns[$fieldName] = array(
            "type"      => Faker::TYPE_PERSONSURNAME,
            "random"    => $generationMode == Faker::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == Faker::RANDOM_VALUE ? null : $fixContent,
            "minLength" => $generationMode == null,
            "maxLength" => $generationMode == null,
        );
        return $this;
    }

    public function addFullPersonName($fieldName, $generationMode = Faker::RANDOM_VALUE, $fixContent = null)
    {
        if ($this->totPersonNames == 0) {
            $this->personNames = explode("\n", file_get_contents($this->lists[Faker::LISTTYPE_PERSONNAMES]));
            $this->totPersonNames = count($this->personNames) - 1;
        }
        if ($this->totPersonSurnames == 0) {
            $this->personSurnames = explode("\n", file_get_contents($this->lists[Faker::LISTTYPE_PERSONSURNAMES]));
            $this->totPersonSurnames = count($this->personSurnames) - 1;
        }
        $this->columns[$fieldName] = array(
            "type"      => Faker::TYPE_FULLPERSONNAME,
            "random"    => $generationMode == Faker::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == Faker::RANDOM_VALUE ? null : $fixContent,
            "minLength" => $generationMode == null,
            "maxLength" => $generationMode == null,
        );
        return $this;
    }

    protected function generateFullPersonName(string $fixValue = null)
    {
        if (isset($fixValue)) {
            return $fixValue;
        } else {
            return $this->personNames[rand(0, $this->totPersonNames)] . ' ' . $this->personSurnames[rand(0, $this->totPersonSurnames)];
        }
    }

    protected function generatePersonName(string $fixValue = null)
    {
        if (isset($fixValue)) {
            return $fixValue;
        } else {
            return $this->personNames[rand(0, $this->totPersonNames)];
        }
    }

    protected function generatePersonSurname(string $fixValue = null)
    {
        if (isset($fixValue)) {
            return $fixValue;
        } else {
            return $this->personSurnames[rand(0, $this->totPersonSurnames)];
        }
    }
}
