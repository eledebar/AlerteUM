@props(['disabled' => false])

@php
$id = $attributes->get('id') ?? 'champ-'.\Illuminate\Support\Str::uuid()->toString();
$nom = $attributes->get('name');
$estInvalide = $nom ? $errors->has($nom) : false;
$decritPar = collect([$attributes->get('aria-describedby')])->filter()->implode(' ');
$decritPar = trim($decritPar.' '.($estInvalide ? ($id.'-erreur') : ''));
$libelleAria = $attributes->get('aria-label') ?? $attributes->get('libelle') ?? ($attributes->has('aria-labelledby') ? null : ($nom ? ucfirst(str_replace(['_', '-'], ' ', $nom)) : 'Champ de formulaire'));
$requis = $attributes->has('required');
$attrsA11y = [];
if ($libelleAria) { $attrsA11y['aria-label'] = $libelleAria; }
if ($requis) { $attrsA11y['aria-required'] = 'true'; }
if ($estInvalide) { $attrsA11y['aria-invalid'] = 'true'; }
if ($decritPar !== '') { $attrsA11y['aria-describedby'] = $decritPar; }
@endphp

<input
    @disabled($disabled)
    {{ $attributes->merge(array_merge([
        'id' => $id,
        'class' => 'block w-full px-3 py-2 rounded-md shadow-sm border border-gray-500 dark:border-gray-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-600 dark:placeholder-gray-400 focus-visible:outline-none focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-400 disabled:opacity-60 disabled:cursor-not-allowed',
    ], $attrsA11y)) }}
>

@if($estInvalide)
<p id="{{ $id }}-erreur" class="sr-only">{{ $errors->first($nom) }}</p>
@endif
