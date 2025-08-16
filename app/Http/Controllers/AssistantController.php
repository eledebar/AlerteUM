<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Assistant\Nlu;
use App\Services\Assistant\IntentRouter;
use App\Services\Assistant\DialogueState;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AssistantController extends Controller
{
    public function index()
    {
        return view('assistant.index');
    }

    public function message(Request $request)
    {
        $request->validate([
            'message' => 'required|string|min:1',
        ]);

        $user = Auth::user();
        $input = trim((string)$request->input('message'));

        $stateArr = $request->session()->get(DialogueState::SESSION_KEY, []);
        $state = DialogueState::fromArray($stateArr);

        try {
            $nlu = new Nlu();
            $router = new IntentRouter();

            $intent = $nlu->detecter($input, $user, $state);
            $result = $router->traiter($intent, $user, $state);

            if (!is_array($result) || (!($result['texte'] ?? null) && !($result['actions'] ?? null))) {
                $result = $this->fallbackConversational();
            }

            $request->session()->put(DialogueState::SESSION_KEY, $state->toArray());

            return response()->json([
                'ok'          => true,
                'answer'      => $result['texte'] ?? '',
                'actions'     => $result['actions'] ?? [],
                'suggestions' => $result['suggestions'] ?? $this->suggestionsParDefaut(),
                'cards'       => $result['cards'] ?? [],
                'state'       => $state->toArray(),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok'          => false,
                'answer'      => "Je vous ai lu. Indiquez, si possible, le lieu, l’impact (bloquant/lent) et s’il existe un **numéro d’incident**.",
                'actions'     => $this->actionsParDefaut(),
                'suggestions' => $this->suggestionsParDefaut(),
            ], 200);
        }
    }

    protected function fallbackConversational(): array
    {
        $txt = "Merci. Pour affiner, précisez le contexte (lieu/service), l’impact (bloquant/lent) et s’il existe un **numéro d’incident**.";
        return [
            'texte' => $txt,
            'actions' => $this->actionsParDefaut(),
            'suggestions' => $this->suggestionsParDefaut(),
        ];
    }

    protected function actionsParDefaut(): array
    {
        return [
            ['label' => 'Mes incidents', 'command' => '/liste ouverts'],
            ['label' => 'Rechercher « wifi »', 'command' => '/rechercher wifi'],
            ['label' => 'Exporter CSV', 'command' => '/csv'],
            ['label' => 'Notifications', 'command' => '/notifs'],
        ];
    }

    protected function suggestionsParDefaut(): array
    {
        return ['Mes incidents ouverts','Rechercher « wifi »','Exporter CSV','Notifications','Créer un incident','Vérifier un incident'];
    }
}
