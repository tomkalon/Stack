import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['fields'];

    connect() {
        this.fetchUrl = null;
        this.submitUrl = null;
        this.rowId = null;

        this.onRowClick = (event) => {
            const row = event.target.closest('[data-quick-edit-row]');
            if (!row) return;

            this.fetchUrl = row.dataset.quickEditFetchUrl;
            this.submitUrl = row.dataset.quickEditSubmitUrl;
            this.rowId = row.dataset.quickEditRow;

            this.load(this.fetchUrl);
            window.bootstrap.Offcanvas.getOrCreateInstance(this.element).show();
        };

        document.addEventListener('click', this.onRowClick);
    }

    disconnect() {
        document.removeEventListener('click', this.onRowClick);
    }

    async load(url) {
        const response = await fetch(url);
        this.fieldsTarget.innerHTML = await response.text();
    }

    async submit(event) {
        event.preventDefault();

        const form = event.target;
        const response = await fetch(this.submitUrl, {
            method: 'PATCH',
            body: new FormData(form),
            redirect: 'manual',
        });

        if (response.status === 422) {
            const errorsResponse = await fetch(this.fetchUrl, {
                method: 'PATCH',
                body: new FormData(form),
            });
            this.fieldsTarget.innerHTML = await errorsResponse.text();

            return;
        }

        window.bootstrap.Offcanvas.getOrCreateInstance(this.element).hide();
        await this.refreshRow();
    }

    async refreshRow() {
        const currentRow = document.querySelector(`[data-quick-edit-row="${this.rowId}"]`);
        if (!currentRow) return;

        const response = await fetch(window.location.href);
        const html = await response.text();
        const freshRow = new DOMParser()
            .parseFromString(html, 'text/html')
            .querySelector(`[data-quick-edit-row="${this.rowId}"]`);

        if (freshRow) {
            currentRow.outerHTML = freshRow.outerHTML;
        }
    }
}
