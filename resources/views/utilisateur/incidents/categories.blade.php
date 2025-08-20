<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            Signaler un incident
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
            $categories = [
                [
                    'key' => 'communication',
                    'title' => 'Communication et collaboration',
                    'desc' => 'Emails, outils partagés, messagerie institutionnelle.',
                    'types' => ['Messagerie', 'Outils collaboratifs'],
                    'color' => 'bg-blue-600'
                ],
                [
                    'key' => 'acces',
                    'title' => 'Identification et accès',
                    'desc' => 'Accès Internet, ordinateurs, connexions réseau.',
                    'types' => ['Compte utilisateur', 'Connexion réseau'],
                    'color' => 'bg-yellow-500'
                ],
                [
                    'key' => 'plateformes',
                    'title' => 'Plateformes web et services numériques',
                    'desc' => 'Sites web, formulaires, services en ligne.',
                    'types' => ['Sites web', 'Formulaires en ligne'],
                    'color' => 'bg-red-600'
                ],
                [
                    'key' => 'equipements',
                    'title' => 'Équipements et logiciels',
                    'desc' => 'Pannes matérielles, logiciels à installer.',
                    'types' => ['Matériel défectueux', 'Installation logiciel', 'Périphériques'],
                    'color' => 'bg-green-600'
                ],
                [
                    'key' => 'enseignement',
                    'title' => 'Enseignement et recherche',
                    'desc' => 'Laboratoires, ateliers, outils de recherche.',
                    'types' => ['Salles de TP', 'Logiciels de recherche'],
                    'color' => 'bg-orange-500'
                ],
                [
                    'key' => 'assistance',
                    'title' => 'Assistance générale et demandes',
                    'desc' => 'Aide numérique, orientation, demandes diverses.',
                    'types' => ['Orientation', 'Questions générales', 'Support technique'],
                    'color' => 'bg-purple-600'
                ]
            ];
            $fondsClairs = ['acces','enseignement'];
        @endphp

        @foreach ($categories as $cat)
            @php
                $clair = in_array($cat['key'], $fondsClairs, true);
                $text = $clair ? 'text-gray-900' : 'text-white';
                $muted = $clair ? 'text-gray-800/90' : 'text-white/90';
                $chip = $clair ? 'bg-black/10 text-gray-900 ring-1 ring-black/10' : 'bg-white/20 text-white';
                $focusRing = $clair ? 'focus-visible:ring-black/30' : 'focus-visible:ring-white/60';
                $focusOffset = $clair ? 'focus-visible:ring-offset-white' : 'focus-visible:ring-offset-black';
                $titleId = 'title-'.$cat['key'];
                $descId = 'desc-'.$cat['key'];
            @endphp

            <a href="{{ route('utilisateur.incidents.create', ['categorie' => $cat['key']]) }}"
               class="rounded-xl shadow-lg transition hover:scale-[1.02] hover:shadow-xl p-6 {{ $cat['color'] }} {{ $text }} flex flex-col space-y-3 focus:outline-none focus-visible:ring-4 focus-visible:ring-offset-2 {{ $focusRing }} {{ $focusOffset }}"
               aria-labelledby="{{ $titleId }}" aria-describedby="{{ $descId }}">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset($cat['key'] . '.webp') }}" alt="" class="w-10 h-10 object-contain">
                    <h3 id="{{ $titleId }}" class="text-lg font-bold">{{ $cat['title'] }}</h3>
                </div>
                <p id="{{ $descId }}" class="text-sm {{ $muted }}">{{ $cat['desc'] }}</p>
                <div class="space-y-1 mt-1">
                    @foreach ($cat['types'] as $type)
                        <span class="text-xs px-2 py-0.5 rounded inline-block {{ $chip }}">
                            {{ $type }}
                        </span>
                    @endforeach
                </div>
            </a>
        @endforeach
    </div>
</x-app-layout>
