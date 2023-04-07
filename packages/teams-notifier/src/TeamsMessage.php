<?php

namespace Petshop\TeamsNotifier;

use JsonSerializable;

class TeamsMessage extends JsonSerializable
{
    protected array $data;

    /**
     * @param string $content
     * @param string $theme
     */
    public function __construct(string $content, string $theme = 'primary')
    {
        $this->data['@type'] = 'MessageCard';
        $this->data['@context'] = 'https://schema.org/extensions';
        $this->data['summary'] = 'Incoming Notification';
        $this->data['themeColor'] = $this->getThemeColour($theme);

        $this->setMessageContent($content);
    }

    public function setMessageType(string $type): TeamsMessage
    {
        $this->data['@type'] = $type;

        return $this;
    }

    public function setMessageSummary(string $summary): TeamsMessage
    {
        $this->data['summary'] = $summary;

        return $this;
    }

    protected function setMessageContent(mixed $content, int|null $section = null): TeamsMessage
    {
        if ($section !== null) {
            $this->data['sections'][$section] = $content;
        } else {
            $this->data['text'] = $content;
        }

        return $this;
    }

    protected function getThemeColour(string $theme): string
    {
        return match($theme) {
            'primary' => '6200EE',
            'secondary' => '03DAc6',
            'accent' => '018766',
            'error' => 'B00020',
            'info' => '00ACC1',
            'success' => '388E3C',
            'warning' => 'EF6C00',
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->data;
    }
}
