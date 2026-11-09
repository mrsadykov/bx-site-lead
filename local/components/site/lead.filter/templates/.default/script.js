BX.ready(function () {
    const node = document.getElementById('site-lead-filter');
    if (!node || !BX.Vue3 || !BX.Vue3.BitrixVue) {
        return;
    }

    BX.Vue3.BitrixVue.createApp({
        data() {
            return {
                kind: 'flat',
                rows: [],
                loading: false,
                seq: 0,
                controller: null,
                url: node.dataset.url || window.location.href,
            };
        },
        methods: {
            apply() {
                if (this.controller) {
                    this.controller.abort();
                }
                this.controller = new AbortController();
                const mySeq = ++this.seq;
                this.loading = true;

                const body = new URLSearchParams();
                body.set('action', 'filter');
                body.set('kind', this.kind);
                body.set('sessid', BX.bitrix_sessid());

                fetch(this.url, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: body.toString(),
                    signal: this.controller.signal,
                })
                    .then((response) => response.json())
                    .then((payload) => {
                        if (mySeq !== this.seq) {
                            return;
                        }
                        this.rows = Array.isArray(payload.rows) ? payload.rows : [];
                    })
                    .catch((error) => {
                        if (error.name === 'AbortError') {
                            return;
                        }
                        throw error;
                    })
                    .finally(() => {
                        if (mySeq === this.seq) {
                            this.loading = false;
                        }
                    });
            },
        },
    }).mount(node);
});
