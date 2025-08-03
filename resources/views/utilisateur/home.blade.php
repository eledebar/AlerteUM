<x-app-layout>
    <div class="bg-white dark:bg-gray-900 pt-10 pb-20">
        <div class="max-w-6xl mx-auto px-6 space-y-16">

            <section class="flex flex-col-reverse md:flex-row items-center justify-between gap-10 md:gap-16">
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white leading-tight">
                        AlerteUM<br>
                        <span class="text-indigo-600 dark:text-indigo-400">Signalez. Suivez. Résolvez.</span>
                    </h1>
                    <p class="mt-4 text-base text-gray-700 dark:text-gray-300 max-w-xl mx-auto md:mx-0">
                        La plateforme officielle de l’Université de Mbujimayi pour déclarer les incidents informatiques — rapidement, efficacement et en toute transparence.
                    </p>
                    <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a href="{{ url('/utilisateur/incidents/categories') }}"
                           class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-lg shadow text-center">
                            Signaler un incident
                        </a>
                        <a href="{{ route('utilisateur.incidents.index') }}"
                           class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-200 font-semibold px-6 py-3 rounded-lg shadow text-center">
                            Voir mes incidents
                        </a>
                    </div>
                </div>
                <div class="flex-shrink-0 flex justify-center md:justify-end w-full md:w-auto">
                    <img src="{{ asset('logo-um.webp') }}" alt="Logo UM" class="w-[160px] md:w-[200px] h-auto">
                </div>
            </section>

            <section class="bg-gray-100 dark:bg-gray-800 rounded-2xl p-8 shadow-xl">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 text-center">Comment signaler un incident ?</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-md p-6 text-center">
                        <img src="{{ asset('step1.webp') }}" alt="Step 1" class="mx-auto w-20 h-20 mb-4">
                        <p class="text-sm text-gray-800 dark:text-gray-200">
                            <strong>Choisissez</strong> la catégorie concernée.
                        </p>
                    </div>
                    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-md p-6 text-center">
                        <img src="{{ asset('step2.webp') }}" alt="Step 2" class="mx-auto w-20 h-20 mb-4">
                        <p class="text-sm text-gray-800 dark:text-gray-200">
                            <strong>Sélectionnez</strong> le type d’incident spécifique.
                        </p>
                    </div>
                    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-md p-6 text-center">
                        <img src="{{ asset('step3.webp') }}" alt="Step 3" class="mx-auto w-20 h-20 mb-4">
                        <p class="text-sm text-gray-800 dark:text-gray-200">
                            <strong>Ajoutez</strong> un titre clair et une description précise.
                        </p>
                    </div>
                    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-md p-6 text-center">
                        <img src="{{ asset('step4.webp') }}" alt="Step 4" class="mx-auto w-20 h-20 mb-4">
                        <p class="text-sm text-gray-800 dark:text-gray-200">
                            <strong>Soumettez</strong> et suivez le traitement en ligne.
                        </p>
                    </div>
                </div>
            </section>

        </div>
    </div>

    <footer class="bg-gray-100 dark:bg-gray-800">
        <div class="max-w-6xl mx-auto px-6 py-12 grid md:grid-cols-2 gap-12">
            <div class="space-y-5 text-sm text-gray-800 dark:text-gray-300">
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
        </div>
    </footer>
</x-app-layout>
