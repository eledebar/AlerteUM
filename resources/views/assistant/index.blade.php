<x-app-layout>
    <x-slot name="header">
        <h2 id="chat-title" class="text-xl font-semibold text-gray-900 dark:text-gray-100 leading-tight flex items-center gap-2">
            <img src="{{ asset('bot2.webp') }}" alt="Assistant" class="h-7 w-7 rounded-md object-cover">
            Assistant
        </h2>
    </x-slot>

    <div x-data="assistantChat()" x-init="init()" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="relative">
            <div class="absolute inset-0 -z-10 bg-gradient-to-br from-indigo-600/15 via-sky-500/10 to-cyan-400/10"></div>

            <div class="rounded-3xl border border-white/10 bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl shadow-2xl overflow-hidden">
                <div class="flex items-center justify-between px-4 sm:px-6 py-4 border-b border-white/10">
                    <div class="flex items-center gap-2 text-sm text-gray-900 dark:text-gray-100">
                        <span class="inline-flex w-8 h-8 rounded-md overflow-hidden ring-1 ring-white/30 shadow">
                            <img src="{{ asset('bot3.webp') }}" alt="Bot" class="h-full w-full object-cover">
                        </span>
                        <span><strong>AlerteBot</strong> — prêt à vous aider</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="clearChat()" aria-controls="chat" class="text-xs px-3 py-1.5 rounded-full bg-white/90 dark:bg-gray-800/90 border border-white/20 hover:bg-white dark:hover:bg-gray-700 focus-visible:focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:ring-2 focus-visible:ring-indigo-600">
                            Effacer
                        </button>
                    </div>
                </div>

                <div id="chat" role="log" aria-live="polite" aria-relevant="additions text" aria-labelledby="chat-title" class="space-y-4 max-h-[70vh] overflow-y-auto p-4 sm:p-6 pr-2"></div>

                <div class="px-4 sm:px-6 pb-2">
                    <div class="flex flex-wrap gap-2" id="suggestions" role="group" aria-label="Suggestions"></div>
                </div>

                <div class="border-t border-white/10 bg-gradient-to-t from-white/80 dark:from-gray-900/80 to-transparent px-4 sm:px-6 py-4">
                    <form @submit.prevent="send()" class="flex gap-2 items-end" aria-labelledby="chat-title">
                        <div class="flex-1">
                            <label for="msg" class="sr-only">Message</label>
                            <textarea x-model="input"
                                      @keydown.enter.prevent="send()"
                                      @input="autoGrow($event)"
                                      placeholder="Explique ton problème en langage naturel…"
                                      class="w-full min-h-[44px] rounded-2xl border border-white/30 bg-white/90 dark:bg-gray-800/90 shadow-inner px-4 py-3 text-gray-900 dark:text-gray-100 placeholder:text-gray-600 dark:placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600"
                                      id="msg" autocomplete="off" rows="1"></textarea>
                        </div>
                        <button :disabled="loading" :aria-disabled="loading ? 'true' : 'false'" class="px-4 py-2 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm disabled:opacity-50 focus-visible:focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:ring-2 focus-visible:ring-indigo-600">
                            <span x-show="!loading">Envoyer</span>
                            <span x-show="loading">…</span>
                        </button>
                    </form>
                    <p class="mt-2 text-xs text-gray-700 dark:text-gray-300">
                        Commandes disponibles : /help, /csv, /notifs, /liste, /rechercher, /etat, /contact
                    </p>
                    <p class="text-[11px] text-gray-600 dark:text-gray-400">IA locale (sans API). Les données restent sur ce serveur.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    function assistantChat() {
        return {
            input: '',
            loading: false,
            streamTimer: null,

            init() {
                const chat = document.getElementById('chat');
                if (chat.childElementCount === 0) {
                    this.pushBot(
                        "Décrivez votre problème librement. Je comprends le texte et j’en déduis l’action à mener (ouvrir via le catalogue, vérifier l’état, recherches, notifications, export CSV). Tapez /help ou /contact si besoin."
                    );
                    this.setSuggestions([
                        { label: 'Ouvrir le catalogue', href: '{{ route('utilisateur.incidents.categories') }}' },
                        { label: 'Mes incidents', href: '{{ route('utilisateur.incidents.index') }}' },
                        { label: 'Vérifier un incident', command: '/etat ', auto: false },
                        { label: 'Rechercher…', command: '/rechercher ', auto: false },
                        { label: 'Notifications', command: '/notifs', auto: true },
                        { label: 'Exporter CSV', command: '/csv', auto: true },
                        { label: 'Contact du support', command: '/contact', auto: true }
                    ]);
                }
            },

            setSuggestions(list) {
                const wrap = document.getElementById('suggestions');
                wrap.innerHTML = '';
                (list || []).forEach(item => {
                    const a = (typeof item === 'string') ? { label: item, command: item } : item;
                    const isLink = !!a.href;
                    const el = document.createElement('button');
                    el.type = 'button';
                    el.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs bg-white/90 dark:bg-gray-800/90 text-gray-900 dark:text-gray-100 border border-white/20 hover:bg-white dark:hover:bg-gray-700 transition shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-600';
                    el.textContent = a.label || 'Action';
                    el.addEventListener('click', () => {
                        if (isLink) {
                            const url = a.href;
                            if (/^https?:\/\//i.test(url)) window.open(url, '_blank', 'noopener');
                            else window.location.href = url;
                        } else {
                            this.input = a.command || '';
                            if (a.auto) { this.send(); }
                            else {
                                const inp = document.getElementById('msg');
                                inp.focus();
                                const v = inp.value; inp.value = ''; inp.value = v;
                            }
                        }
                    });
                    wrap.appendChild(el);
                });
            },

            clearChat() {
                document.getElementById('chat').innerHTML = '';
                this.init();
            },

            autoGrow(e) {
                const el = e.target;
                el.style.height = 'auto';
                el.style.height = el.scrollHeight + 'px';
            },

            scrollBottom() {
                const el = document.getElementById('chat');
                el.scrollTop = el.scrollHeight;
            },

            pushUser(text) {
                const cont = document.getElementById('chat');
                const box = document.createElement('div');
                box.className = 'flex justify-end';
                box.setAttribute('role', 'article');
                box.setAttribute('aria-label', "Message de l’utilisateur");
                box.innerHTML = `
                    <div class="max-w-[85%] sm:max-w-[70%]">
                        <div class="flex items-start gap-3">
                            <div class="rounded-2xl bg-indigo-600 text-white border border-white/20 shadow-lg px-4 py-3 leading-relaxed">
                                <div>${this.escape(text).replace(/\n/g,'<br>')}</div>
                            </div>
                            <div class="shrink-0 h-9 w-9 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-900 dark:text-gray-100 font-bold" aria-hidden="true">
                                {{ mb_strtoupper(mb_substr(auth()->user()->name,0,1)) }}
                            </div>
                        </div>
                    </div>`;
                cont.appendChild(box);
                this.scrollBottom();
            },

            pushTyping() {
                const cont = document.getElementById('chat');
                const box = document.createElement('div');
                box.className = 'flex justify-start';
                box.setAttribute('role', 'status');
                box.setAttribute('aria-live', 'polite');
                box.innerHTML = `
                    <div class="max-w-[85%] sm:max-w-[70%]">
                        <div class="flex items-start gap-3">
                            <span class="inline-flex w-9 h-9 rounded-full overflow-hidden ring-1 ring-white/30 shadow shrink-0">
                                <img src="{{ asset('bot.webp') }}" alt="Bot" class="h-full w-full object-cover">
                            </span>
                            <div class="rounded-2xl bg-white/90 dark:bg-gray-800/90 text-gray-900 dark:text-gray-100 border border-white/20 shadow-lg px-4 py-3">
                                <span class="inline-flex gap-1">
                                    <span class="w-2 h-2 bg-gray-600 rounded-full animate-bounce motion-reduce:animate-none [animation-delay:-0.2s]"></span>
                                    <span class="w-2 h-2 bg-gray-600 rounded-full animate-bounce motion-reduce:animate-none"></span>
                                    <span class="w-2 h-2 bg-gray-600 rounded-full animate-bounce motion-reduce:animate-none [animation-delay:0.2s]"></span>
                                </span>
                            </div>
                        </div>
                    </div>`;
                cont.appendChild(box);
                this.scrollBottom();
                return box;
            },

            pushBot(text, actions = []) {
                const cont = document.getElementById('chat');
                const box = document.createElement('div');
                box.className = 'flex justify-start';
                box.setAttribute('role', 'article');
                box.setAttribute('aria-label', 'Message du bot');
                box.innerHTML = `
                    <div class="max-w-[85%] sm:max-w-[70%]">
                        <div class="flex items-start gap-3">
                            <span class="inline-flex w-9 h-9 rounded-full overflow-hidden ring-1 ring-white/30 shadow shrink-0">
                                <img src="{{ asset('bot.webp') }}" alt="Bot" class="h-full w-full object-cover">
                            </span>
                            <div class="rounded-2xl bg-white/90 dark:bg-gray-800/90 text-gray-900 dark:text-gray-100 border border-white/20 shadow-lg px-4 py-3 leading-relaxed">
                                <div class="sr-only" role="status" aria-live="polite" aria-atomic="true" data-live></div>
                                <div class="prose prose-sm dark:prose-invert max-w-none" aria-hidden="true" data-content></div>
                                <div class="mt-2 flex flex-wrap gap-2" data-actions></div>
                            </div>
                        </div>
                    </div>
                `;
                cont.appendChild(box);

                const plain = this.escape(text).replace(/\n/g,'<br>');
                const live = box.querySelector('[data-live]');
                live.innerHTML = plain;

                const target = box.querySelector('[data-content]');
                let i = 0;
                clearInterval(this.streamTimer);
                this.streamTimer = setInterval(() => {
                    target.innerHTML = plain.slice(0, i++);
                    if (i > plain.length) clearInterval(this.streamTimer);
                    this.scrollBottom();
                }, 8);

                const holder = box.querySelector('[data-actions]');
                (actions || []).forEach(a => {
                    const el = document.createElement('button');
                    el.type = 'button';
                    el.className = 'text-xs px-3 py-1.5 rounded-full border transition bg-white/90 dark:bg-gray-800/90 text-gray-900 dark:text-gray-100 border-white/20 hover:bg-white dark:hover:bg-gray-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-600';
                    el.textContent = a.label || 'Action';
                    el.addEventListener('click', () => {
                        if (a.command) {
                            this.input = a.command;
                            a.auto ? this.send() : document.getElementById('msg').focus();
                        } else if (a.href) {
                            const url = a.href;
                            if (/^https?:\/\//i.test(url)) window.open(url, '_blank', 'noopener');
                            else window.location.href = url;
                        }
                    });
                    holder.appendChild(el);
                });

                this.scrollBottom();
            },

            async send() {
                const msg = this.input.trim();
                if (!msg || this.loading) return;
                this.pushUser(msg);
                this.input = '';
                this.loading = true;
                const typing = this.pushTyping();

                try {
                    const res = await fetch("{{ route('utilisateur.assistant.message') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ message: msg })
                    });
                    const data = await res.json();
                    typing.remove();
                    this.pushBot(data.answer || "Je n’ai pas de réponse.", data.actions || []);
                    this.setSuggestions((data.suggestions || []).map(s => typeof s === 'string' ? {label:s, command:s} : s));
                } catch (e) {
                    typing.remove();
                    this.pushBot("Désolé, une erreur est survenue.");
                } finally {
                    this.loading = false;
                }
            },

            escape(s) {
                return (s || '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
            }
        }
    }
    </script>
</x-app-layout>
