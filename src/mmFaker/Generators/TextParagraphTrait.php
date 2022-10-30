<?php

namespace mmFaker\Generators;

use mmFaker\Faker;

trait TextParagraphTrait
{
    public function addTextParagraph($fieldName, $generationMode = Faker::RANDOM_VALUE, $minLengthOrFix = null, $maxLength = null)
    {
        if ($this->totTextParagraph == 0) {
            $this->textParagraph = explode("\n", file_get_contents($this->lists[Faker::LISTTYPE_PARAGRAPHS]));
            $this->totTextParagraph = count($this->textParagraph) - 1;
            echo "Loaded {$this->totTextParagraph} text paragraph.\n";
        }
        $this->columns[$fieldName] = array(
            "type"      => Faker::TYPE_TEXT,
            "random"    => $generationMode == Faker::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == Faker::RANDOM_VALUE ? null : $minLengthOrFix,
            "minLength" => $generationMode == Faker::RANDOM_VALUE ? $minLengthOrFix : null,
            "maxLength" => $generationMode == Faker::RANDOM_VALUE ? $maxLength : null,
        );
        return $this;
    }

    protected function generateTextParagraph($minLen = null, $maxLen = null)
    {
        $text = "";
        $done = false;
        while (!$done) {
            $text .= $this->textParagraph[rand(0, $this->totTextParagraph - 1)];
            $textLen = strlen($text);
            if ((isset($minLen) && ($textLen >= $minLen)) && isset($maxLen)) {
                if ($textLen >= $maxLen) {
                    $text = substr($text, 0, strpos($text, ' ', $maxLen));
                    $done = true;
                }
            } elseif (isset($minLen) && ($textLen >= $minLen)) {
                $done = true;
            } elseif (isset($maxLen) && ($textLen <= $maxLen)) {
                $text = substr($text, 0, strpos($text, ' ', $maxLen));
                $done = true;
            } else {
                $done = true;
            }
        }
        return $text;
    }
}
