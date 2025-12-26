<?php

namespace TSRP\Tether\BbCode;

use XF\BbCode\Renderer\AbstractRenderer;

class Tether
{
    private const IMAGE_BASE_PATH = '/db/';
    private const JSON_BASE_PATH = '/db/tethers/';

    public static function render(array $tag, array $rendererStates, AbstractRenderer $renderer): string
    {
        $tetherId = isset($tag['option']) ? trim((string)$tag['option']) : '';
        if ($tetherId === '')
        {
            return '';
        }

        $content = trim((string)$renderer->renderSubTree($tag['children'], $rendererStates));
        $selectedLevels = self::parseSelectedLevels($content);

        $title = $tetherId;
        $imageUrl = self::IMAGE_BASE_PATH . rawurlencode($tetherId) . '.webp';
        $jsonUrl = self::JSON_BASE_PATH . rawurlencode($tetherId) . '.json';

        $renderer->addCss('tsrp_tether');
        $renderer->addJs('tsrp/tether.js');

        $templater = $renderer->getTemplater();

        static $includeTemplate = true;
        $html = $templater->renderTemplate('tsrp_tether_tag', [
            'tetherId' => $tetherId,
            'title' => $title,
            'imageUrl' => $imageUrl,
            'jsonUrl' => $jsonUrl,
            'selectedPositive' => implode(',', array_keys($selectedLevels['positive'])),
            'selectedNegative' => implode(',', array_keys($selectedLevels['negative'])),
            'includeTemplate' => $includeTemplate,
        ]);
        $includeTemplate = false;

        return $html;
    }

    private static function parseSelectedLevels(string $content): array
    {
        $selected = [
            'positive' => [],
            'negative' => [],
        ];

        if ($content === '')
        {
            return $selected;
        }

        $parts = preg_split('/\s*,\s*/', $content, -1, PREG_SPLIT_NO_EMPTY);
        if (!$parts)
        {
            return $selected;
        }

        foreach ($parts as $part)
        {
            if (preg_match('/^(positive|negative)\s*(\d+)$/i', $part, $matches))
            {
                $type = strtolower($matches[1]);
                $level = (int)$matches[2];
                $selected[$type][$level] = true;
            }
        }

        return $selected;
    }
}
