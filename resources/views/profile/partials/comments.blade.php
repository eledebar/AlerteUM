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
@endphp

<div class="flex gap-3">
    <x-user-avatar :utilisateur="$auteur" taille="36" />
    <div class="grow">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $auteur?->name }}</span>
            <span>·</span>
            <span>{{ $comment->created_at?->format('d/m/Y H:i') }}</span>
        </div>
        <div class="mt-1 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-3">
            <p class="text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $texte }}</p>
        </div>
    </div>
</div>
