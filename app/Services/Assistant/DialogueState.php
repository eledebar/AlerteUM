<?php
namespace App\Services\Assistant;

class DialogueState
{
    public const SESSION_KEY = 'assistant.state';

    public ?int $dernierIncidentId = null;
    public ?string $derniereAction = null;
    public array $derniersMots = [];
    public string $lang = 'fr';
    public array $topics = [];
    public array $facts = [];
    public array $slots = [
        'ouvrir' => [
            'categorie' => null,
            'type' => null,
            'description' => null,
        ],
        'reouvrir' => [
            'id' => null,
            'motif' => null,
        ],
    ];

    public static function fromArray(array $data): self
    {
        $s = new self();
        $s->dernierIncidentId = isset($data['dernierIncidentId']) ? (int)$data['dernierIncidentId'] : null;
        $s->derniereAction = $data['derniereAction'] ?? null;
        $s->derniersMots = is_array($data['derniersMots'] ?? null) ? $data['derniersMots'] : [];
        $s->lang = $data['lang'] ?? 'fr';
        $s->topics = is_array($data['topics'] ?? null) ? $data['topics'] : [];
        $s->facts = is_array($data['facts'] ?? null) ? $data['facts'] : [];
        $s->slots = is_array($data['slots'] ?? null) ? $data['slots'] : $s->slots;
        return $s;
    }

    public function toArray(): array
    {
        return [
            'dernierIncidentId' => $this->dernierIncidentId,
            'derniereAction' => $this->derniereAction,
            'derniersMots' => $this->derniersMots,
            'lang' => $this->lang,
            'topics' => $this->topics,
            'facts' => $this->facts,
            'slots' => $this->slots,
        ];
    }
}
