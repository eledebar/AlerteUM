@props(['disabled' => false])

<input 
    @disabled($disabled) 
    {{ $attributes->merge([
        'class' => '
            border-gray-300 dark:border-gray-700 
            focus:border-indigo-500 dark:focus:border-indigo-400 
            focus:ring-indigo-500 dark:focus:ring-indigo-400 
            rounded-md shadow-sm 
            bg-white dark:bg-gray-800 
            text-gray-900 dark:text-white
            placeholder-gray-400 dark:placeholder-gray-500
        ']) 
    }}>
