@php
    $auteur = $comment->user
        ?? $comment->author
        ?? $comment->auteur
        ?? $comment->utilisateur
        ?? (isset($comment->user_id) ? \App\Models\User::find($comment->user_id) : null);

    $texte = $comment->content
        ?? $comment->message
        ?? $comment->texte
        ?? $comment->comment
        ?? $comment->body
        ?? '';

    $cid = 'cmt-'.($comment->id ?? uniqid());
    $nom = trim($auteur->name ?? __('Utilisateur'));
    $iso = $comment->created_at?->toIso8601String();
@endphp

<article class="flex gap-3" role="article" aria-labelledby="{{ $cid }}-hdr" aria-describedby="{{ $cid }}-time">
    <div aria-hidden="true">
        <x-user-avatar :utilisateur="$auteur" taille="36" />
    </div>
    <div class="grow">
        <header id="{{ $cid }}-hdr" class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $nom }}</span>
            <span aria-hidden="true">·</span>
            <time id="{{ $cid }}-time" datetime="{{ $iso }}">{{ $comment->created_at?->format('d/m/Y H:i') }}</time>
        </header>
        <div class="mt-1 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-3">
            <p class="text-gray-800 dark:text-gray-200 whitespace-pre-line" dir="auto">{{ $texte }}</p>
        </div>
    </div>
</article>
