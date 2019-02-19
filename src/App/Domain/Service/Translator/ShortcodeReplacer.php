<?php

namespace App\Domain\Service\Translator;

class ShortcodeReplacer
{
    /**
     * @param array  $parameters
     * @param string $replacement
     * @param array  $options
     *
     * @return string
     */
    public function do(array $parameters, string $replacement, array $options = []): string
    {
        foreach ($parameters as $key => $value) {
            $replacement = str_replace($key, $value, $replacement);
        }
        return $replacement;
    }
}