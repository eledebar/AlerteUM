<x-app-layout>
    <div class="bg-gray-50 dark:bg-gray-900 py-20">
        <div class="max-w-6xl mx-auto px-6 space-y-28">

            <!-- HERO -->
            <section class="flex flex-col-reverse md:flex-row items-center justify-between gap-10 md:gap-16">
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white leading-tight">
                        AlerteUM<br>
                        <span class="text-indigo-600 dark:text-indigo-400">Signalez. Suivez. Résolvez.</span>
                    </h1>
                    <p class="mt-4 text-base text-gray-600 dark:text-gray-300 max-w-xl mx-auto md:mx-0">
                        La plateforme officielle de l’Université de Mbujimayi pour déclarer les incidents informatiques — rapidement, efficacement et en toute transparence.
                    </p>
                    <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a href="{{ url('/utilisateur/incidents/categories') }}"
                           class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-lg shadow text-center">
                            Signaler un incident
                        </a>
                        <a href="{{ route('utilisateur.incidents.index') }}"
                           class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold px-6 py-3 rounded-lg shadow text-center">
                            Voir mes incidents
                        </a>
                    </div>
                </div>
                <div class="flex-shrink-0 flex justify-center md:justify-end w-full md:w-auto">
                    <img src="{{ asset('logo-um.webp') }}" alt="Logo UM" class="w-[160px] md:w-[200px] h-auto">
                </div>
            </section>

            <!-- ÉTAPES -->
            <section class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl px-10 py-14 grid md:grid-cols-2 items-center gap-12">
                <div class="flex justify-center">
                    <img src="{{ asset('steps.webp') }}"
                         alt="Étapes illustration"
                         class="w-full max-w-[300px] md:max-w-[340px]">
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                        Comment signaler un incident ?
                    </h2>
                    <ul class="space-y-5 text-gray-700 dark:text-gray-300 text-base leading-relaxed list-disc pl-5">
                        <li>Choisissez la <strong>catégorie</strong> concernée.</li>
                        <li>Sélectionnez le <strong>type d’incident</strong> spécifique.</li>
                        <li>Ajoutez un <strong>titre clair</strong> et une <strong>description précise</strong>.</li>
                        <li>Soumettez et <strong>suivez le traitement</strong> en ligne.</li>
                    </ul>
                </div>
            </section>

            <!-- CONTACT -->
            <section class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl px-10 py-14 grid md:grid-cols-2 items-start gap-12">
                <div class="space-y-5 text-sm text-gray-700 dark:text-gray-300">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        Contact Université de Mbujimayi
                    </h2>
                    <p>
                        📍 <strong>Rectorat :</strong><br>
                        Rotonda de Ndebo, Baudine III MIBA<br>
                        Mbuji-Mayi, RDC
                    </p>
                    <p>
                        🏫 <strong>Campus Tshikama :</strong><br>
                        Universidad Q., Dibindi C., Mbuji-Mayi
                    </p>
                    <p>
                        📞 <a href="tel:+243854524647" class="text-indigo-600 dark:text-indigo-400 hover:underline">+243 854 524 647</a><br>
                        ✉️ <a href="mailto:recteur@um.ac.cd" class="text-indigo-600 dark:text-indigo-400 hover:underline">recteur@um.ac.cd</a><br>
                        🕘 <strong>8h30 – 16h00</strong>
                    </p>
                </div>
                <div class="w-full">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3971.6433849396677!2d23.58937261405799!3d-6.138379495547652!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1a372c4f55a04b4f%3A0xf9f6e988d183af69!2sUniversit%C3%A9%20Officielle%20de%20Mbuji-Mayi!5e0!3m2!1sfr!2scd!4v1692286800340!5m2!1sfr!2scd"
                        width="100%" height="280" style="border:0; border-radius: 0.75rem;"
                        allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </section>

        </div>
    </div>
</x-app-layout>
